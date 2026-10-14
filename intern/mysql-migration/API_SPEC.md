# Games Hub API contract

**For:** whoever rebuilds the Games Hub backend on MySQL.

This is every HTTP endpoint the web app calls today, with the request, the response and the rules behind it. If the new API keeps these paths and shapes, the front end needs no changes beyond `VITE_API_URL`. The current implementation is the reference: `src/server/api-handler.ts` and the services it calls.

**Who calls what:**

| Client file                                               | Endpoints                                                           |
| --------------------------------------------------------- | ------------------------------------------------------------------- |
| `src/services/api.ts`                                     | `/api/auth/*`                                                       |
| `src/services/admin-api.ts`                               | `/api/admin/*`                                                      |
| `src/lib/analytics.ts`                                    | `/api/track/*`                                                      |
| `src/games/_shared/content.ts`                            | `/api/content/:slug`                                                |
| `src/lib/catalog.ts` (server-side, during page rendering) | `/api/catalog`, after migration; it currently reads the DB directly |

---

## 1. Conventions

- **Base path:** `/api/...`. `/auth/...` is accepted as an alias for `/api/auth/...`.
- **Format:** JSON in and out (`Content-Type: application/json`), except the tracking endpoints (§3), which receive `text/plain` bodies containing JSON.
- **Success:** `{ "success": true, ...data }`.
- **Error:** `{ "success": false, "message": "Human readable", "error": "same text" }` with an HTTP status. The front end shows `message` to the user, so keep messages friendly.
- **Unknown route:** `404 { "error": "NOT_FOUND", "message": "Cannot GET /api/x" }`.
- **Times:** stored and compared in UTC.
- **IDs:** lowercase UUID strings.

### CORS

Echo `Access-Control-Allow-Origin` (plus `Allow-Credentials: true`) when the request `Origin` is one of:

- a `CORS_ORIGIN` entry (comma-separated env var)
- the origin of any external game URL (`VITE_MEMORY_GAME_URL`, `VITE_ONE_LINE_DRAW_GAME_URL`, `VITE_EMOTIONAL_TETRIS_GAME_URL`)
- any `*.vercel.app` origin
- `http://localhost:*` or `http://127.0.0.1:*`

Also:

- No `Origin` header: send `*`.
- Always send `Allow-Methods: GET, POST, PUT, DELETE, OPTIONS` and `Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept`.
- `OPTIONS` returns `204`.

---

## 2. Players and SSO

### Shared objects

```ts
interface UserProfile {
  id: string; // UUID
  name: string;
  username: string; // lowercase
  email: string; // lowercase
  avatar: string; // emoji, default "🧘"
  created_at?: string;
}
```

### `GET /api/health`

Returns `200 { status: "ok", service, timestamp, dbConnected: boolean }`.

### `POST /api/auth/signup`

**Body:** `{ name, username, email, password, avatar? }`

**Validation, in order** (message text as today):

| Check                                          | Message                                                                                         |
| ---------------------------------------------- | ----------------------------------------------------------------------------------------------- |
| `name.trim()` at least 2 characters            | "Please enter a name of at least 2 characters."                                                 |
| `username` matches `/^[a-zA-Z0-9_]{3,20}$/`    | "Username must be 3-20 characters long and can only contain letters, numbers, and underscores." |
| `email` matches `/^[^\s@]+@[^\s@]+\.[^\s@]+$/` | "Please enter a valid email address."                                                           |
| `password` at least 6 characters               | "Password must be at least 6 characters long."                                                  |
| Username taken (case-insensitive)              | `Username "@<username>" is already taken.`                                                      |
| Email taken (case-insensitive)                 | "This email address is already registered. Please log in instead."                              |

**On success:**

- Store `name` trimmed, and `username` and `email` trimmed and lowercased.
- Store `password_hash = bcrypt(password, 10 rounds)` and `avatar` (default `🧘`).
- **Generate `id` in the API**; MySQL has no `gen_random_uuid()`.
- Return `201 { success: true, user: UserProfile }`.

**Status codes:** today, "already taken" and "already registered" return **400** and the other validation errors return **500**, an accident of the current error mapping. Return **400** for all validation errors in the new API; the front end only reads `message`.

### `POST /api/auth/login`

- **Body:** `{ loginIdentifier, password }`. `email` or `username` are also accepted in place of `loginIdentifier`.
- Matches username **or** email, case-insensitively, then runs `bcrypt.compare`.
- Missing fields: "Please enter your username or email address, and your password." (`500` today; return `400`)
- Wrong credentials, or no `password_hash`: `401`, "Invalid email or password".
- Success: `200 { success: true, user: UserProfile }`.

### `GET /api/auth/me?userId=<uuid>`

The `x-user-id` header is also accepted.

| Case         | Response                                   |
| ------------ | ------------------------------------------ |
| No ID given  | `401`                                      |
| No such user | `404`                                      |
| Found        | `200 { success: true, user: UserProfile }` |

The web app calls this on page load to refresh the stored profile. There are no player sessions today; the API trusts the ID it's given.

### `POST /api/auth/logout`

Returns `200 { success: true }`. Nothing is stored server-side.

### `POST /api/auth/sso/create`

- **Body:** `{ userId, gameId }`. Both required; the user must exist (`400` / "User not found.").
- Creates `sso_tickets` row: a `code` of 64 random hex characters (32 random bytes) and `expires_at = now + 120 s`.
- Returns `201 { success: true, code, expiresInSeconds: 120, gameId }`.

### `POST /api/auth/sso/exchange`

Called by external games, cross-origin.

- **Body:** `{ code }`.
- **Single use** within 120 seconds. Do the redeem as one atomic `UPDATE … WHERE used_at IS NULL AND expires_at > now`; see MIGRATION_GUIDE §5.

| Case                    | Response                                           |
| ----------------------- | -------------------------------------------------- |
| Valid                   | `200 { success: true, user: UserProfile, gameId }` |
| Missing or unknown code | `400`, "Unable to authenticate…"                   |
| Already used            | `400`, "This login link has already been used…"    |
| Expired                 | `400`, "This login session has expired…"           |

---

## 3. Usage tracking

The browser sends these with `fetch(url, { method: "POST", keepalive: true, headers: { "Content-Type": "text/plain;charset=UTF-8" }, body: JSON.stringify(...) })`.

- **Read the raw body and `JSON.parse` it.** `text/plain` avoids a CORS preflight and lets the request finish while the page closes.
- **Responses:** `204` with no body on success, `400 { success: false, message }` on validation failure. The client ignores responses.

**Shared validation:**

| Field       | Rule                                                                                           |
| ----------- | ---------------------------------------------------------------------------------------------- |
| `userType`  | `"guest"` or `"registered"`                                                                    |
| `userId`    | Required and a UUID when registered. **Must be NULL for guests**: ignore any ID a guest sends. |
| `gameSlug`  | `/^[a-z0-9][a-z0-9-]{0,59}$/`                                                                  |
| `sessionId` | A UUID                                                                                         |

### `POST /api/track/event`

- **Body:** `{ type: "hub_open" | "external_launch", userType, userId?, gameSlug? }`. `gameSlug` is required for `external_launch` and ignored for `hub_open`.
- Inserts into `activity_events`, with `id` generated by the server and `created_at` = server UTC time.

### `POST /api/track/session/start`

- **Body:** `{ sessionId, gameSlug, userType, userId? }`.
- Inserts into `game_sessions` (`started_at` = server UTC time).
- If the ID already exists (a retry), ignore it.

### `POST /api/track/session/end`

**Body:** `{ sessionId, gameSlug, userType, userId?, durationSec, endedBy: "done" | "left", stats? }`

**Cleaning the input:**

- `durationSec`: round it and clamp to `0..21600`.
- `endedBy`: anything other than `"done"` counts as `"left"`.
- `stats`: keep at most 8 items shaped `{ label: non-empty string (cut to 40 characters), value: finite number | string of 24 characters or fewer }`. Store as a JSON string, or `NULL` if none remain.

**Writing it** (it can be called more than once per session):

1. `UPDATE game_sessions SET ended_at = now, duration_sec, ended_by, stats WHERE id = ? AND (ended_by IS NULL OR ended_by <> 'done' OR ? = 'done')`. A later `"left"` never overwrites a `"done"`.
2. If no row changed and the ID doesn't exist (the start was lost), insert the full row with `started_at = now − durationSec`.

---

## 4. Public content

### `GET /api/catalog`

Admin edits to library cards. **Cache:** `Cache-Control: public, max-age=30`.

```json
{
  "success": true,
  "overrides": [
    { "slug": "night-sky", "name": null, "tagline": null, "description": null, "hidden": true }
  ]
}
```

`null` means "use the text coded in the app". `hidden` is a boolean (`TINYINT 1` → `true`). Only games with a row are listed.

### `GET /api/content/:slug`

Admin-edited in-game text for one game. The slug must match the pattern above, otherwise `404`. **Cache:** `public, max-age=30`.

```json
{
  "success": true,
  "collections": {
    "thoughts": [
      { "text": "What if I mess this up?", "kind": "pass", "tag": "What-if", "note": "…" }
    ],
    "end-titles": ["All popped out", "…"]
  }
}
```

- **Contents:** only customised lists appear. Each value is `JSON.parse(game_content.items)`, returned exactly as stored.
- **Bad data:** a row whose `items` isn't a valid JSON array is left out, so the game falls back to its built-in list.

---

## 5. Admin

### Authentication

There's a single admin, configured by env vars:

| Variable               | Contents                                                                                                                                                       |
| ---------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `ADMIN_EMAIL`          | Compared lowercased and trimmed                                                                                                                                |
| `ADMIN_PASSWORD_HASH`  | `pbkdf2:<iterations>:<saltHex>:<hashHex>`: PBKDF2-HMAC-SHA256, 100,000 iterations, 16-byte salt, 32-byte hash. Generated by `scripts/hash-admin-password.mjs`. |
| `ADMIN_SESSION_SECRET` | At least 32 characters                                                                                                                                         |

**Session cookie** `hm_admin`:

| Part        | Value                                                                               |
| ----------- | ----------------------------------------------------------------------------------- |
| Value       | `<payload>.<signature>`                                                             |
| `payload`   | base64url of `{"e":"<admin email>","x":<expiry ms epoch>}`. Expiry is 12 hours.     |
| `signature` | base64url of `HMAC-SHA256(ADMIN_SESSION_SECRET, payload)`                           |
| Attributes  | `Path=/api/admin; HttpOnly; SameSite=Strict; Max-Age=43200`, plus `Secure` on HTTPS |

- **Valid only if:** the signature verifies, `e` equals the current `ADMIN_EMAIL`, and `x` is in the future.
- **Lockout:** 5 failed logins from one IP lock it out for 15 minutes (`429`). The IP comes from `cf-connecting-ip`, or else the first `x-forwarded-for` entry.
- **Caching:** every admin response sends `Cache-Control: no-store`. Every endpoint except `login`, `logout` and `session` returns `401 { message: "Please sign in to the admin dashboard." }` without a valid cookie.

The web app sends requests with `credentials: "include"`. If the admin API moves to a different site than the web app, `SameSite=Strict` stops the cookie being sent; use the same registrable domain, or change the cookie policy and add CSRF protection.

### `POST /api/admin/login`

**Body:** `{ email, password }`. Always derive the hash, even for a wrong email, so response timing doesn't reveal which part was wrong.

| Case                    | Response                                        |
| ----------------------- | ----------------------------------------------- |
| Success                 | `200 { success: true }` + `Set-Cookie`          |
| Wrong email or password | `401`, "Incorrect email or password."           |
| Locked out              | `429`, "Too many attempts. Try again in N min." |
| Env vars missing        | `503`, "Admin login isn't set up yet…"          |

### `POST /api/admin/logout`

Returns `200` and clears the cookie (same attributes, `Max-Age=0`).

### `GET /api/admin/session`

No login needed. Returns `{ success: true, configured: boolean, authenticated: boolean, email?: string }`.

### `GET /api/admin/overview?days=7|30|90&tz=<minutes>`

- **`days`:** anything other than 7 or 30 or 90 becomes 30.
- **`tz`:** the viewer's UTC offset in minutes (e.g. `330` for IST), an integer between −720 and 840; otherwise 0.
- **Windows** (calculated in the viewer's timezone, then converted to UTC for queries):
  - `todayStart`: local midnight today
  - `since`: `todayStart − (days − 1) days`
  - week and month starts: same formula with 7 and 30.

**Response** (`{ success: true, overview }`):

```ts
interface Overview {
  range: { days: number; from: "YYYY-MM-DD"; to: "YYYY-MM-DD" }; // local dates
  totals: {
    registeredActive: number; // distinct registered user_id with hub_open since `since`
    activeToday: number; // same, since local midnight
    activeWeek: number; // last 7 local days
    activeMonth: number; // last 30 local days
    registeredVisits: number; // registered hub_open count in range
    guestVisits: number; // guest hub_open count in range
    signups: number; // users.created_at in range
    totalUsers: number; // COUNT(*) FROM users
    registeredPlays: number; // game_sessions started in range
    guestPlays: number;
    registeredPlayers: number; // distinct registered user_id in game_sessions in range
    playSeconds: number; // SUM(duration_sec)
    avgSessionSec: number | null; // playSeconds / sessions with a duration
    completionRate: number | null; // done / (done + left), 0..1
    externalLaunches: number;
  };
  daily: Array<{
    // one entry per local day in range, oldest first, zeros filled
    date: "YYYY-MM-DD";
    registeredActive: number;
    registeredVisits: number;
    guestVisits: number;
    registeredPlays: number;
    guestPlays: number;
    signups: number;
  }>;
  games: Array<{
    // only games with sessions in range
    slug: string;
    registeredPlays: number;
    guestPlays: number;
    registeredPlayers: number;
    totalSec: number;
    avgSec: number | null;
    completionRate: number | null;
  }>;
  external: Array<{ slug: string; registered: number; guest: number }>; // external_launch counts
}
```

**Daily bucket in MySQL:** `DATE_FORMAT(DATE_ADD(col, INTERVAL ? MINUTE), '%Y-%m-%d')`, with `?` = `tz`.

**Reference implementation:** `src/server/admin-stats.ts` and `readDashboard()` in `src/server/analytics-db.ts`.

### `GET /api/admin/catalog`

Same shape as `GET /api/catalog`, but uncached: `{ success: true, overrides }`.

### `GET /api/admin/content`

Every customised list: `{ success: true, content: [ { game, collection, items } ] }`, where `items` is the parsed array.

### `PUT /api/admin/publish`

The dashboard's **Save & publish**. Everything is validated first, then written in **one transaction**: all of it goes live, or none of it does.

**Request:**

```ts
{
  catalog?: Array<{ slug: string; name: string | null; tagline: string | null;
                    description: string | null; hidden: boolean }>;   // max 200
  content?: Array<{ game: string; collection: string; items: unknown[] | null }>;  // max 200
}
```

**Writing catalog entries:**

- Delete any existing `game_overrides` row for the slug.
- Insert a new row unless all text fields are `null` and `hidden` is false. All-null and visible means "back to default".
- Text is trimmed, with runs of whitespace collapsed to one space. Empty text is an error.
- Limits (characters): name 40, tagline 60, description 160 (`src/config/catalog-limits.ts`).

**Writing content entries:**

- `collection` must exist for that game in `src/config/content-schemas.ts` (`unknown list` otherwise). The `end-titles` and `end-messages` lists exist for every game.
- `items: null` deletes the row (back to the built-in list).
- Otherwise run `validateCollection()`. **Any error rejects the whole request.** Then delete and insert the row with `items = JSON.stringify(items)`.

**Responses:**

- Success: `200 { success: true, overrides, content }`, with both re-read fresh after the write.
- Failure: `400 { success: false, message }`, describing the first problem, e.g. `"guess-my-answer · Questions (item 1): Needs exactly 4 options."`
- Either way, clear any server caches for `/api/catalog` and `/api/content`.

### Content rules (what `validateCollection` checks)

The source of truth is `src/config/content-schemas.ts`. A Node/TypeScript backend should import it as-is; it has no dependencies beyond two game content files.

**Field types and checks:**

| Field type | Error                                                                                       | Warning             |
| ---------- | ------------------------------------------------------------------------------------------- | ------------------- |
| `text`     | Empty (unless optional); over `max`; missing a `mustInclude` placeholder such as `{name}`   | Over `soft`         |
| `emoji`    | Empty; over 16 characters; doesn't start with an emoji                                      |                     |
| `select`   | Value not one of its options                                                                |                     |
| `textList` | Not exactly `count` items; an empty item; an item over `max`; duplicates (case-insensitive) | An item over `soft` |

**List-level rules:**

- Item count must be within `minItems..maxItems` (error).
- Duplicate first text field is a warning.
- Game-specific `rules` produce errors or warnings, for example:
  - each Guess My Answer deck needs ≥ 6 questions
  - each Control area needs ≥ 3 situations
  - Thought Traffic needs at least one "pass" and one "park" thought.

**Warnings never block publishing.** Only errors do. The dashboard runs the same function before calling the API.

---

## 6. Caching and timing

| Data                                    | Cached where                                         | Duration                              |
| --------------------------------------- | ---------------------------------------------------- | ------------------------------------- |
| `/api/catalog`, `/api/content/:slug`    | HTTP `max-age=30`, plus the server's in-memory cache | 30 s; cleared on publish              |
| Published text in each player's browser | `localStorage`                                       | Refreshed whenever the game is opened |
| Catalog during page rendering           | Root loader                                          | 30 s                                  |

After a publish, visitors see changes within about 30 seconds, and games pick up new text the next time they're opened.
