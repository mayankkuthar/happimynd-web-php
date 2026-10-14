# Games Hub backend — delivery notes

The Games Hub API is now served by the existing Laravel (PHP) application on
EC2, backed by a **separate `happimynd_hub` MySQL database**. It implements the
contract in `API_SPEC.md`, so the front end needs no code changes beyond
pointing `VITE_API_URL` at this API.

---

## 1. Point the front end at it

```
VITE_API_URL=https://api.happimynd.com
```

All routes are under `/api/...`. `GET /api/health` should return
`{ "status": "ok", "dbConnected": true, ... }`.

---

## 2. Endpoints

| Method | Path                        | Notes |
| ------ | --------------------------- | ----- |
| GET    | `/api/health`               | `{ status, service, timestamp, dbConnected }` |
| POST   | `/api/auth/signup`          | `201 { success, user }` |
| POST   | `/api/auth/login`           | `200 { success, user }` |
| GET    | `/api/auth/me?userId=<uuid>`| `x-user-id` header also accepted |
| POST   | `/api/auth/logout`          | `200 { success: true }` |
| POST   | `/api/auth/sso/create`      | `201 { success, code, expiresInSeconds:120, gameId }` |
| POST   | `/api/auth/sso/exchange`    | `200 { success, user, gameId }` |
| POST   | `/api/track/event`          | `text/plain` body → `204` / `400` |
| POST   | `/api/track/session/start`  | `text/plain` body → `204` / `400` |
| POST   | `/api/track/session/end`    | `text/plain` body → `204` / `400` |
| GET    | `/api/catalog`              | `Cache-Control: public, max-age=30` |
| GET    | `/api/content/{slug}`       | `Cache-Control: public, max-age=30` |
| POST   | `/api/admin/login`          | sets `hm_admin` cookie |
| POST   | `/api/admin/logout`         | clears the cookie |
| GET    | `/api/admin/session`        | no login needed |
| GET    | `/api/admin/overview`       | `?days=7|30|90&tz=<minutes>` |
| GET    | `/api/admin/catalog`        | uncached |
| GET    | `/api/admin/content`        | uncached |
| PUT    | `/api/admin/publish`        | validate-all-then-write in one transaction |

`/auth/...` is an alias for `/api/auth/...` (both work).

Error envelope: `{ "success": false, "message": "...", "error": "..." }`.
Unknown route: `404 { "error": "NOT_FOUND", "message": "Cannot GET /api/x" }`.

---

## 3. Environment variables (set on the server)

```dotenv
# Hub database (falls back to DB_* when unset)
HUB_DB_HOST=
HUB_DB_PORT=3306
HUB_DB_DATABASE=happimynd_hub
HUB_DB_USERNAME=
HUB_DB_PASSWORD=
HUB_DB_COLLATION=utf8mb4_0900_ai_ci
# HUB_DATABASE_URL=            # optional full DSN, overrides the above

# Admin dashboard (single admin)
HUB_ADMIN_EMAIL=
HUB_ADMIN_PASSWORD_HASH=
HUB_ADMIN_SESSION_SECRET=      # at least 32 characters
HUB_ADMIN_COOKIE_SECURE=true

# CORS (only needed when the web app is on a different origin)
CORS_ORIGINS=https://hub.happimynd.com
CORS_ALLOW_CREDENTIALS=true
```

Generate the admin password hash (PBKDF2-HMAC-SHA256, 100000 iterations,
16-byte salt, 32-byte hash):

```bash
php -r '$p="YOUR_PASSWORD";$s=random_bytes(16);$h=hash_pbkdf2("sha256",$p,$s,100000,32,true);echo "pbkdf2:100000:".bin2hex($s).":".bin2hex($h)."\n";'
```

---

## 4. Create the database and run the migrations

```sql
CREATE DATABASE happimynd_hub CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
```

Then, from the app root on the server:

```bash
php artisan migrate --path=database/migrations/hub --database=hub
```

This creates the six tables (`users`, `sso_tickets`, `activity_events`,
`game_sessions`, `game_overrides`, `game_content`) on the `hub` connection.
To load the exported Neon data, run the `hub-data.mysql.sql` file into this
database **before** or after migrating, as usual.

Times are stored as UTC `DATETIME(3)`; the framework opens the hub connection
with `time_zone = '+00:00'` per request, so the main app's `Asia/Kolkata`
timezone does not leak in.

---

## 5. Notes, differences, and known gaps

- **Content validation is permissive for per-game lists.** The source of truth
  `src/config/content-schemas.ts` was **not** in the migration package, so the
  server can only strictly validate the shared `end-titles` / `end-messages`
  lists and the catalog/card limits (`config/hub.php`, `config/hub_content.php`).
  The dashboard still validates everything client-side with the real schemas, so
  publishing works; but the server will accept an unknown per-game list rather
  than reject it with `unknown list`. For exact parity, port `content-schemas.ts`
  into `config/hub_content.php` (`games` map) and set `strict => true`.
- **Portable limits** from `catalog-limits.ts` live in `config/hub.php`
  (`limits`): name 40, tagline 60, description 160, 200 entries each.
- **SSO exchange is atomic** (`UPDATE … WHERE used_at IS NULL AND expires_at > UTC_TIMESTAMP(3)`),
  fixing the double-redeem race noted in `MIGRATION_GUIDE.md` §5.
- **Signup validation returns `400`** (the old API returned `500` for most
  validation errors by accident).
- **Guests have no id**: any `userId` a guest sends is ignored and stored `NULL`.
- **Admin cookie** is `hm_admin`, `Path=/api/admin`, `HttpOnly`,
  `SameSite=Strict`, `Max-Age=43200`. Because `SameSite=Strict`, serve the admin
  API on the **same registrable domain** as the web app (e.g. `api.happimynd.com`
  + `hub.happimynd.com`); otherwise the browser won't send the cookie. Lockout:
  5 failed logins per IP → `429` for 15 minutes.
- **CORS**: when the web app is on another origin, set `CORS_ORIGINS` and
  `CORS_ALLOW_CREDENTIALS=true`. Note that setting `CORS_ORIGINS` narrows CORS
  for the whole app (it replaces the default `*`), so include every origin that
  calls this API. `*.vercel.app`, `localhost` and `127.0.0.1` are always allowed
  for hub routes.
- **Server-side catalog read (§6.3):** change `src/lib/catalog.ts` to
  `GET {VITE_API_URL}/api/catalog` instead of reading the database directly.

---

## 6. Files added (all inside the Laravel app)

```
config/database.php                 (+ "hub" connection)
config/hub.php                      (admin, cache, limits, slug pattern)
config/hub_content.php              (publish schemas)
config/cors.php                     (env-driven origins + credentials)

routes/hub.php                      (health, track, content, admin)
routes/hub_auth.php                 (/api/auth/* and /auth/* alias)

app/Providers/RouteServiceProvider.php   (loads the hub routes)
app/Http/Kernel.php                      (hub group + aliases)
app/Exceptions/Handler.php               (hub JSON errors)

app/Http/Controllers/Hub/*.php      (Health, Auth, Sso, Tracking, Content, Admin, base)
app/Services/Hub/*.php              (HubAdminAuth, HubAdminStats, HubContentService, HubContentValidator)
app/Http/Middleware/Hub*.php        (SetUtcTimezone, AdminAuthenticate, NoStore)
app/Models/Hub/*.php                (six models)

database/migrations/hub/*.php       (six migrations)
```
