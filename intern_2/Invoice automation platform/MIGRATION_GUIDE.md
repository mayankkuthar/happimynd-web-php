# Invoice Platform: Postgres → MySQL (AWS) migration guide

**For:** whoever moves the Invoice Platform database from Postgres (Railway) to MySQL on AWS.
**Package contents** (all in `docs/mysql-migration/` unless noted):

| File                                                | What it is                                                                                 |
| --------------------------------------------------- | ------------------------------------------------------------------------------------------ |
| `MIGRATION_GUIDE.md`                                | This guide: what's in the database, how to move it, what changes in the code               |
| `schema.mysql.sql`                                  | MySQL schema for all 15 tables, exactly as Prisma generates it. Becomes the first migration |
| `mysql-switch.patch`                                | The code changes in §5, ready for `git apply`                                              |
| `scripts/export-postgres-to-mysql.mjs` (repo root)  | Read-only export of the current data as MySQL `INSERT` statements                          |

**Why there is no `API_SPEC.md`:** every database call in this project goes through Prisma, and Prisma supports MySQL. The API and worker keep running as they are, with the small changes in §5. No endpoint is rebuilt, and the web app doesn't change.

**What was tested:**

- `schema.mysql.sql` is Prisma 5.22's own output for the patched `schema.prisma`, and Prisma reports no difference between the two.
- With `mysql-switch.patch` applied and Prisma Client generated for MySQL, `apps/api` and `apps/worker` type-check and all unit tests pass (25 worker, 2 API).
- The export's value conversion passed 37 checks: emoji, quotes, backslashes, newlines, timestamps, JSON, decimals, enums, column limits, and unique-key collisions.

**Not tested yet. Do this in a staging dry run (§4.7) before the real cut-over:**

- A live export from the production Postgres, and a live load into MySQL.
- The patched code running against a real MySQL server.

---

## 1. Current setup

|              | Today                                                                                                   |
| ------------ | ------------------------------------------------------------------------------------------------------- |
| Database     | PostgreSQL on Railway, accessed only through Prisma 5.22 (`packages/database`)                          |
| Processes    | `apps/api` (Express, Socket.IO) and `apps/worker` (sync engine, BullMQ jobs) on Railway; Redis on Railway |
| Web          | `apps/web` on Vercel. Talks only to the API (`VITE_API_URL`), never to the database                     |
| Schema       | `packages/database/prisma/schema.prisma`, 9 Postgres migrations in `prisma/migrations/`                 |
| Migrations   | Applied by `npm run start:api` (`prisma migrate deploy`) on every API start                            |
| Connection   | `DATABASE_URL` env var, read by both the API and the worker                                             |

There is no raw SQL anywhere (`$queryRaw` and `$executeRaw` are unused). Only the six Prisma calls in §5 depend on Postgres-only features.

---

## 2. Tables

| Table                   | Purpose                                                          | Rows grow with                     | Migrate data? |
| ----------------------- | ---------------------------------------------------------------- | ---------------------------------- | ------------- |
| `User`                  | Dashboard logins (bcrypt password hashes)                        | Staff accounts                     | **Yes**       |
| `Client`                | One row per person in the spreadsheet                            | New clients in the sheet           | **Yes**       |
| `Session`               | One row per spreadsheet row (purchase/usage)                     | Sheet rows                         | **Yes**       |
| `Payment`               | Payment details for a session                                    | Sheet rows                         | **Yes**       |
| `InvoiceTransaction`    | Consolidated invoice per client and payment date; status machine | Paid days per client               | **Yes**       |
| `InvoiceNumberCounter`  | Single row (`id = 'global'`) behind `INV-0001`, `INV-0002`…      | Never (one row)                    | **Yes, critical.** Without it, numbering restarts at `INV-0001` and reissues numbers already sent to clients. |
| `DataProcessing`        | Generated documents, including the invoice PDF (`pdfBase64`)     | Each generated invoice/version     | **Yes.** Largest table by bytes. |
| `VoiceProcessing`       | Voice generation records                                         | Each session/version               | Yes           |
| `Template`              | Voice/data templates and their `dependencyFields`                | Admin edits                        | **Yes**       |
| `ProcessingJob`         | Job ledger mirrored from BullMQ                                  | Each enqueued job                  | Yes           |
| `SyncRecord`            | Per-row change detection (`rowKey`, `sourceHash`, `rawData`)     | Sheet rows                         | **Yes.** Without it, the first sync sees every row as new. |
| `SyncRun`               | History of sync runs                                             | Every poll (30 s default)          | Yes (history only) |
| `RazorpayImportBatch`   | One row per uploaded Razorpay export                             | Uploads                            | Yes           |
| `RazorpayPaymentRecord` | Rows of those uploads and their match verdicts                   | Uploaded rows                      | Yes           |
| `AuditLog`              | Who changed what                                                 | Every sync change and user action  | Yes           |
| `_prisma_migrations`    | Prisma's migration history                                       | Migrations                         | **No.** MySQL starts its own history at `0_init_mysql` (§4.2). |

Everything is migrated together, in one snapshot. The tables reference each other by ID. The IDs are cuid strings that Prisma generates, and the export copies them unchanged.

---

## 3. Type mapping (Postgres → MySQL)

| Postgres (as Prisma created it)                          | MySQL                                       | Notes |
| -------------------------------------------------------- | ------------------------------------------- | ----- |
| `TEXT` (every Prisma `String`)                           | `VARCHAR(191)`, or `TEXT`/`MEDIUMTEXT`/`LONGTEXT` where marked | **Postgres `TEXT` is unlimited; `VARCHAR(191)` is not.** Keys, IDs and short fields stay `VARCHAR(191)` so they can be indexed. Free-text fields (errors, review reasons, proof links, rendered content, notes) are marked `@db.Text` in the patch. `pdfBase64` becomes `LONGTEXT`, because `TEXT` caps at 64 KB. |
| `TIMESTAMP(3)`                                           | `DATETIME(3)` holding **UTC**               | Prisma already stores UTC. The export copies the raw value with no time-zone shift. |
| `TEXT[]` (`InvoiceTransaction.flags`, `Template.dependencyFields`) | `JSON` array                      | MySQL has no array columns. Same values, e.g. `["AMOUNT_MISSING"]`. |
| `JSONB` (`SyncRecord.rawData`, `AuditLog.oldValue/newValue`) | `JSON`                                  | |
| `BOOLEAN`                                                | `BOOLEAN` (= `TINYINT(1)`)                  | 0 / 1 |
| `DECIMAL(14,2)`                                          | `DECIMAL(14,2)`                             | Copied as text, so no rounding |
| `INTEGER`                                                | `INT`                                       | |
| Postgres enum types                                      | Inline `ENUM(...)`                          | Same values |
| Byte-exact comparison, `mode: "insensitive"` for search  | Collation `utf8mb4_unicode_ci`              | Search becomes case-insensitive by default, and also accent-insensitive ("Jose" finds "José"). **Unique keys also ignore case, accents and trailing spaces** (see below). |

Use **utf8mb4** everywhere. Client names and sheet values may contain any Unicode.

### When the export stops

The export checks every value against its MySQL column before writing anything. If any value won't fit, it writes nothing and lists the problems. To fix each kind:

| Message                                                     | Fix |
| ----------------------------------------------------------- | --- |
| `Table.column (id …): 240 characters, column allows 191`    | Mark that field `@db.Text` in `schema.prisma` (unless it is unique or indexed), regenerate `schema.mysql.sql` (command in its header), and export again. For a key column, shorten the data in Postgres instead. |
| `… collide on <index> once case/accents/trailing spaces are ignored` | Two rows were distinct in Postgres but are the same key in MySQL (e.g. invoice numbers `INV-7` and `inv-7` for the same client). Decide which row is right and merge or delete the other in Postgres, then export again. |
| `Postgres and schema.mysql.sql don't match`                 | Postgres is missing a migration, or `schema.prisma` changed after this package was made. Run `npm run prisma:deploy` against Postgres, or regenerate `schema.mysql.sql`. |

---

## 4. Migration steps

### 4.1 Prepare MySQL

1. Create the RDS instance (§8.1), then create the database:
   ```sql
   CREATE DATABASE invoice_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'invoice_app'@'%' IDENTIFIED BY '<strong password>';
   GRANT ALL PRIVILEGES ON invoice_platform.* TO 'invoice_app'@'%';
   ```
   The app user needs DDL rights because `prisma migrate deploy` runs at API start.

### 4.2 Switch the code (on a branch)

```bash
git switch -c mysql
git apply docs/mysql-migration/mysql-switch.patch

# Replace the Postgres migration history with one MySQL baseline. The old migrations stay in git history.
git rm -r packages/database/prisma/migrations
mkdir -p packages/database/prisma/migrations/0_init_mysql
cp docs/mysql-migration/schema.mysql.sql packages/database/prisma/migrations/0_init_mysql/migration.sql
printf '# Please do not edit this file manually\n# It should be added in your version-control system (i.e. Git)\nprovider = "mysql"\n' \
  > packages/database/prisma/migrations/migration_lock.toml

npm run build        # prisma generate + type-check
npm test
```

Also update on this branch: `.env.example` (`DATABASE_URL="mysql://invoice:invoice@localhost:3306/invoice_platform"`), the `postgres` service in `docker-compose.yml` (swap for `mysql:8.4` with `MYSQL_DATABASE`, `MYSQL_USER`, `MYSQL_PASSWORD`, `MYSQL_ROOT_PASSWORD` on port 3306), and the Postgres mentions in `README.md`.

**Don't merge this branch until cut-over (§4.8).** After the merge, `main` can no longer run on Postgres.

### 4.3 Create the tables

From the `mysql` branch, with `DATABASE_URL` pointing at RDS (see §6 for the format):

```bash
npm run prisma:deploy
# → Applying migration `0_init_mysql` … All migrations have been successfully applied.
```

Don't start the worker yet. It would begin syncing the spreadsheet into an empty database.

### 4.4 Export from Postgres

The export only runs `SELECT`s, inside one read-only snapshot. Run it from the repo root (any branch):

```bash
npm install --no-save pg@8
DATABASE_URL='postgresql://…railway…' node scripts/export-postgres-to-mysql.mjs invoice-data.mysql.sql
# stderr shows row counts, e.g.
# Exported rows: { User: 3, Client: 412, Session: 980, Payment: 975, InvoiceTransaction: 640, … }
```

**What the export does:**

- Reads the table and column list from `schema.mysql.sql`. It stops if the live Postgres has different columns.
- Copies timestamps as raw UTC text and converts `TEXT[]` to JSON arrays. Decimals are copied as text.
- Checks every value against its MySQL column: length, enum value, valid JSON, `NOT NULL`, and unique-key collisions under MySQL's collation. On any problem it writes nothing (§3).
- Writes `INSERT` batches of at most 200 rows or 4 MB, inside one transaction, with foreign-key checks off during the load.
- Skips `_prisma_migrations` on purpose.

**Treat `invoice-data.mysql.sql` as sensitive:** it contains client names, phone numbers, emails and password hashes. Delete it after loading.

### 4.5 Load into MySQL

Load into the **empty** tables from §4.3:

```bash
mysql --default-character-set=utf8mb4 -h <host> -u invoice_app -p invoice_platform < invoice-data.mysql.sql
```

The file sets `utf8mb4`, UTC and backslash escaping for its own session, and commits all or nothing. If it fails partway, nothing is kept. Fix the cause, then load again.

### 4.6 Verify

Row counts should match the numbers the export printed:

```sql
SELECT 'User', COUNT(*) FROM `User`
UNION ALL SELECT 'Client', COUNT(*) FROM `Client`
UNION ALL SELECT 'Session', COUNT(*) FROM `Session`
UNION ALL SELECT 'Payment', COUNT(*) FROM `Payment`
UNION ALL SELECT 'InvoiceTransaction', COUNT(*) FROM `InvoiceTransaction`
UNION ALL SELECT 'InvoiceNumberCounter', COUNT(*) FROM `InvoiceNumberCounter`
UNION ALL SELECT 'DataProcessing', COUNT(*) FROM `DataProcessing`
UNION ALL SELECT 'VoiceProcessing', COUNT(*) FROM `VoiceProcessing`
UNION ALL SELECT 'Template', COUNT(*) FROM `Template`
UNION ALL SELECT 'ProcessingJob', COUNT(*) FROM `ProcessingJob`
UNION ALL SELECT 'SyncRecord', COUNT(*) FROM `SyncRecord`
UNION ALL SELECT 'SyncRun', COUNT(*) FROM `SyncRun`
UNION ALL SELECT 'RazorpayImportBatch', COUNT(*) FROM `RazorpayImportBatch`
UNION ALL SELECT 'RazorpayPaymentRecord', COUNT(*) FROM `RazorpayPaymentRecord`
UNION ALL SELECT 'AuditLog', COUNT(*) FROM `AuditLog`;
```

Spot checks. Compare each with the same query on Postgres (drop the backticks there and use double quotes):

```sql
SELECT `value` FROM `InvoiceNumberCounter` WHERE id = 'global';           -- must match exactly
SELECT SUM(`paymentAmount`), SUM(`igst`), SUM(`cgst`), SUM(`sgst`) FROM `Payment`;
SELECT `status`, COUNT(*) FROM `InvoiceTransaction` GROUP BY `status`;
SELECT MIN(`createdAt`), MAX(`createdAt`) FROM `Client`;                  -- identical, both UTC
SELECT COUNT(*) FROM `InvoiceTransaction` WHERE JSON_LENGTH(`flags`) > 0; -- same as cardinality(flags) > 0 in Postgres
```

Then check in the app (after §4.7 or §4.8):

1. Sign in with an existing account. This proves the password hashes came across.
2. Open a generated invoice PDF. This proves `pdfBase64` came across whole.
3. The Invoices page's flagged count matches the `JSON_LENGTH` query above. This exercises the one rewritten JSON filter (§5).
4. **The first sync reports 0 added and close to 0 updated** on the Sync page. If it reports many new rows, `SyncRecord` didn't come across correctly. Stop the worker and investigate before it generates duplicate sessions and invoices.

### 4.7 Staging dry run (do this first)

Run §4.1 to §4.6 once against a throwaway RDS instance, with the `mysql` branch running on it. Leave `MAIL_FROM`, `GMAIL_*` and `SMTP_*` **empty**: the worker auto-emails generated invoices only when a mailer is configured (`isMailerConfigured`), so nothing reaches clients. Let the worker run two or three syncs and click through the Clients, Payments, Invoices, Verification and Settings pages. Then delete the staging instance and its data file.

### 4.8 Cut-over

1. **Let the queue drain.** On the processing pages, wait until nothing is `QUEUED` or `RUNNING`.
2. **Pause writes.** Stop the worker first; it is the main writer. The spreadsheet is the source of truth, so the next sync catches up anything missed. Then stop the API. A Razorpay upload, password change or manual "Send Email" during the window would be lost, so pick a quiet time.
3. **Run the final export and load** (§4.4 to §4.6) into the RDS database prepared in §4.3.
4. **Deploy the `mysql` branch** with `DATABASE_URL` pointing at RDS. Start the API: `migrate deploy` finds `0_init_mysql` already applied and does nothing. Then start the worker and watch the first sync (§4.6, check 4).
5. **Point the web app at the new API** if its URL changed: `VITE_API_URL` on Vercel, then redeploy.
6. **Keep the Railway Postgres read-only** for a week as a rollback point. Rolling back means deploying `main` from before the merge, with the old `DATABASE_URL`. Anything written to MySQL since then would need to be re-entered or re-synced, so decide quickly.

---

## 5. Code changes (Postgres → MySQL)

All of these are in `mysql-switch.patch`.

| Where                                                         | Postgres                                                            | MySQL |
| ------------------------------------------------------------- | ------------------------------------------------------------------- | ----- |
| `schema.prisma` datasource                                    | `provider = "postgresql"`                                           | `provider = "mysql"` |
| `schema.prisma` `InvoiceTransaction.flags`                    | `String[] @default([])`                                             | `Json`. MySQL has no scalar lists, and Prisma can't default a `Json` column on MySQL. Every `create` already sets `flags`. |
| `schema.prisma` `Template.dependencyFields`                   | `String[]`                                                          | `Json` |
| `schema.prisma` free-text fields (23 columns)                 | `String` (unlimited `TEXT`)                                         | `@db.Text`, `@db.MediumText` (`content`), `@db.LongText` (`pdfBase64`) |
| `apps/api/src/routes/clients.ts`, `invoices.ts`, `payments.ts` (4 searches) | `contains: x, mode: "insensitive"`                    | `contains: x`. The collation is already case-insensitive. `payments.ts` still type-checks with `mode` (because of its `as const`), but fails at runtime with "Unknown argument `mode`". |
| `apps/api/src/routes/invoices.ts` flagged count               | `NOT: { flags: { isEmpty: true } }`                                 | `NOT: { flags: { equals: [] } }` (verify in staging, §4.6 check 3) |
| `apps/api/src/routes/invoices.ts` list response               | `flags: r.flags`                                                    | `flags: r.flags as string[]` (same JSON on the wire) |
| `apps/worker/src/sync/syncEngine.ts` `ensureProcessingForType` | `template.dependencyFields`                                        | `template.dependencyFields as string[]` |
| `apps/worker/src/sync/syncEngine.ts` `findDuplicateInvoiceNumber` | `updateMany` with `flags: { has }` / `flags: { push }`          | Read the other record's flags, and add `DUPLICATE_INVOICE_NUMBER` only if it is missing. Not a single atomic statement any more, but it only ever adds the same flag, so a race just writes it twice. |

**Unchanged:** cuid IDs, `@updatedAt`, `distinct`, interactive transactions, upserts, `Decimal` and the `InvoiceNumberCounter` increment all work the same on MySQL.

### Behaviour differences to know about

- **Unique keys ignore case, accents and trailing spaces.** `SyncRecord(source, rowKey)` includes the raw invoice number. Two sheet rows for the same client whose invoice numbers differ only in case (`INV-7` / `inv-7`) used to be two records; on MySQL they are one. The export reports any such pair in the current data. `RazorpayPaymentRecord.razorpayPaymentId` (`pay_…`) is affected the same way, but Razorpay IDs differing only in case aren't a realistic case.
- **Long values in key columns are rejected.** A spreadsheet cell over 191 characters in a `VARCHAR(191)` field (client name, product, invoice number, payment mode, revenue month, email, phone, state, age, gender) now fails that row's sync with Prisma error `P2000`, where Postgres stored it. The row shows as `ERROR` on the Sync page.

---

## 6. App changes after the move

### 6.1 `DATABASE_URL`

```
mysql://invoice_app:<url-encoded password>@<rds-endpoint>:3306/invoice_platform?sslaccept=strict&sslcert=<path to RDS CA bundle>
```

- With `require_secure_transport = ON` on the server, Prisma connects over TLS. Add `sslaccept=strict` and `sslcert` (the RDS `global-bundle.pem`; the path is relative to `schema.prisma`) to also verify the server certificate.
- Prisma's pool defaults to `num_cpus × 2 + 1` connections per process. Two processes on small instances fit easily in RDS's `max_connections`. Set `&connection_limit=5` if you run many API tasks.

### 6.2 Env vars

| Variable                          | Change |
| --------------------------------- | ------ |
| `DATABASE_URL`                    | MySQL URL above, on **both** the API and the worker |
| `REDIS_URL`                       | Unchanged, or ElastiCache if Redis moves to AWS too (§8.2) |
| `WEB_URL`                         | Unchanged unless the dashboard URL changes (it drives CORS) |
| `VITE_API_URL` (Vercel)           | Set to the new API URL if the API moves |
| Everything else                   | Unchanged |

---

## 7. Things to keep the same

- **UTC everywhere.** Prisma writes UTC, the export copies UTC, and RDS runs in UTC.
- **IDs as they are.** Never regenerate IDs during the move. Foreign keys, `idempotencyKey` values (`data:invoice:<txn id>:v2`) and BullMQ job IDs all contain them.
- **`SyncRecord` and `rawData` as they are.** The sync compares each sheet row with these. If they are lost or altered, every row looks new or changed. That creates duplicate sessions, bumps `contentVersion`, regenerates invoices and auto-emails them to clients again.
- **`InvoiceNumberCounter.value`.** It must be at least as high as on Postgres.
- **Only the API runs migrations.** `start:api` keeps running `prisma migrate deploy`; the worker never does.

---

## 8. AWS notes

### 8.1 Engine and parameter group

- **Engine:** **RDS for MySQL 8.0 or 8.4**, or **Aurora MySQL 3** (8.0-compatible). Pick a region close to the users. The schema uses nothing newer than 8.0.
- **Parameter group** (custom, so it can be changed):

  | Setting                    | Value                  | Notes |
  | -------------------------- | ---------------------- | ----- |
  | `time_zone`                | UTC                    | RDS default; leave it |
  | `character_set_server`     | `utf8mb4`              | |
  | `collation_server`         | `utf8mb4_unicode_ci`   | Matches the tables. Each table also sets it explicitly. |
  | `lower_case_table_names`   | `0` (default)          | **Leave it at 0.** Table names are mixed case (`InvoiceTransaction`) and Prisma queries them exactly. On RDS this can only be set when the instance is created. |
  | `sql_mode`                 | Default                | The data file turns `NO_BACKSLASH_ESCAPES` off for its own session either way |
  | `require_secure_transport` | `ON`                   | TLS only |
  | `max_allowed_packet`       | Default (64 MB)        | Export statements stay under 4 MB plus one row |

- **Encryption at rest:** on. Use **Multi-AZ** for production.

### 8.2 Network and where the app runs

RDS should be private (no public endpoint). That decides where the API and worker run:

| Option | How | Notes |
| ------ | --- | ----- |
| **A. API and worker on AWS (recommended)** | Two **ECS Fargate** services (or one EC2 with `scripts/start-api.sh` / `start-worker.sh` under systemd) in the RDS VPC. Redis on **ElastiCache**. API behind an **ALB** with HTTPS. | RDS stays private, and every query is in-region. The worker runs several sequential queries per sheet row. `syncEngine.ts` records that a remote high-latency database already once pushed a sync past `SYNC_TIMEOUT_MS`. |
| B. Keep Railway, RDS public | RDS publicly accessible, TLS required, security group limited to Railway's static outbound IPs (Railway Pro). | Less isolated, and every query crosses the internet. Expect slower syncs; watch `SYNC_TIMEOUT_MS`. |

With option A:

- The worker is long-running (polling, BullMQ), so use a service or instance, not Lambda.
- Socket.IO needs WebSockets. The ALB supports them. Enable stickiness if you run more than one API task.
- Health check: `/health` (as in `railway.json`).
- Set `VITE_API_URL` on Vercel to the ALB's HTTPS domain, and keep `WEB_URL` as the dashboard origin for CORS. Auth is a Bearer token, not a cookie, so the API can sit on any domain.

### 8.3 Running the export and load

- **Export** from any machine that can reach the Railway Postgres (its public URL from a laptop is fine). The script only reads.
- **Load** from a machine inside the VPC: an EC2 bastion, or AWS Systems Manager port forwarding (`AWS-StartPortForwardingSessionToRemoteHost`) from a laptop to RDS. §4.3 (`prisma:deploy`) needs the same access.
- **Moving the data file:** copy it through an encrypted S3 bucket, or `scp` it to the bastion. **Delete it from every location after loading.** It contains personal data and password hashes.

### 8.4 Secrets and backups

- **Secrets Manager / SSM Parameter Store:** store `DATABASE_URL`, `AUTH_SECRET`, `GMAIL_*`, `GOOGLE_SERVICE_ACCOUNT_PRIVATE_KEY` and `SMTP_PASSWORD`, and inject them into the ECS tasks as env vars. No `.env` file is needed; `scripts/start.mjs` reads the real environment.
- **Snapshot:** take a manual RDS snapshot right after the load, before starting the worker.
- **Backups:** keep automated backups on (7+ days).
- **Railway Postgres:** keep it, read-only, for a week after cut-over as a fallback.
