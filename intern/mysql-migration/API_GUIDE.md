# Games Hub API — integration guide (for the front end)

The hub backend now runs on the HappiMynd Laravel server (AWS EC2) with a MySQL
database. It implements the contract in `API_SPEC.md`. **Your front end should
only need `VITE_API_URL` changed** — every path and response shape matches what
you already call.

- **Base URL:** `https://happimynd.com` (set as `VITE_API_URL`, origin only, no trailing slash)
- **Paths:** all endpoints are under `/api/hub/...` (e.g. `${VITE_API_URL}/api/hub/catalog`)
- **Format:** JSON in/out, except the tracking endpoints and progress saves, which take a `text/plain` body

---

## 1. Point the app at it

```bash
# .env / Vercel env
VITE_API_URL=https://happimynd.com
```

Verify the API is up:

```bash
curl -s https://happimynd.com/api/hub/health
# {"status":"ok","service":"happimynd-games-hub","timestamp":"2026-10-14T08:00:14+00:00","dbConnected":true}
```

If `dbConnected` is `false`, the server can't reach MySQL — tell the backend owner.

---

## 2. Conventions

**Success** — `{ "success": true, ...data }`
**Error** — `{ "success": false, "message": "Human readable", "error": "same text" }`

The `message` is what you show the user. **Unknown route** →

```json
404 { "error": "NOT_FOUND", "message": "Cannot GET /api/hub/x" }
```

- **Times** are UTC ISO-8601 strings (e.g. `2026-10-14T08:00:55+00:00`).
- **IDs** are lowercase UUID strings.
- There are **no player sessions** — the API trusts the `userId` you send back.

---

## 3. Players

### `UserProfile`

```ts
interface UserProfile {
  id: string;        // uuid
  name: string;
  username: string;  // lowercase
  email: string;     // lowercase
  avatar: string;    // emoji, default "🧘"
  created_at: string;
}
```

### `POST /api/hub/auth/signup`

```jsonc
// request
{ "name": "Ada Lovelace", "username": "ada", "email": "ada@example.com", "password": "secret1", "avatar": "🦊" }
```

`201 { "success": true, "user": UserProfile }`
Validation errors return `400` with a friendly `message` (e.g. `"Please enter a valid email address."`, `Username "@ada" is already taken.`).

### `POST /api/hub/auth/login`

```jsonc
// request — loginIdentifier may be the username or the email
{ "loginIdentifier": "ada", "password": "secret1" }
```

`200 { "success": true, "user": UserProfile }`
`400` if fields are missing; `401 "Invalid email or password"` if wrong.

### `GET /api/hub/auth/me?userId=<uuid>`

Also accepts an `x-user-id: <uuid>` header instead of the query param.
`200 { "success": true, "user": UserProfile }` · `401` no id · `404` unknown user.

### `POST /api/hub/auth/logout`

`200 { "success": true }` (nothing is stored server-side).

---

## 4. Saved game progress

Card Memory (`card-memory-game`), One Line Draw (`one-line-draw`) and Emotional
Tetris (`emotional-tetris`) keep level/settings progress server-side for signed-in
players. One save per player per game; the save is an opaque JSON object the game
owns. Guests keep their save in the browser and never call these.

### `GET /api/hub/progress/:slug?userId=<uuid>`

`200 { "success": true, "data": { ... }, "updatedAt": "2026-10-14T08:00:55+00:00" }`
No save yet (or unreadable stored text) → `200 { "success": true, "data": null, "updatedAt": null }`.
`400 "Invalid userId."` · `404 "Invalid game."`. Sends `Cache-Control: no-store`.

The game calls this when it opens, then merges the server copy with the browser copy.

### `POST /api/hub/progress/:slug`

Sent like tracking — debounced (~1.5 s after the last change):

```ts
fetch(url, { method: "POST", keepalive: true,
  headers: { "Content-Type": "text/plain;charset=UTF-8" },
  body: JSON.stringify({ userId, data }) })  // data must be a JSON object
```

Success is **`204` with no body**. Errors: `400 "Invalid JSON."`, `404 "Invalid game."`,
`400 "Invalid userId."`, `400 "Invalid data."`, `400 "Save is too large."` (> 64 KB),
`404 "User not found."`. Last write wins.

---

## 5. Usage tracking

Send these with `fetch(url, { method: "POST", keepalive: true, headers: { "Content-Type": "text/plain;charset=UTF-8" }, body: JSON.stringify(...) })`.
Success is **`204` with no body**; validation failure is `400`. The client can ignore the response.

**Common fields**

| Field | Rule |
| --- | --- |
| `userType` | `"guest"` or `"registered"` |
| `userId` | required + UUID when `registered`; **ignored for guests** (stored `NULL`) |
| `gameSlug` | `/^[a-z0-9][a-z0-9-]{0,59}$/` |
| `sessionId` | a UUID |

### `POST /api/hub/track/event`

```jsonc
{ "type": "hub_open", "userType": "guest" }
```

`hub_open` is the only event type now; any other `type` is a `400`.

### `POST /api/hub/track/session/start`

```jsonc
{ "sessionId": "<uuid>", "gameSlug": "night-sky", "userType": "guest" }
```

### `POST /api/hub/track/session/end`

```jsonc
{
  "sessionId": "<uuid>", "gameSlug": "night-sky", "userType": "guest",
  "durationSec": 42, "endedBy": "done",
  "stats": [{ "label": "Score", "value": 10 }]
}
```

- `durationSec` is rounded and clamped to `0..21600`.
- `endedBy`: anything other than `"done"` counts as `"left"`.
- `stats`: at most 8 items; `label` ≤ 40 chars, `value` a number or a ≤ 24-char string.
- Safe to call more than once; a later `"left"` never overwrites a `"done"`.

---

## 6. Public content

### `GET /api/hub/catalog`

```json
{
  "success": true,
  "overrides": [
    { "slug": "night-sky", "name": null, "tagline": null, "description": null, "hidden": true }
  ]
}
```

`null` means "use the text coded in the app". Only games with an override are listed.
`Cache-Control: public, max-age=30`.

> Server-side page rendering: change `src/lib/catalog.ts` to fetch
> `${VITE_API_URL}/api/hub/catalog` instead of reading the database directly.

### `GET /api/hub/content/:slug`

```json
{
  "success": true,
  "collections": {
    "thoughts": [{ "text": "What if I mess this up?", "kind": "pass" }],
    "end-titles": ["All popped out"]
  }
}
```

Only customised lists appear; each value is the JSON exactly as stored.
`404` if the slug is malformed. `Cache-Control: public, max-age=30`.

---

## 7. Admin dashboard

The dashboard calls these with `credentials: "include"`. Auth is a cookie named
`hm_admin` (set on login). **It is `Path=/api/hub/admin` and `SameSite=Strict`, so the
admin API must be on the same site as the web app** — e.g. `happimynd.com`
(or a subdomain like `hub.happimynd.com`, which is same-site). On a totally
different domain the cookie won't be sent.

Every admin response sends `Cache-Control: no-store`.

| Method | Path | Result |
| --- | --- | --- |
| POST | `/api/hub/admin/login` | `200 { success: true }` + `Set-Cookie: hm_admin` |
| POST | `/api/hub/admin/logout` | `200`, clears the cookie |
| GET | `/api/hub/admin/session` | `{ success, configured, authenticated, email? }` (no login needed) |
| GET | `/api/hub/admin/overview?days=7\|30\|90&tz=<minutes>` | `{ success, overview }` |
| GET | `/api/hub/admin/catalog` | `{ success, overrides }` (uncached) |
| GET | `/api/hub/admin/content` | `{ success, content: [{ game, collection, items }] }` |
| PUT | `/api/hub/admin/publish` | `{ success, overrides, content }` |

**Login errors:** `401 "Incorrect email or password."` ·
`429 "Too many attempts. Try again in N min."` (5 fails / IP / 15 min) ·
`503 "Admin login isn't set up yet."`

**Protected endpoints** return `401 { "message": "Please sign in to the admin dashboard." }` without a valid cookie.

### `PUT /api/hub/admin/publish`

Everything is validated first, then written in one transaction — all of it goes live, or none of it does.

```ts
{
  catalog?: Array<{ slug: string; name: string | null; tagline: string | null;
                    description: string | null; hidden: boolean }>;      // max 200
  content?: Array<{ game: string; collection: string; items: unknown[] | null }>; // max 200
}
```

- **catalog:** text is trimmed with whitespace collapsed; an all-null, visible entry removes the row (back to default). Limits: name 40, tagline 60, description 160.
- **content:** `items: null` deletes the row (back to the built-in list). The shared `end-titles` / `end-messages` lists are validated server-side.
- **Failure:** `400 { success: false, message }` with the first problem, e.g. `"guess-my-answer · Questions (item 1): …"`.
- **Success:** `200 { success: true, overrides, content }`, both re-read fresh. Public caches are cleared.

### `overview` shape

`overview` contains `range` (`days`, local `from`/`to` dates), `totals`,
`daily` (one zero-filled entry per local day, oldest first) and `games`. `tz` is
the viewer's UTC-offset in minutes (`330` = IST), clamped to `-720..840`. Full
field list is in `API_SPEC.md` §5.

---

## 8. Quick smoke test

```bash
BASE=https://happimynd.com

curl -s $BASE/api/hub/health

curl -s -X POST $BASE/api/hub/auth/signup -H 'Content-Type: application/json' \
  -d '{"name":"Test User","username":"testuser1","email":"test1@example.com","password":"secret1"}'

curl -s -X POST $BASE/api/hub/auth/login -H 'Content-Type: application/json' \
  -d '{"loginIdentifier":"testuser1","password":"secret1"}'

curl -s -o /dev/null -w "track=%{http_code}\n" -X POST $BASE/api/hub/track/event \
  -H 'Content-Type: text/plain;charset=UTF-8' -d '{"type":"hub_open","userType":"guest"}'

curl -s $BASE/api/hub/catalog

# progress (use the id from the signup response)
curl -s "$BASE/api/hub/progress/card-memory-game?userId=<uuid>"
curl -s -o /dev/null -w "save=%{http_code}\n" -X POST $BASE/api/hub/progress/card-memory-game \
  -H 'Content-Type: text/plain;charset=UTF-8' \
  -d '{"userId":"<uuid>","data":{"savedAt":1}}'
```

---

## 9. Known differences from `API_SPEC.md`

1. **Per-game content validation is now strict.** The server ports
   `src/config/content-schemas.ts`, so publishing validates every game's lists
   (same rules and messages as the dashboard), including the `area`
   (control-cant-control) and `deck` (guess-my-answer) option lists.
2. **Signup validation returns `400`** (the old API returned `500` for most
   validation errors) — the front end only reads `message`, so no change needed.
3. **Guests never get an id stored**, even if one is sent (privacy decision kept).

If anything here disagrees with a response you actually get, send me the request
and response and I'll reconcile it.
