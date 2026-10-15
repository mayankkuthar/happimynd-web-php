# HR Auto: Postgres (Neon) → MySQL migration guide

**For:** the backend lead moving the HR Auto (Resume Extractor) database to MySQL on AWS and porting the API's data layer.
**Package contents** (all in `docs/mysql-migration/` unless noted):

| File                                                  | What it is                                                                   |
| ----------------------------------------------------- | ---------------------------------------------------------------------------- |
| `MIGRATION_GUIDE.md`                                  | This guide: what's in the database, how to move it, what changes in the code |
| `schema.mysql.sql`                                    | MySQL schema: the `records` table, a readable view, the migration ledger. Safe to run more than once. |
| `API_SPEC.md`                                         | Contract for every endpoint the web app calls (request, response, rules)     |

**What was tested (on MySQL 8.4.9, with the §8.1 settings):**

- The schema loads, and loads again without errors.
- The export script, run end to end, produced a file that loaded with every value intact: emoji, accented names, quotes, backslashes, newlines, tabs, `\x1a`, JSON with escaped text, a 200 KB resume text, millisecond UTC timestamps, and a `duplicate_of` pointing at a later row. A row exported mid-`processing` came back as `queued`.
- Generated columns: JSON `null`, `''` and missing keys all give `NULL`; values over 64 KB are accepted; the columns reject direct `UPDATE`s; email lookup ignores case and uses its index. The view joins list fields, and shows a non-array value as text, as Postgres does.
- Every MySQL query form in §5, run through `mysql2` with the §6.1 pool settings: `JSON_SET` keeps `null`s, `JSON_MERGE_PRESERVE` appends to the log, `IN (?)` expands arrays (and `IN ()` errors, so guard it), two parallel claims get different rows with `SKIP LOCKED`, an open claim doesn't block an upload insert at READ COMMITTED, the reaper's `INTERVAL ? MICROSECOND`, deleting an original together with its duplicates in one statement, and `GET_LOCK` for migrations.
- **Not tested:** the Neon side of the export (the test used stand-in rows shaped like the export's `SELECT`), and the API itself on MySQL, since the data layer is not ported yet. The rehearsal in §4.6 covers both.

**Decision for this project: fresh start, no data migration.** The Neon data stays on Neon and is not copied to AWS. MySQL on RDS starts empty, and the API creates the schema on first boot (`DATABASE_AUTO_MIGRATE`). Skip §4.2–4.4 and the export script. The data layer (`server/src/db/`) is already ported to MySQL as described in §5 and §6, and was run end to end against an empty MySQL 8.4.9: schema created on boot, then sign-in, upload with duplicate detection, the background queue, edit, viewed, Excel export, logs, reprocess and delete all worked through the HTTP API. The Postgres migrations are kept in `server/src/db/migrations-postgres/` for rollback only; they are not run.

---

## 1. Current setup

|              | Today                                                                                          |
| ------------ | ---------------------------------------------------------------------------------------------- |
| Database     | PostgreSQL on Neon (`pg` driver, pooled connection), region `us-east-2`                        |
| API          | Node 20+, Express 5, TypeScript (`server/`), deployed on Render (`render.yaml`)                |
| Web app      | React 19 + Vite (`frontend/`), deployed on Vercel; `/api/*` is rewritten to the Render API     |
| SQL location | Every statement is in `server/src/db/records.repo.ts`, plus `SELECT 1` in `meta.routes.ts`     |
| Schema       | `server/src/db/migrations/001–006.sql`, applied on boot by `server/src/db/migrate.ts`          |
| Connection   | `DATABASE_URL`, `DATABASE_SSL`, `DATABASE_POOL_MAX`, `DATABASE_AUTO_MIGRATE`                   |
| Files        | Uploaded resumes and Excel exports in Google Cloud Storage; the DB stores each file's key only |

The web app only talks to the database through the HTTP endpoints in `API_SPEC.md`. If the API keeps those endpoints and response shapes, **the front end needs no changes.** The whole migration lives in `server/src/db/`.

---

## 2. Tables

| Table               | Purpose                                                         | Rows grow with                | Migrate data?                                      |
| ------------------- | --------------------------------------------------------------- | ----------------------------- | -------------------------------------------------- |
| `records`           | One row per uploaded resume: file info, extracted fields, status | Every uploaded file          | **Yes**                                            |
| `schema_migrations` | Which migration files have run                                  | Each deploy with a new migration | **No.** The MySQL schema replaces all six Postgres migrations (§6.4). |

There are no user or session tables. Sign-in is a single admin account from env vars (`ADMIN_USERNAME`, `ADMIN_PASSWORD`, `SESSION_SECRET`); nothing about it is stored in the database.

**`records` is also the work queue.** Upload inserts a `queued` row; a background loop in every API instance claims the oldest one, extracts it, and writes `done` or `error`. A sweep requeues rows stuck in `processing` after a crash. That queue is the one part of the code that needs real care in MySQL (§5.1).

**The generated candidate columns** (`candidate_name`, `email`, `phone`, …, from migrations 004 and 006) are computed from the `fields` JSON by the database. The app never reads or writes them; they exist for people browsing the table. MySQL keeps them as `STORED` generated columns, except the three list fields (`technical_skills`, `project_names`, `certification_names`), which MySQL can't express in a generated column. Those are in the `records_candidates` view instead (§3).

---

## 3. Type mapping (Postgres → MySQL)

| Postgres                                     | MySQL                                          | Notes                                                                                                         |
| -------------------------------------------- | ---------------------------------------------- | ------------------------------------------------------------------------------------------------------------- |
| `TEXT PRIMARY KEY` (`id`, `duplicate_of`)    | `VARCHAR(64) CHARACTER SET ascii COLLATE ascii_bin` | IDs are 12 hex characters generated by the API. Binary collation keeps comparisons exact.                |
| `TEXT` (`checksum`)                          | `CHAR(64)` ascii, binary collation             | SHA-256 hex                                                                                                   |
| `TEXT` (`file_name`, `storage_key`, `content_type`, `source`, `department`, `status`, `ai_status`) | `VARCHAR(n)` | MySQL can't index `TEXT` without a prefix length; lengths are in the schema                                   |
| `TEXT` (`text_content`)                      | `MEDIUMTEXT`                                   | Full extracted resume text. `TEXT` stops at 64 KB, which a long or OCR'd resume can exceed.                   |
| `JSONB` (`fields`, `confidence`, `log`)      | `JSON`                                         | Defaults are `(JSON_OBJECT())` / `(JSON_ARRAY())`; expression defaults need MySQL 8.0.13+                     |
| `BOOLEAN` (`viewed`, `used_ocr`)             | `TINYINT(1)`                                   | **The driver returns `0`/`1`, not `true`/`false`.** See §6.2.                                                 |
| `TIMESTAMPTZ`                                | `DATETIME(3)` holding **UTC**                  | The export converts to UTC. Run MySQL with `time_zone = '+00:00'`.                                            |
| `BIGINT` / `INTEGER`                         | `BIGINT` / `INT`                               |                                                                                                               |
| `CHECK` constraints                          | `CHECK` constraints                            | Enforced from MySQL 8.0.16. The department check is case-insensitive under the default collation; the API validates exact spelling first. |
| Partial indexes (`WHERE status = 'queued'`, `WHERE email IS NOT NULL`, …) | Plain composite indexes | MySQL has no partial indexes. `(status, created_at)` serves the queue; `(status, claimed_at)` the reaper. |
| `lower(email)` / `lower(candidate_name)` indexes | Plain keys under `utf8mb4_0900_ai_ci`      | That collation ignores case (and accents), so lookups need no `LOWER()`.                                      |
| `GENERATED ALWAYS AS (NULLIF(fields ->> 'x', '')) STORED` | `GENERATED ALWAYS AS (IF(JSON_TYPE(…) = 'NULL', NULL, NULLIF(JSON_UNQUOTE(JSON_EXTRACT(fields, '$.x')), ''))) STORED` | MySQL turns a JSON `null` into the text `'null'` without the `JSON_TYPE` check. |
| `jsonb_text_list()` function + generated columns | `records_candidates` view (`JSON_TABLE` + `GROUP_CONCAT`) | MySQL forbids subqueries and stored functions in generated columns. Set `group_concat_max_len` (§8.1). |

Use **utf8mb4** everywhere. Resume text, candidate names and file names contain non-ASCII characters and emoji.

---

## 4. Migration steps

### 4.1 Prepare MySQL

1. Create the database with utf8mb4:
   ```sql
   CREATE DATABASE hr_auto CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
   ```
2. Use the parameter group in §8.1 (`time_zone` UTC, `transaction_isolation` `READ-COMMITTED`, `group_concat_max_len`).
3. Load the schema:
   ```bash
   mysql --default-character-set=utf8mb4 -h <host> -u <user> -p hr_auto < docs/mysql-migration/schema.mysql.sql
   ```

### 4.2 Export from Neon

> **Not used for this project** (fresh start, no data migration). §4.2–4.4 are kept for reference only, and the export script they describe is not included in the repository.

The export only runs `SELECT`s, inside one read-only snapshot transaction. Run it from `server/` after `npm install` (it uses the `pg` package the API already depends on):

```bash
cd server
DATABASE_URL='postgres://…neon…' node scripts/export-postgres-to-mysql.mjs > hr-auto-data.mysql.sql
# stderr shows row counts, e.g.
# Exported rows: {"records":1840,"requeued":0,"bySource":{"linkedin":912,"naukri":604,...},"byStatus":{"done":1822,"error":18}}
```

On Windows PowerShell set the variable first: `$env:DATABASE_URL='postgres://…'; node scripts/export-postgres-to-mysql.mjs > hr-auto-data.mysql.sql`. Use the **direct** (non-pooler) Neon host if the pooled one rejects the long read.

**What the export does:**

- Refuses to run unless all six Postgres migrations have been applied, so the column set is known.
- Exports the base columns only. MySQL recomputes the generated candidate columns on insert.
- Converts every timestamp to UTC `DATETIME(3)` text, formatted by Postgres so the machine's time zone can't leak in.
- Writes `fields`, `confidence` and `log` as JSON text, which MySQL parses on insert.
- Escapes strings for MySQL's default `sql_mode` (backslash escapes).
- Writes multi-row `INSERT`s capped at 100 rows **or** 4 MB, whichever comes first, so a batch of long resumes can't exceed `max_allowed_packet`.
- Turns foreign-key checks off during the load (`duplicate_of` can point at a row later in the file) and wraps the load in one transaction.
- Rows that were `processing` at export time are written as `queued` with `claimed_at` cleared, and their attempt refunded. The new API processes them again. stderr reports how many.

**Treat `hr-auto-data.mysql.sql` as sensitive:** it contains candidate names, emails, phone numbers and full resume text. Delete it after loading.

### 4.3 Load into MySQL

Load into an **empty** `records` table:

```bash
mysql --default-character-set=utf8mb4 --max-allowed-packet=64M -h <host> -u <user> -p hr_auto < hr-auto-data.mysql.sql
```

If the server runs with `NO_BACKSLASH_ESCAPES` in `sql_mode`, turn it off for this session first. The file uses backslash escapes.

### 4.4 Verify

Row counts should match the numbers the export printed:

```sql
SELECT COUNT(*) FROM records;
SELECT source, COUNT(*) FROM records GROUP BY source;
SELECT status, COUNT(*) FROM records GROUP BY status;
```

Spot checks (compare with the same query on Neon; the Postgres form is in the comment):

```sql
SELECT MIN(created_at), MAX(created_at) FROM records;               -- UTC on both sides
SELECT department, COUNT(*) FROM records GROUP BY department;        -- includes NULL for old rows
SELECT COUNT(*) FROM records WHERE email IS NOT NULL;                -- generated column populated
SELECT COUNT(*) FROM records WHERE duplicate_of IS NOT NULL
  AND duplicate_of NOT IN (SELECT id FROM records);                  -- expect 0 (dangling duplicate links)
SELECT SUM(viewed), SUM(used_ocr) FROM records;                      -- Postgres: SUM(viewed::int), SUM(used_ocr::int)
SELECT SUM(CHAR_LENGTH(text_content)) FROM records;                  -- Postgres: SUM(length(text_content))
SELECT id, candidate_name, technical_skills FROM records_candidates
 ORDER BY created_at DESC LIMIT 5;                                   -- view reads cleanly
```

Then, against the API running on MySQL: sign in, open each source tab, open a resume's detail drawer and preview, edit a field, and download an export. That proves the JSON columns, booleans, timestamps (the "uploaded month" filter) and storage keys made it across.

### 4.5 Cut-over

1. **Pause writes.** Stop uploads and let the queue drain: `GET /api/resumes?source=…` should show `queued: 0` and `processing: 0` for every source. Scale the Render service to zero, or put the Vercel app in maintenance, so nothing new arrives.
2. **Run the final export and load** (§4.2–4.4).
3. **Deploy the API with the MySQL data layer** (§6) and point `DATABASE_URL` at RDS. Nothing changes in the front end.
4. **Keep Neon read-only** for a week as a rollback point. Rollback means redeploying the Postgres build of the API with the old `DATABASE_URL`. Uploads made on MySQL during that week would need a reverse export, so decide early.

### 4.6 Rehearsal (do this first)

Run the whole thing once against a throwaway RDS instance before the real cut-over: load the schema twice (it must succeed both times), load a real export, run §4.4, and run the API's MySQL build against it with a small upload batch. This is the step that proves the schema on your exact engine version.

---

## 5. Query changes (Postgres → MySQL)

Every statement is in `server/src/db/records.repo.ts`.

| Where (current code)                     | Postgres                                                                  | MySQL                                                                                                         |
| ---------------------------------------- | ------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------- |
| Every query                              | `$1, $2…` placeholders                                                    | `?`                                                                                                           |
| `insert`, `updateFields`, `setViewed`, `requeue`, `claimNext` | `… RETURNING <columns>`                      | MySQL has no `RETURNING`. Run the write, then `SELECT` the row by `id` on the **same connection, in the same transaction**. |
| `remove`, `clearSource`                  | `DELETE … RETURNING storage_key`                                          | In one transaction: `SELECT storage_key … FOR UPDATE`, then `DELETE`                                          |
| `markViewed`, `reapStuck`                | `… RETURNING id`, then `rows.length`                                      | Use the result's `affectedRows`                                                                               |
| `listCompleted`, `markViewed`            | `id = ANY($1::text[])`                                                    | `id IN (?)` with an array (mysql2's `query()` expands it). **Guard the empty array**: `IN ()` is a syntax error, so return early when `ids` is empty. |
| `updateFields`                           | `fields = fields \|\| $2::jsonb`                                          | `JSON_SET(fields, '$."k1"', CAST(? AS JSON), '$."k2"', CAST(? AS JSON), …)`, one pair per edited key. Not `JSON_MERGE_PATCH`: it deletes a key whose new value is `null`, where Postgres stores the `null`. Keys are already validated against `FIELD_NAMES`. Same for `confidence`. |
| `markDone`, `markFailed`                 | `log = log \|\| $5::jsonb` (array append)                                 | `log = JSON_MERGE_PRESERVE(log, CAST(? AS JSON))`, which concatenates two arrays                              |
| `requeue`                                | `log = '[]'::jsonb`                                                       | `log = JSON_ARRAY()`                                                                                          |
| `insert`, `markDone`                     | `$n::jsonb`                                                               | `CAST(? AS JSON)` with the `JSON.stringify`'d value                                                           |
| Everywhere                               | `now()`                                                                   | `UTC_TIMESTAMP(3)`. Explicit UTC, independent of the session's time zone.                                     |
| `reapStuck`                              | `claimed_at < now() - $1::interval` with `'600000 milliseconds'`          | `claimed_at < UTC_TIMESTAMP(3) - INTERVAL ? MICROSECOND` with `timeoutMs * 1000`                              |
| `stats`, `queueDepth`                    | `COUNT(*)::text`                                                          | `COUNT(*)`. The code already wraps it in `Number()`.                                                          |
| `claimNext`                              | `UPDATE … WHERE id = (SELECT … FOR UPDATE SKIP LOCKED) RETURNING …`       | A transaction (§5.1). MySQL rejects an `UPDATE` with a subquery on the same table (error 1093).               |
| `migrate.ts`                             | `pg_advisory_xact_lock(key)`                                              | `SELECT GET_LOCK('hr_auto_migrate', 60)` … `SELECT RELEASE_LOCK('hr_auto_migrate')` on the same connection    |
| `migrate.ts`                             | Each migration in a transaction                                           | **MySQL DDL commits implicitly**, so a failed migration can be half-applied. Keep one DDL change per file, and write each one so it can run again. |
| Unchanged                                | `SELECT 1`, `ORDER BY created_at`, `GROUP BY status`, `SELECT … FOR UPDATE`, transactions | Same                                                                                    |

### 5.1 The queue claim

`FOR UPDATE SKIP LOCKED` exists in MySQL 8.0+, so the multi-instance design carries over. Only the statement shape changes:

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

Run it at **READ COMMITTED** (set in the parameter group, §8.1, or per session). Under MySQL's default REPEATABLE READ, the locking read also takes gap locks on the `(status, created_at)` index, which blocks uploads inserting new `queued` rows while a claim is open. Postgres's default is already READ COMMITTED, so this keeps the behaviour the code was written for.

### 5.2 Known race, unchanged by the move

`insert()` looks for an existing row with the same `(source, checksum)` using `SELECT … FOR UPDATE`, then inserts. When no row exists yet, there is nothing to lock, so two simultaneous uploads of the identical file can both be recorded as the original. This is true on Postgres today and stays true on MySQL at READ COMMITTED. It only affects the duplicate flag, never data. If you want it closed, take `GET_LOCK(CONCAT('dup:', source, ':', checksum), 5)` around the check and insert.

---

## 6. App changes after the move

### 6.1 Driver and pool (`server/src/db/pool.ts`)

Replace `pg` with `mysql2/promise`:

```ts
import mysql from "mysql2/promise";

pool = mysql.createPool({
  uri: env.DATABASE_URL,                 // mysql://user:pass@host:3306/hr_auto
  connectionLimit: env.DATABASE_POOL_MAX,
  timezone: "Z",                         // DATETIME ↔ JS Date as UTC. Without it, created_at shifts by the host's offset.
  charset: "utf8mb4",
  ssl: env.DATABASE_SSL ? { ca: rdsCaBundle } : undefined,   // §8.1: require TLS
  enableKeepAlive: true,
});
// Belt and braces alongside the parameter group:
pool.on("connection", (conn) => {
  conn.query("SET time_zone = '+00:00', SESSION transaction_isolation = 'READ-COMMITTED'");
});
```

- `query()` returns `[rows, fields]`. Return `rows` to keep the existing `query<T>()` signature.
- `transaction()` becomes `getConnection()` → `beginTransaction()` → `commit()` / `rollback()` → `release()`.
- JSON columns come back already parsed, as `pg` does today.
- Update `@types/pg` / `pg` in `package.json`, and the "Postgres" wording in error messages (`pool.ts`, `index.ts`, `upload.routes.ts`).

### 6.2 Booleans come back as numbers

`viewed` and `used_ocr` are `TINYINT(1)`, and mysql2 returns `0`/`1`. `toRecord()` passes them straight into the API response, where the front end expects `true`/`false`. Coerce them:

```ts
viewed: Boolean(row.viewed),
usedOcr: Boolean(row.used_ocr),
```

Writing booleans needs no change. MySQL accepts `true`/`false`.

### 6.3 Return shapes stay identical

Every handler returns `toSummary(record)`, so as long as `toRecord()` builds the same `ResumeRecord`, every response in `API_SPEC.md` stays byte-identical. Check in particular:

- `created_at`: epoch **seconds** (`Date.getTime() / 1000`), so it depends on the `timezone: "Z"` setting.
- `elapsed_ms`: `finished_at − created_at` in milliseconds.
- `size_bytes`: a number. mysql2 returns `BIGINT` as a number by default; leave `supportBigNumbers` off.

### 6.4 Migrations folder

The six Postgres files can't run on MySQL. Replace them with one baseline:

1. Copy `schema.mysql.sql` to `server/src/db/migrations/001_mysql_baseline.sql`, and remove the old `001`–`006` files. They stay in git history.
2. In `migrate.ts`, swap the advisory lock for `GET_LOCK` (§5) and run each file with a connection that has `multipleStatements: true`. Use that connection only for migrations.
3. Future migrations: one DDL change per file, written to be re-runnable (§5, `migrate.ts` row).

The baseline is idempotent, so applying it to the database you already loaded in §4.1 is harmless.

### 6.5 Env vars

| Variable                | Change                                                                                     |
| ----------------------- | ------------------------------------------------------------------------------------------ |
| `DATABASE_URL`          | `mysql://user:password@<rds-endpoint>:3306/hr_auto`                                        |
| `DATABASE_SSL`          | Keep `1`. Now means "verify against the RDS CA bundle", not "TLS without verification".    |
| `DATABASE_POOL_MAX`     | Unchanged (10). Keep instances × pool max well under the RDS `max_connections`.            |
| `DATABASE_AUTO_MIGRATE` | Unchanged. Set `0` and run `npm run migrate` from the pipeline if you run several instances. |
| Everything else         | Unchanged. The admin login, session cookie, Bedrock and limits don't touch the database.  |

---

## 7. Things to keep the same

- **UTC everywhere.** `created_at` drives the table's ordering, the "uploaded month" filter (epoch seconds) and `elapsed_ms`.
- **Source isolation.** Every list, count, clear and export query is filtered by `source`. Duplicate detection is scoped to `(source, checksum)`.
- **`DELETE /api/resumes/:id` returns global stats**, across every source. Every other endpoint returns per-source stats. This is deliberate (`API_SPEC.md` §4).
- **`fields` keeps exactly the JSON the app writes.** The front end and Excel export read it directly. Don't normalise it into columns. The generated columns are read-only copies.
- **Department stays the display label** (`"HR & Finance"`, not a slug), and `NULL` for rows that predate departments. Don't backfill it.
- **The queue is the database.** Don't move to SQS as part of this migration. The claim, reaper and retry limit all depend on the row state, and changing both at once makes a regression hard to place.

---

## 8. AWS notes

### 8.1 Engine and parameter group

- **Engine:** **RDS for MySQL 8.0 / 8.4** or **Aurora MySQL 3** (8.0-compatible). The schema needs 8.0.16+ (enforced `CHECK`; expression defaults need 8.0.13).
- **Region:** put the database in the same region as the API. The extraction pipeline makes several queries per resume, and a cross-region round trip on each adds up across a batch.
- **Parameter group** (custom, so it can be changed):

  | Setting                  | Value                | Notes                                                                                    |
  | ------------------------ | -------------------- | ---------------------------------------------------------------------------------------- |
  | `time_zone`              | UTC                  | RDS default; leave it                                                                    |
  | `character_set_server`   | `utf8mb4`            |                                                                                          |
  | `collation_server`       | `utf8mb4_0900_ai_ci` |                                                                                          |
  | `transaction_isolation`  | `READ-COMMITTED`     | Matches Postgres; avoids gap locks on the queue (§5.1)                                    |
  | `group_concat_max_len`   | `1048576`            | Otherwise the `records_candidates` view cuts list fields at 1 KB                          |
  | `max_allowed_packet`     | `67108864` (64 MB)   | Default on 8.0; the export's statements stay under 4 MB, but a large resume row is one packet |
  | `sql_mode`               | Default              | Must not include `NO_BACKSLASH_ESCAPES`; the data file relies on backslash escapes        |
  | `require_secure_transport` | `ON`               | TLS only                                                                                 |

- **Encryption:** turn on encryption at rest, and connect with the RDS CA bundle (`global-bundle.pem`). This table holds candidate PII.

### 8.2 Network and how the API reaches the database

The API runs on **Render** today, outside AWS. RDS should not be on the public internet. Pick one:

| Option                                          | How                                                                                                      | Notes                                                                                                   |
| ----------------------------------------------- | -------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------- |
| **A. API on AWS (recommended)**                 | Run `server/` on **ECS Fargate** or **App Runner** in the RDS VPC. Point the Vercel rewrite (`frontend/vercel.json`) at the new API URL. | RDS stays private. The API is a long-running process with a background worker loop, so a container service fits; **Lambda doesn't** (the queue loop and worker threads need a process that stays up). |
| B. Keep the API on Render                       | RDS with a public endpoint, security group allow-listing Render's static outbound IPs                    | Render's static outbound IPs are a paid feature. Less isolated. TLS is mandatory.                      |

With option A:

- **Health check:** use `/api/health/ready` for the load balancer / App Runner health check, not `/api/health`. Only the former fails when the database is unreachable (`render.yaml` explains why).
- **Cookie:** keep the Vercel rewrite. The browser then sees one origin, so `SESSION_COOKIE_SAMESITE=Lax` keeps working with no CORS or third-party-cookie issues (`API_SPEC.md` §1).
- **Uploads:** the front end sends batches of at most 20 files / 4 MB (`frontend/src/upload/batching.ts`), sized for Vercel's ~4.5 MB proxied-body limit. A single file over 4 MB still goes alone in one request, up to the API's 25 MB `MAX_FILE_BYTES`, so whatever sits in front of the API (ALB, App Runner, nginx) must accept bodies of at least 25 MB.

### 8.3 File storage moves too

The database stores only object keys (`storage_key`, e.g. `resumes/linkedin/a1b2c3d4e5f6.pdf`); the files are in Google Cloud Storage. Moving to AWS usually means moving them to **S3** as well:

- Copy the bucket **with the same keys** (e.g. `gsutil -m rsync -r gs://<bucket> s3://<bucket>` via a machine with both credentials, or S3's managed transfer). Then no `storage_key` needs rewriting.
- Add an S3 driver beside `server/src/storage/gcs.ts`; the `StorageDriver` interface (`save`, `openRead`, `size`, `delete`, `deleteMany`) is the whole contract.
- If the files move at a different time from the database, keep the API on GCS until the copy is done. A row whose key is missing from storage shows "File not found" in the preview, and the processor fails it.

### 8.4 Running the export and load

- **Export** from any machine that can reach Neon (a laptop is fine). The script only reads.
- **Load** from a machine inside the VPC: an EC2 bastion, or AWS Systems Manager port forwarding (`AWS-StartPortForwardingSessionToRemoteHost`) to RDS from a laptop.
- **Moving the data file:** copy it through an encrypted S3 bucket, or `scp` to the bastion. **Delete it from every location after loading.** It contains candidate PII and full resume text.

### 8.5 Secrets and backups

- **Secrets Manager:** store the DB credentials, `ADMIN_PASSWORD`, `SESSION_SECRET` and `BEDROCK_API_KEY`, and inject them as env vars (ECS task definition `secrets`, or App Runner secret references). Rotating `SESSION_SECRET` signs everyone out.
- **Snapshot:** take a manual RDS snapshot right after the load and before cut-over.
- **Backups:** keep automated backups on (7+ days).
- **Neon:** keep it read-only for a week after cut-over as a fallback.
