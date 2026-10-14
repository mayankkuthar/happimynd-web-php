# Games Hub: Postgres (Neon) → MySQL migration guide

**For:** the backend lead moving the HappiMynd Games Hub database to MySQL on AWS and rebuilding its API.
**Package contents** (all in `docs/mysql-migration/` unless noted):

| File                                           | What it is                                                                   |
| ---------------------------------------------- | ---------------------------------------------------------------------------- |
| `MIGRATION_GUIDE.md`                           | This guide: what's in the database, how to move it, what changes in the code |
| `schema.mysql.sql`                             | MySQL schema for all six tables. Safe to run more than once.                 |
| `API_SPEC.md`                                  | Contract for every endpoint the web app calls (request, response, rules)     |
| `scripts/export-neon-to-mysql.mjs` (repo root) | Read-only export of the current data as MySQL `INSERT` statements            |

**What was tested (on MySQL 9.4, written for 8.0+):**

- The schema loads, and loads again without errors.
- An export containing emoji, quotes, backslashes, newlines and non-UTC timestamps loaded with every value intact.
- The MySQL versions of the dashboard, tracking and SSO queries in §5 return the expected results.

---

## 1. Current setup

|              | Today                                                                                       |
| ------------ | ------------------------------------------------------------------------------------------- |
| Database     | PostgreSQL on Neon (`@neondatabase/serverless` driver)                                      |
| App          | TanStack Start (React 19) built with Nitro for Cloudflare Workers. Node 22+ in development. |
| API          | The same app serves `/api/*` (`src/server/api-handler.ts`)                                  |
| SQL location | `src/server/db.ts`, `auth-service.ts`, `analytics-db.ts`, `catalog-db.ts`, `content-db.ts`  |
| Connection   | `DATABASE_URL` env var                                                                      |

The front end only talks to the backend through the HTTP endpoints in `API_SPEC.md`. The one exception is page rendering, which also reads published game-card edits on the server (see §6.3). If the new backend keeps the same endpoints and response shapes, the front end only needs `VITE_API_URL` pointed at it.

---

## 2. Tables

| Table             | Purpose                                                        | Rows grow with                            | Migrate data?                                                  |
| ----------------- | -------------------------------------------------------------- | ----------------------------------------- | -------------------------------------------------------------- |
| `users`           | Player accounts (may be shared with other HappiMynd apps)      | Signups                                   | **Yes**                                                        |
| `sso_tickets`     | Single-use login codes for external games, valid 120 s         | Every external launch by a signed-in user | **No.** Create the empty table; old codes are already expired. |
| `activity_events` | Hub visits (`hub_open`) and external game launches             | Every visit (one per browser tab)         | Yes                                                            |
| `game_sessions`   | One row per play of a built-in game                            | Every Start press                         | Yes                                                            |
| `game_overrides`  | Admin edits to a game's name, tagline, description, visibility | Admin publishes (≤ ~25 rows)              | Yes                                                            |
| `game_content`    | Admin-edited in-game text, one row per customised list         | Admin publishes (≤ ~40 rows)              | Yes                                                            |

**`users`:** the hub doesn't create this table. If another HappiMynd service already owns `users` in MySQL, keep that table; the hub needs `id`, `email`, `username`, `name`, `password_hash`, `avatar` and `created_at`.

**The other five tables** are created by the hub. Currently that happens automatically on first use; on MySQL, run `schema.mysql.sql` instead.

---

## 3. Type mapping (Postgres → MySQL)

| Postgres                                   | MySQL                                         | Notes                                                                                                           |
| ------------------------------------------ | --------------------------------------------- | --------------------------------------------------------------------------------------------------------------- |
| `UUID` (users.id, sso_tickets.user_id)     | `CHAR(36)`                                    | Lowercase text. **MySQL won't generate it.** The app must create the ID at signup (see §6.1).                   |
| `VARCHAR(36)` ids in hub tables            | `CHAR(36)`                                    | Already generated by the app or browser                                                                         |
| `TIMESTAMPTZ` (users, sso_tickets)         | `DATETIME(3)` holding **UTC**                 | The export converts to UTC. Run MySQL with `time_zone = '+00:00'`.                                              |
| `TIMESTAMP` (hub tables)                   | `DATETIME(3)`                                 | Already UTC, written by the app as `'YYYY-MM-DD HH:MM:SS.mmm'`                                                  |
| `SMALLINT` flag (`game_overrides.hidden`)  | `TINYINT(1)`                                  | 0 / 1                                                                                                           |
| `TEXT` JSON (`game_sessions.stats`)        | `TEXT`                                        | Could be `JSON` if you prefer; the app writes valid JSON                                                        |
| `TEXT` JSON (`game_content.items`)         | `MEDIUMTEXT`                                  | Largest list today is about 25 KB                                                                               |
| `LOWER(email)` / `LOWER(username)` indexes | Plain `UNIQUE` keys with `utf8mb4_0900_ai_ci` | That collation is case-insensitive, so lookups and uniqueness ignore case. Signup stores both lowercase anyway. |

Use **utf8mb4** everywhere. Avatars and game text contain emoji.

---

## 4. Migration steps

### 4.1 Prepare MySQL

1. Create the database with utf8mb4:
   ```sql
   CREATE DATABASE happimynd_hub CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
   ```
2. Set the server or parameter group to `time_zone = '+00:00'`. The schema file also sets it for its own session.
3. Load the schema:
   ```bash
   mysql --default-character-set=utf8mb4 -h <host> -u <user> -p happimynd_hub < docs/mysql-migration/schema.mysql.sql
   ```

### 4.2 Export from Neon

The export only runs `SELECT`s. Run it from the repo root after `npm install`:

```bash
DATABASE_URL='postgres://…neon…' node scripts/export-neon-to-mysql.mjs > hub-data.mysql.sql
# stderr shows row counts, e.g.
# Exported rows: { users: 412, activity_events: 3810, game_sessions: 2950, game_overrides: 3, game_content: 2 }
```

**What the export does:**

- Converts every timestamp to UTC (`DATETIME(3)` format) and IDs to text.
- Escapes strings for MySQL's default `sql_mode`.
- Writes 200-row `INSERT` batches in foreign-key order, with foreign-key checks off during the load.
- Skips tables that don't exist yet, and skips `sso_tickets` on purpose.

**Treat `hub-data.mysql.sql` as sensitive:** it contains emails and password hashes. Delete it after loading.

### 4.3 Load into MySQL

Load into **empty** tables:

```bash
mysql --default-character-set=utf8mb4 -h <host> -u <user> -p happimynd_hub < hub-data.mysql.sql
```

If the server runs with `NO_BACKSLASH_ESCAPES` in `sql_mode`, turn it off for this session first. The file uses backslash escapes.

### 4.4 Verify

Row counts should match the numbers the export printed:

```sql
SELECT 'users', COUNT(*) FROM users
UNION ALL SELECT 'activity_events', COUNT(*) FROM activity_events
UNION ALL SELECT 'game_sessions', COUNT(*) FROM game_sessions
UNION ALL SELECT 'game_overrides', COUNT(*) FROM game_overrides
UNION ALL SELECT 'game_content', COUNT(*) FROM game_content;
```

Spot checks (compare with the same query on Neon):

```sql
SELECT MIN(created_at), MAX(created_at) FROM users;                 -- UTC on both sides
SELECT game_slug, COUNT(*) FROM game_sessions GROUP BY game_slug;   -- per-game plays
SELECT COUNT(*) FROM game_sessions WHERE stats IS NOT NULL AND NOT JSON_VALID(stats);  -- expect 0
SELECT COUNT(*) FROM game_content WHERE NOT JSON_VALID(items);      -- expect 0
```

Then sign in to the app with an existing account. That proves the bcrypt hashes and IDs made it across.

### 4.5 Cut-over

1. **Pause writes.** Admin publishing can simply wait. Analytics writes (`/api/track/*`) are fire-and-forget, so a short gap only loses a few visit and play records. Signups during the window would be lost, so pick a quiet time or put the app in maintenance mode.
2. **Run the final export and load** (§4.2–4.4).
3. **Switch the app** to the new backend: set `VITE_API_URL`, or deploy the new API on the same origin.
4. **Keep Neon read-only** for a week as a rollback point. Rollback means pointing the app back at the old API.

---

## 5. Query changes (Postgres → MySQL)

All of these were run on MySQL and return the expected results.

| Where (current code)                                                                | Postgres                                                                   | MySQL                                                                                                    |
| ----------------------------------------------------------------------------------- | -------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------- |
| Every query                                                                         | `$1, $2…` placeholders                                                     | `?`                                                                                                      |
| `analytics-db.ts` `localDay()`: daily buckets in the admin's timezone               | `CAST(CAST(col + ($2::int * INTERVAL '1 minute') AS DATE) AS VARCHAR(10))` | `DATE_FORMAT(DATE_ADD(col, INTERVAL ? MINUTE), '%Y-%m-%d')`                                              |
| `auth-service.ts` signup                                                            | `INSERT … RETURNING id, …` with `gen_random_uuid()`                        | Generate `id = crypto.randomUUID()` in the app, `INSERT`, then return the row you built (or `SELECT` it) |
| `auth-service.ts` create SSO ticket                                                 | `NOW() + INTERVAL '2 minutes'`                                             | `UTC_TIMESTAMP(3) + INTERVAL 120 SECOND`                                                                 |
| `auth-service.ts` login and signup lookups                                          | `LOWER(username) = $1 OR LOWER(email) = $1`                                | `username = ? OR email = ?` (the collation is case-insensitive)                                          |
| Duplicate key detection (`isDuplicateKey`)                                          | Error code `23505`                                                         | Error number `1062` (`ER_DUP_ENTRY`)                                                                     |
| Table setup on first use                                                            | `CREATE INDEX IF NOT EXISTS`                                               | Not supported; run `schema.mysql.sql` once at deploy instead                                             |
| `CREATE TABLE IF NOT EXISTS`, `COUNT(DISTINCT CASE …)`, `SUM(CASE …)`, transactions | Same                                                                       | Same                                                                                                     |

### Recommended fix while porting: make the SSO redeem atomic

`exchangeSsoTicket()` currently reads the ticket, checks `used_at` and `expires_at` in code, and then runs a separate `UPDATE`. Two requests at the same moment could both pass the check. In MySQL, redeem in one statement and check the affected-row count:

```sql
UPDATE sso_tickets
   SET used_at = UTC_TIMESTAMP(3)
 WHERE code = ? AND used_at IS NULL AND expires_at > UTC_TIMESTAMP(3);
-- affectedRows = 1 → valid: SELECT the user and return it
-- affectedRows = 0 → SELECT the ticket to report "already used" / "expired" / "not found"
```

Tested: the first redeem changes 1 row; the second changes 0.

Expired tickets can be cleared with `DELETE FROM sso_tickets WHERE expires_at < UTC_TIMESTAMP(3) - INTERVAL 1 DAY` on a schedule. The current app never deletes them.

---

## 6. App changes after the move

### 6.1 Signup must create the user ID

MySQL has no `gen_random_uuid()` default. Generate the UUID in the API: `crypto.randomUUID()`, lowercase, 36 characters.

### 6.2 If the API moves to a separate service

- Point the web app at it with `VITE_API_URL=https://api.example.com`.
- Allow the web app's origin in CORS. The **admin cookie** (`hm_admin`, `SameSite=Strict`, `Path=/api/admin`) only works if the admin API is on the **same site** as the web app, for example `hub.example.com` and `api.example.com` under one registrable domain. Otherwise move to `SameSite=None; Secure` and add CSRF protection. Details are in `API_SPEC.md` §5.
- Tracking calls use `fetch(…, { keepalive: true })` with a `text/plain` body, which avoids a CORS preflight. The new API must accept `text/plain` and parse the body as JSON.

### 6.3 Server-side catalog read

While rendering pages, the app reads published card edits directly from the database through a server function (`src/lib/catalog.ts` → `readCatalogOverrides()`). After the move, change that function to fetch `GET {API}/api/catalog` server-to-server. It's one function, and it already falls back to the coded catalog on error.

### 6.4 Env vars

| Variable                                                     | Change                                                                            |
| ------------------------------------------------------------ | --------------------------------------------------------------------------------- |
| `DATABASE_URL`                                               | Becomes the MySQL connection, or is dropped from the web app if the API moves out |
| `ADMIN_EMAIL`, `ADMIN_PASSWORD_HASH`, `ADMIN_SESSION_SECRET` | Move with whichever service serves `/api/admin/*`                                 |
| `VITE_API_URL`                                               | Set if the API is on a different origin                                           |

---

## 7. Things to keep the same

- **UTC everywhere.** The dashboard converts to the viewer's timezone using a `tz` offset the browser sends.
- **Guests have no ID.** `user_type = 'guest'` and `user_id = NULL`. This is a deliberate privacy decision, so don't add device IDs.
- **Validation rules.** The publish rules are shared between the dashboard and the server: `src/config/catalog-limits.ts` and `src/config/content-schemas.ts`. A Node/TypeScript backend can import those files unchanged. Any other language must port them exactly (see "Content rules" in `API_SPEC.md` §5), or the dashboard and server will disagree about what's valid.
- **Stored item shape.** `game_content.items` must keep exactly the JSON the games read. Don't normalise it into columns unless the API reassembles the same JSON.

---

## 8. AWS notes

### 8.1 Engine and parameter group

- **Engine:** **RDS for MySQL 8.0 / 8.4** or **Aurora MySQL 3** (8.0-compatible). The schema uses no MySQL 9 features.
- **Parameter group** (custom, so it can be changed):

  | Setting                | Value                | Notes                                                                              |
  | ---------------------- | -------------------- | ---------------------------------------------------------------------------------- |
  | `time_zone`            | UTC                  | RDS default; leave it                                                              |
  | `character_set_server` | `utf8mb4`            |                                                                                    |
  | `collation_server`     | `utf8mb4_0900_ai_ci` |                                                                                    |
  | `sql_mode`             | Default              | Must not include `NO_BACKSLASH_ESCAPES`; the data file relies on backslash escapes |

- **Encryption:** turn on encryption at rest and require TLS for connections (`require_secure_transport = ON`). Connect with the RDS CA bundle.

### 8.2 Network and how the app reaches the database

The web app is currently built for **Cloudflare Workers**, and a Worker can't open a connection into a private VPC. Pick one:

| Option                                            | How                                                                                                              | Notes                                                                                                               |
| ------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------- |
| **A. API on AWS (recommended; matches the plan)** | Run the rebuilt API in the VPC: Lambda + **RDS Proxy**, or ECS/Fargate. The web app calls it via `VITE_API_URL`. | RDS stays private. RDS Proxy prevents Lambda from exhausting database connections.                                  |
| B. Keep the API in the Workers app                | Use Cloudflare **Hyperdrive** (supports MySQL) to reach RDS                                                      | Needs RDS reachable from Cloudflare (public endpoint with IP allow-listing, or a Cloudflare Tunnel). Less isolated. |

With option A:

- **Domain:** serve the API on a subdomain of the web app's domain (e.g. `api.happimynd.com` via API Gateway or ALB with a custom domain). That keeps the admin cookie's `SameSite=Strict` working (§6.2).
- **CORS:** allow the web app's origin, with credentials, for `/api/admin/*`.
- **Tracking:** accept `text/plain` bodies on `/api/track/*`. If API Gateway is in front, make sure it passes the raw body through.
- **Page rendering:** the web app's server-side catalog read (§6.3) must fetch `GET https://api…/api/catalog`.

### 8.3 Running the export and load

- **Export** from any machine that can reach Neon (a laptop is fine). The script only reads.
- **Load** from a machine inside the VPC: an EC2 bastion, or AWS Systems Manager port forwarding (`AWS-StartPortForwardingSessionToRemoteHost`) to RDS from a laptop.
- **Moving the data file:** copy it through an encrypted S3 bucket, or `scp` to the bastion. **Delete it from every location after loading**; it contains emails and password hashes.

### 8.4 Secrets and backups

- **Secrets Manager:** store the DB credentials and `ADMIN_EMAIL` / `ADMIN_PASSWORD_HASH` / `ADMIN_SESSION_SECRET` there, and read them at runtime. Rotating `ADMIN_SESSION_SECRET` signs the admin out everywhere.
- **Snapshot:** take a manual RDS snapshot right after the load and before cut-over.
- **Backups:** keep automated backups on (7+ days).
- **Neon:** keep it read-only for a week after cut-over as a fallback.
