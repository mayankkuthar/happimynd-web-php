# HR Auto: MySQL → Amazon RDS for MySQL migration guide

**For:** the backend lead moving the HR Auto database (Resume Extractor, candidate portal and hiring dashboard) from the development MySQL server to MySQL on AWS.
**Package contents** (all in `docs/mysql-migration/` unless noted):

| File                 | What it is                                                                   |
| -------------------- | ---------------------------------------------------------------------------- |
| `MIGRATION_GUIDE.md` | This guide: what's in the database, how to move it, how the API uses it      |
| `schema.mysql.sql`   | MySQL schema for all seven tables and the view. Safe to run more than once.  |
| `API_SPEC.md`        | Contract for every endpoint the web app calls (request, response, rules)     |

**What was tested (on MySQL 8.4.7, 27–28 October 2026):**

- `schema.mysql.sql` loads, and loads again without errors. Afterwards the API's own migration runner finds nothing left to apply.
- A database built from `schema.mysql.sql` is **identical** to one built by the API's migrations `001`–`005`: every column (type, default, character set, collation, generated expression), every index, `CHECK` constraint, foreign key, the view definition, the seven seeded departments and the migration ledger were compared from `information_schema`.
- The next two checks, and the API's migrations, ran as a user holding **only** the grants in §4.1, not as root. Loading `schema.mysql.sql` twice as that user also works.
- The data move in §4.3–4.5, end to end: the exact export command, loaded into a fresh schema, then loaded a second time. Row counts for every table and content checksums of `records` and `applications` matched exactly, and the departments came out in the same order.
- The API against the copy: `/api/health/ready`, sign-in, listing and adding departments (and the 409 on a duplicate in different case), the resume list, the applications list, adding a note, changing a status, and the Excel export.
- The applications, notes and audit rows counted in §2 were created through the careers page and the hiring dashboard running on MySQL in development.
- **Not tested:** RDS itself (TLS with the RDS certificate bundle, the parameter group in §8.1, network access). The rehearsal in §4.7 covers these.

**RDS starts empty; no data is moved.** Do §4.1, §4.2, §4.6 and §4.7, and skip §4.3–4.5. The schema comes from the API's first boot against the empty database (`DATABASE_AUTO_MIGRATE=1`) or from loading `schema.mysql.sql` by hand (§4.2). Either way the seven default departments are created. §4.3–4.5 stay in the guide in case data ever has to be copied later.

---

## 1. Current setup

|              | Today                                                                                          |
| ------------ | ---------------------------------------------------------------------------------------------- |
| Database     | MySQL Community Server 8.4.7 on a development machine, database `hr_auto`, utf8mb4, UTC        |
| API          | Node 20+, Express 5, TypeScript (`server/`), `mysql2` driver, deployed on Render (`render.yaml`) |
| Web app      | React 19 + Vite (`frontend/`), deployed on Vercel; `/api/*` is rewritten to the API            |
| SQL location | `server/src/db/records.repo.ts`, `applications.repo.ts`, `departments.repo.ts`, `audit.repo.ts`, plus `SELECT 1` in `meta.routes.ts` |
| Schema       | `server/src/db/migrations/001`–`005.sql`, applied on boot by `server/src/db/migrate.ts`        |
| Connection   | `DATABASE_URL`, `DATABASE_SSL`, `DATABASE_SSL_CA`, `DATABASE_POOL_MAX`, `DATABASE_AUTO_MIGRATE` |
| Files        | Uploaded resumes and Excel exports in Google Cloud Storage; the DB stores each file's key only |

The web app only talks to the database through the HTTP endpoints in `API_SPEC.md`, and the data layer already runs on MySQL. **Moving to RDS changes configuration, not code.**

---

## 2. Tables

| Table                | Purpose                                                          | Rows grow with                     | On RDS at go-live                     |
| -------------------- | ---------------------------------------------------------------- | ---------------------------------- | ------------------------------------- |
| `records`            | One row per uploaded resume: file info, department, extracted fields, status | Every uploaded file, and every portal application | Empty                |
| `applications`       | One row per application from the careers page (`/apply`)         | Every submission                   | Empty                                 |
| `application_notes`  | Internal hiring notes on an application                          | Each note                          | Empty                                 |
| `application_emails` | Every email sent to a candidate, sent or failed                  | Each email                         | Empty                                 |
| `audit_log`          | Append-only trail of admin actions                               | Each status change, note, email, department added | Empty                  |
| `departments`        | Departments a resume can be filed under (7 seeded, admin can add) | Each department the admin adds    | The 7 defaults, seeded by the schema  |
| `schema_migrations`  | Which migration files have run                                   | Each deploy with a new migration   | `001`–`005`, written by the schema (§4.2) |
| `records_candidates` | View: one readable row per resume, for people browsing with SQL  | (view)                             | Created by the schema                 |

No development data is copied. Departments the admin added in development are not carried over; add them again from the upload prompt if they are still needed.

There are no user or session tables. Sign-in is a single admin account from env vars (`ADMIN_USERNAME`, `ADMIN_PASSWORD`, `SESSION_SECRET`); nothing about it is stored in the database.

**`records` is also the work queue.** Upload inserts a `queued` row; a background loop in every API instance claims the oldest one, extracts it, and writes `done` or `error`. A sweep requeues rows stuck in `processing` after a crash (§5.1).

**Portal applications create two rows.** A submission writes an `applications` row and a linked `records` row (`applications.record_id`, source `others`), so the resume also appears in the extractor's "Others" tab. There is no foreign key on that link on purpose: the ID is written before the record exists (`API_SPEC.md` §7).

**The generated candidate columns** on `records` (`candidate_name`, `email`, `phone`, …) are `STORED` generated columns computed from the `fields` JSON by the database. The app never reads or writes them; they exist for people browsing the table. The three list fields (`technical_skills`, `project_names`, `certification_names`) are in the `records_candidates` view instead.

**Personal data:** `records` and `applications` hold candidate names, emails, phone numbers and full resume text. Treat the database, its snapshots and every export file as confidential.

---

## 3. Column types and conventions

| Data                                          | MySQL type                                          | Notes                                                                                          |
| --------------------------------------------- | --------------------------------------------------- | ---------------------------------------------------------------------------------------------- |
| IDs (`records.id`, `applications.id`, links)  | `VARCHAR(64)` / `VARCHAR(32)` `CHARACTER SET ascii COLLATE ascii_bin` | Generated by the API: 12 hex characters for records, `APP-` + 10 for applications. Binary collation keeps comparisons exact. |
| Notes, emails, audit IDs                      | `BIGINT AUTO_INCREMENT`                             |                                                                                                |
| `checksum`                                    | `CHAR(64)` ascii, binary collation                  | SHA-256 hex of the file; duplicate detection is per `(source, checksum)`                        |
| Short text (`file_name`, `source`, `department`, `status`, names, URLs) | `VARCHAR(n)`              | Lengths are in the schema; MySQL can't index `TEXT` without a prefix                            |
| `text_content`, email `body`                  | `MEDIUMTEXT`                                        | A long or OCR'd resume can exceed `TEXT`'s 64 KB                                                |
| `fields`, `confidence`, `log`, `skills`, `detail` | `JSON`                                          | Defaults `(JSON_OBJECT())` / `(JSON_ARRAY())`; expression defaults need 8.0.13+                 |
| Booleans (`viewed`, `used_ocr`)               | `TINYINT(1)`                                        | mysql2 returns `0`/`1`; the repository converts with `Boolean()`                                |
| Timestamps                                    | `DATETIME(3)` holding **UTC**                       | The pool sets `time_zone = '+00:00'`; the API reports epoch seconds                             |
| `experience_years`                            | `DECIMAL(4,1)`                                      | 0.0–60.0                                                                                       |
| Status values                                 | `CHECK` on `records.status` and `application_emails.status` | Enforced from 8.0.16. Application statuses and departments are **not** constrained in SQL: they are configuration / data, and the API validates them. |
| Case-insensitive lookups (email, name, department) | Plain keys under `utf8mb4_0900_ai_ci`          | That collation ignores case (and accents), so lookups need no `LOWER()`                         |

Use **utf8mb4** everywhere. Resume text, candidate names and file names contain non-ASCII characters and emoji.

---

## 4. Migration steps

### 4.1 Prepare RDS

1. Create the custom parameter group in §8.1 and the RDS instance (§8.1–8.2). Wait for **Available** and note the endpoint.
2. As the master user, create the database and the application user:
   ```sql
   CREATE DATABASE hr_auto CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;

   CREATE USER 'hr_auto'@'%' IDENTIFIED BY '<strong password>' REQUIRE SSL;

   GRANT SELECT, INSERT, UPDATE, DELETE,
         CREATE, ALTER, INDEX, DROP, REFERENCES, CREATE VIEW, SHOW VIEW
     ON hr_auto.* TO 'hr_auto'@'%';
   ```
   The DDL privileges are there because the API applies its own migrations (§5.3). They are limited to `hr_auto`.
3. Download the RDS CA bundle (`global-bundle.pem`) for command-line clients. The API already ships with it.

### 4.2 Create the schema

Either one; the result is identical (tested, see top):

- **Let the API do it (recommended).** Point `DATABASE_URL` at RDS and run, from `server/`:
  ```powershell
  $env:DATABASE_URL = "mysql://hr_auto:<password>@<rds-endpoint>:3306/hr_auto"
  $env:DATABASE_SSL = "1"
  npm run migrate
  ```
  Expect `001_mysql_baseline.sql` … `005_drop_department_check.sql` applied, then `database schema up to date`. With `DATABASE_AUTO_MIGRATE=1` the API also does this on its first boot.
- **Load the file by hand:**
  ```bash
  mysql --default-character-set=utf8mb4 --ssl-ca=global-bundle.pem --ssl-mode=VERIFY_IDENTITY \
        -h <rds-endpoint> -u hr_auto -p hr_auto < docs/mysql-migration/schema.mysql.sql
  ```
  The file ends by recording `001`–`005` in `schema_migrations`, so the API won't re-apply them.

### 4.3 Export from the development database

> §4.3–4.5 are not needed for this migration: RDS starts empty. Skip to §4.6.

1. **Stop the API** (and anything else that writes) so nothing arrives during the copy.
2. Export **data only**, since the schema already exists on RDS:
   ```powershell
   mysqldump -u root -h 127.0.0.1 `
     --no-create-info --skip-triggers --single-transaction --skip-add-locks `
     --replace --hex-blob --default-character-set=utf8mb4 `
     --set-gtid-purged=OFF `
     --ignore-table=hr_auto.schema_migrations `
     --result-file=hr_auto_data.sql hr_auto
   ```

**What the flags do:**

- `--no-create-info`: no `CREATE TABLE`s; the view is skipped too.
- `--skip-add-locks`: no `LOCK TABLES` in the file. Without it the load fails as the `hr_auto` user (error 1044), which has no `LOCK TABLES` privilege. The API is stopped during the copy, so the locks add nothing.
- `--replace`: the seven default departments already exist on RDS (migration 004), so the copied rows replace them, keeping the original `created_at` and therefore the same order in the department picker. Every other table is empty, so nothing else is replaced. It also makes the file safe to load twice.
- `--ignore-table=…schema_migrations`: RDS has its own ledger from §4.2.
- Generated columns are left out of the `INSERT`s automatically; MySQL recomputes them on load.
- Foreign-key checks are switched off inside the file, so `duplicate_of` can point at a row later in the file.
- `--result-file`, **not** `> file.sql`: Windows PowerShell 5.1's `>` writes UTF-16, which `mysql` can't load.

**Treat `hr_auto_data.sql` as sensitive:** it contains candidate names, emails, phone numbers and full resume text. Delete it after loading.

### 4.4 Load into RDS

From a machine that can reach RDS (§8.4):

```powershell
mysql -h <rds-endpoint> -u hr_auto -p `
  --ssl-ca=global-bundle.pem --ssl-mode=VERIFY_IDENTITY `
  --default-character-set=utf8mb4 --max-allowed-packet=64M hr_auto `
  -e "source hr_auto_data.sql"
```

If the server runs with `NO_BACKSLASH_ESCAPES` in `sql_mode`, turn it off for this session first. The file uses backslash escapes.

### 4.5 Verify

Run on **both** databases. Every line must match:

```sql
SELECT 'records', COUNT(*) FROM records
UNION ALL SELECT 'applications', COUNT(*) FROM applications
UNION ALL SELECT 'application_notes', COUNT(*) FROM application_notes
UNION ALL SELECT 'application_emails', COUNT(*) FROM application_emails
UNION ALL SELECT 'audit_log', COUNT(*) FROM audit_log
UNION ALL SELECT 'departments', COUNT(*) FROM departments
UNION ALL SELECT 'records checksum',
  SUM(CRC32(CONCAT_WS('|', id, checksum, fields, text_content))) FROM records
UNION ALL SELECT 'applications checksum',
  SUM(CRC32(CONCAT_WS('|', id, email, skills))) FROM applications;
```

Spot checks:

```sql
SELECT source, status, COUNT(*) FROM records GROUP BY source, status;
SELECT department, COUNT(*) FROM records GROUP BY department;          -- NULL for old and portal rows
SELECT COUNT(*) FROM records WHERE email IS NOT NULL;                  -- generated column populated
SELECT COUNT(*) FROM records r WHERE duplicate_of IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM records d WHERE d.id = r.duplicate_of); -- expect 0
SELECT COUNT(*) FROM applications a WHERE record_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM records r WHERE r.id = a.record_id);    -- expect 0 (portal links intact)
SELECT id, candidate_name, technical_skills FROM records_candidates
 ORDER BY created_at DESC LIMIT 5;                                     -- view reads cleanly
```

Then, against the API running on RDS: sign in, open each source tab, preview a resume, edit a field, download an export, open an application and add a note. That proves the JSON columns, booleans, timestamps and storage keys made it across.

### 4.6 Cut-over

1. **Stop the API.** Let the queue drain first (`GET /api/resumes?source=…` shows `queued: 0` and `processing: 0` for every source) so no upload is left half-processed.
2. **Point the API at RDS** (§6.1) and start it. `GET /api/health/ready` must return `{"status":"ready","database":"ok",…}`. Nothing changes in the front end.
3. **Smoke test:** sign in, check the department picker lists the seven defaults, upload one resume and wait for it to finish, submit one application on the careers page, and download an Excel export. Delete the test rows afterwards from the app.
4. **Snapshot RDS** (e.g. `hr-auto-after-migration`).
5. **Rollback** means pointing `DATABASE_URL` back at the development database and restarting. Anything written to RDS in between stays on RDS only.

### 4.7 Rehearsal (do this first)

Run the whole thing once against a throwaway RDS instance: load the schema twice (it must succeed both times), then start the API against it with a small upload batch and one portal application. This is the step that proves TLS, the parameter group and network access on your exact engine version.

---

## 5. How the API uses MySQL

Nothing here needs changing for RDS. It is what to keep in mind when touching the data layer.

### 5.1 The queue claim

Every API instance claims work with `FOR UPDATE SKIP LOCKED`, so several instances can share a batch without handing one file to two workers:

```sql
START TRANSACTION;
SELECT id FROM records
 WHERE status = 'queued'
 ORDER BY created_at
 LIMIT 1
 FOR UPDATE SKIP LOCKED;                 -- no row → COMMIT and return null
UPDATE records
   SET status = 'processing', claimed_at = UTC_TIMESTAMP(3), attempts = attempts + 1
 WHERE id = ?;
SELECT <ALL_COLUMNS> FROM records WHERE id = ?;
COMMIT;
```

It runs at **READ COMMITTED** (the pool sets it per connection; set it in the parameter group too, §8.1). Under MySQL's default REPEATABLE READ, the locking read also takes gap locks on the `(status, created_at)` index, which blocks uploads inserting new `queued` rows while a claim is open.

A reaper (every 60 s and at boot) requeues rows stuck in `processing` longer than `STUCK_JOB_TIMEOUT_MS` (10 min), or fails them after `MAX_PROCESS_ATTEMPTS` (3). See `API_SPEC.md` §9.

### 5.2 JSON edits keep `null`

Field edits from the results table use `JSON_SET(fields, '$."k1"', CAST(? AS JSON), …)`, one pair per edited key. **Not** `JSON_MERGE_PATCH`: it deletes a key whose new value is `null`, and the front end relies on the key staying with a `null` value. The processing log is appended with `JSON_MERGE_PRESERVE(log, CAST(? AS JSON))`.

### 5.3 Migrations

- `server/src/db/migrate.ts` runs every file in `server/src/db/migrations/` in name order, once each, recorded in `schema_migrations`.
- A `GET_LOCK('hr_auto_migrate', 60)` serialises instances booting together.
- **MySQL DDL commits implicitly**, so a failed migration can be half-applied. Keep one DDL change per file, and write each so it can run again (`IF NOT EXISTS`, `CREATE OR REPLACE`, `INSERT IGNORE`).
- Next migration: `006_….sql`. It is applied automatically on the next deploy (or by `npm run migrate` when `DATABASE_AUTO_MIGRATE=0`). Add the same change to `schema.mysql.sql` and its ledger insert so the two stay identical.

### 5.4 Known race

`records.insert()` looks for an existing row with the same `(source, checksum)` using `SELECT … FOR UPDATE`, then inserts. When no row exists yet there is nothing to lock, so two simultaneous uploads of the identical file can both be recorded as the original. It only affects the duplicate flag, never data. To close it, take `GET_LOCK(CONCAT('dup:', source, ':', checksum), 5)` around the check and insert.

---

## 6. App configuration

### 6.1 Env vars

| Variable                | Value on AWS                                                                               |
| ----------------------- | ------------------------------------------------------------------------------------------ |
| `DATABASE_URL`          | `mysql://hr_auto:<password>@<rds-endpoint>:3306/hr_auto`                                   |
| `DATABASE_SSL`          | `1`. Verified TLS against the RDS CA bundle the API ships with.                            |
| `DATABASE_SSL_CA`       | Leave empty. Only for a server whose certificate is not from RDS.                          |
| `DATABASE_POOL_MAX`     | `10`. Keep instances × pool max well under the RDS `max_connections`.                      |
| `DATABASE_AUTO_MIGRATE` | `1` for one instance. With several, set `0` and run `npm run migrate` from the pipeline.  |
| Everything else         | Unchanged. The admin login, session cookie, Bedrock, SMTP, portal and storage settings don't touch the database connection. |

### 6.2 Settings that live in the database vs. in env

- **In the database:** departments (the admin adds them from the upload prompt; there is no rename or delete).
- **In env:** application pipeline stages (`APPLICATION_STATUSES`), open roles on the careers page (`PORTAL_OPEN_ROLES`), duplicate-application rules (`APPLICATION_DUPLICATE_MATCH`, `APPLICATION_DUPLICATE_WINDOW_DAYS`). Renaming a stage does not rewrite existing `applications.status` values.

---

## 7. Things to keep the same

- **UTC everywhere.** `created_at` drives the table ordering, the "uploaded month" filter (epoch seconds) and `elapsed_ms`.
- **Source isolation.** Every list, count, clear and export query is filtered by `source`. Duplicate detection is scoped to `(source, checksum)`.
- **`DELETE /api/resumes/:id` returns global stats**, across every source. Every other endpoint returns per-source stats. This is deliberate (`API_SPEC.md` §4).
- **`fields` keeps exactly the JSON the app writes.** The front end and Excel export read it directly. Don't normalise it into columns. The generated columns are read-only copies.
- **Department stays the display label** (`"HR & Finance"`, not a slug), and `NULL` for rows that predate departments or came from the portal. Don't backfill it.
- **The queue is the database.** Don't move to SQS as part of this migration. The claim, reaper and retry limit all depend on the row state, and changing both at once makes a regression hard to place.
- **The audit log is append-only** and has no foreign keys: it must outlive what it describes.

---

## 8. AWS notes

### 8.1 Engine and parameter group

- **Engine:** **RDS for MySQL 8.4** (same major version as development) or **Aurora MySQL 3**. The schema needs 8.0.19+ (`DROP CHECK` in migration 005; enforced `CHECK` from 8.0.16; expression defaults from 8.0.13).
- **Instance:** `db.t4g.small` is plenty for this data; scale up later without data changes. gp3 storage, 20 GB, autoscaling on. Multi-AZ for production.
- **Region:** the same region as the API. The extraction pipeline makes several queries per resume, and a cross-region round trip on each adds up across a batch.
- **Parameter group** (custom, family `mysql8.4`, so it can be changed):

  | Setting                    | Value                | Notes                                                                                    |
  | -------------------------- | -------------------- | ---------------------------------------------------------------------------------------- |
  | `time_zone`                | UTC                  | RDS default; leave it                                                                    |
  | `character_set_server`     | `utf8mb4`            |                                                                                          |
  | `collation_server`         | `utf8mb4_0900_ai_ci` | Matches the schema; case-insensitive lookups                                             |
  | `transaction_isolation`    | `READ-COMMITTED`     | Avoids gap locks on the queue (§5.1); the pool also sets it per connection               |
  | `group_concat_max_len`     | `1048576`            | Otherwise the `records_candidates` view cuts list fields at 1 KB                          |
  | `max_allowed_packet`       | `67108864` (64 MB)   | A large resume row is one packet                                                         |
  | `sql_mode`                 | Default              | Must not include `NO_BACKSLASH_ESCAPES`; the data file relies on backslash escapes        |
  | `require_secure_transport` | `ON`                 | TLS only                                                                                 |

- **Encryption:** turn on encryption at rest. Connect with the RDS CA bundle. The tables hold candidate PII.

### 8.2 Network and how the API reaches the database

The API runs on **Render** today, outside AWS. RDS should not be on the public internet. Pick one:

| Option                          | How                                                                                                      | Notes                                                                                                   |
| ------------------------------- | -------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------- |
| **A. API on AWS (recommended)** | Run `server/` on **ECS Fargate** or **App Runner** in the RDS VPC. Point the Vercel rewrite (`frontend/vercel.json`) at the new API URL. | RDS stays private. The API is a long-running process with a background worker loop, so a container service fits; **Lambda doesn't**. |
| B. Keep the API on Render       | RDS with a public endpoint, security group allow-listing Render's static outbound IPs                    | Render's static outbound IPs are a paid feature. Less isolated. TLS is mandatory.                      |

With option A:

- **Health check:** use `/api/health/ready`, not `/api/health`. Only the former fails when the database is unreachable.
- **Cookie:** keep the Vercel rewrite. The browser then sees one origin, so `SESSION_COOKIE_SAMESITE=Lax` keeps working with no CORS or third-party-cookie issues (`API_SPEC.md` §1).
- **Uploads:** the front end sends batches of at most 20 files / 4 MB, sized for Vercel's ~4.5 MB proxied-body limit. A single file over 4 MB still goes alone, up to the API's 25 MB `MAX_FILE_BYTES`, so whatever sits in front of the API (ALB, App Runner, nginx) must accept bodies of at least 25 MB.
- **Security group:** port 3306 open only to the API's security group and the admin path in §8.4.

### 8.3 File storage

The database stores only object keys (`storage_key`, `resume_storage_key`, e.g. `resumes/linkedin/a1b2c3d4e5f6.pdf`, `resumes/portal/APP-….pdf`); the files are in Google Cloud Storage. **The database move doesn't require moving files.** To move them to **S3** as well:

- Copy the bucket **with the same keys** (e.g. `gsutil -m rsync -r gs://<bucket> s3://<bucket>` from a machine with both credentials). Then no key needs rewriting.
- Add an S3 driver beside `server/src/storage/gcs.ts`; the `StorageDriver` interface (`save`, `openRead`, `size`, `delete`, `deleteMany`) is the whole contract.
- If the files move at a different time from the database, keep the API on GCS until the copy is done. A row whose key is missing shows "File not found" in the preview, and the processor fails it.

### 8.4 Running the export and load

Only if data is ever copied (§4.3–4.5); not needed for this migration.

- **Export** on the development machine (§4.3). It only reads.
- **Load** from a machine inside the VPC: an EC2 bastion, or AWS Systems Manager port forwarding (`AWS-StartPortForwardingSessionToRemoteHost`) to RDS from a laptop.
- **Moving the data file:** through an encrypted S3 bucket, or `scp` to the bastion. **Delete it from every location after loading.**

### 8.5 Secrets and backups

- **Secrets Manager:** store the DB password, `ADMIN_PASSWORD`, `SESSION_SECRET`, `BEDROCK_API_KEY` and `SMTP_PASSWORD`, and inject them as env vars (ECS task definition `secrets`, or App Runner secret references). Rotating `SESSION_SECRET` signs everyone out.
- **Snapshot:** take a manual RDS snapshot after the smoke test in §4.6.
- **Backups:** automated backups on, 7+ days (point-in-time restore).
- **Monitoring:** CloudWatch alarms on `FreeStorageSpace` (< 20%), `CPUUtilization` (> 80% for 15 min) and `DatabaseConnections` (> 80% of `max_connections`).
