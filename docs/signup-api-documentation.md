# HappiMynd Signup API — Individual & Organization

Documentation for the React/internee team. All endpoints are **JSON** (`Content-Type: application/json`).

- **Base URL:** `https://happimynd.com/api` (dev: use local `APP_URL`)
- **Auth:** Bearer JWT (only for endpoints marked 🔒)
- **Error responses:** HTTP `400/401/422` with a `message` field.
- `GET` and `POST` both work on all these endpoints (they read `$request`).

---

## 1. Signup flow overview

| Step | Endpoint | Purpose |
|---|---|---|
| 1 (optional) | `GET /api/v1/organizer-list` | List organizations for the "select your company" dropdown |
| 2 (optional) | `GET /api/v1/user-profile` | List "who are you" profile types |
| 3 (optional) | `GET /api/v1/language-list` | List selectable languages |
| 4 (optional, org) | `GET/POST /api/v1/entry-via-org` | Verify org happimynd code **before** signup |
| 4 (optional, mobile) | `GET/POST /api/v1/send-login-otp` + `verify-login-otp` | Verify mobile number before signup |
| 5 | `POST /api/v1/signup` | **Create account** (individual or organization) |

---

## 2. Prerequisite endpoints

### 2.1 Organization list
```
GET /api/v1/organizer-list
```
Response:
```json
{
  "message": "Organizer list get sucessfully.",
  "data": [ { "id": 1, "name": "Company A", ... } ]
}
```

### 2.2 User profiles ("who are you")
```
GET /api/v1/user-profile
```
Response:
```json
{
  "message": "User profile get sucessfully.",
  "data": [ { "id": 1, "name": "Salaried", "status": 1, ... } ]
}
```

### 2.3 Language list
```
GET /api/v1/language-list
```
Response:
```json
{
  "status": "success",
  "message": "Language list get successfully.",
  "data": [ { "id": 1, "name": "English", ... } ]
}
```

### 2.4 Verify organization code (before org signup, optional)
```
POST /api/v1/entry-via-org
```
Request body:
```json
{
  "organization_id": 1,
  "happimynd_code": "COMPANY_CODE"
}
```
Success (`200`):
```json
{ "status": "success", "message": "Code verified. Now you can signup now." }
```
Errors (`400`):
- `Invalid happimynd code.`
- `Max user limit for this code is over.`

### 2.5 Verify mobile number (before signup, optional)
Used to mark the signup account as `mobile_verified`.

**a) Send OTP**
```
POST /api/v1/send-login-otp
```
Request body:
```json
{
  "mobile": "9876543210",
  "country_code": "91",
  "type": "signup"
}
```
Response: `{ "status": "success", "message": "OTP has been sent to your mobile number." }`

**b) Verify OTP**
```
POST /api/v1/verify-login-otp
```
Request body:
```json
{ "mobile": "9876543210", "otp": "123456" }
```
- If the mobile already has an account → returns a JWT (`status: success` + `access_token`).
- If it is a **new** mobile → returns `status: "register"` plus a `mobile_verified_token`:
```json
{
  "status": "register",
  "mobile_verified_token": "<encrypted token, valid 30 min>"
}
```
Pass that `mobile_verified_token` (with the same `mobile`) into the signup call below to auto-mark the account mobile-verified.

---

## 3. Create account — `POST /api/v1/signup`

This single endpoint handles **both** individual and organization signup. They differ only by the `signup_type` field. The response returns the JWT needed for all authenticated calls.

### 3.1 Individual signup

**Request body:**
```json
{
  "signup_type": "individual",
  "nickname": "John",
  "user_profile_id": 2,
  "username": "john.doe",
  "password": "secret123",
  "confirm_password": "secret123",
  "language": 1,
  "age": 25,
  "gender": "male",
  "device_token": "ExponentPushToken[...]",
  "mobile": "9876543210",
  "mobile_verified_token": "<from verify-login-otp>",
  "referral_code": "REFCODE123"
}
```

### 3.2 Organization signup

Requires a valid `happimyndCode` (org code). The user is matched/updated by `username`.

**Request body:**
```json
{
  "signup_type": "organization",
  "happimyndCode": "COMPANY_CODE",
  "nickname": "John",
  "user_profile_id": 1,
  "username": "john.doe",
  "password": "secret123",
  "confirm_password": "secret123",
  "language": 1,
  "age": 25,
  "gender": "male",
  "device_token": "ExponentPushToken[...]",
  "mobile": "9876543210",
  "mobile_verified_token": "<from verify-login-otp>"
}
```

### 3.3 Field reference

| Field | Required | Rules / notes |
|---|---|---|
| `signup_type` | ✅ | `individual` or `organization` |
| `happimyndCode` | ✅ (org only) | Org access code; validated + token-assigned |
| `nickname` | ✅ | min:2, max:200 |
| `user_profile_id` | ✅ | from `/user-profile` |
| `username` | ✅ | must be unique (`unique:users,username`) |
| `password` | ✅ | min:6 |
| `confirm_password` | ✅ | min:6 (must match password) |
| `language` | ✅ | must exist in `user_languages` (id from `/language-list`) |
| `age` | ✅ (in UI) | integer |
| `gender` | ✅ (in UI) | `male` \| `female` \| `other` |
| `mobile` | optional | required together with `mobile_verified_token` |
| `mobile_verified_token` | optional | encrypted token from `/verify-login-otp` (valid 30 min); marks account `mobile_verify = 1` |
| `device_token` | optional | push notification token |
| `referral_code` | optional (individual) | rewards the referring user; invalid code → 400 |

> **Note:** In the current code the `age`/`gender` validation lines are commented out, but the app still sends them, and `gender` is used to pick the default avatar.

### 3.4 Success response (`200`)
```json
{
  "access_token": "<JWT>",
  "token_type": "bearer",
  "user": {
    "id": 45,
    "nickname": "John",
    "username": "john.doe",
    "language": "english",
    "platform": "mobile",
    "avatar": "male1.svg",
    "user_profile_id": 2,
    "happimynd_code": "COMPANY_CODE",
    "...": "..."
  }
}
```
Use the returned `access_token` as `Authorization: Bearer <JWT>` on all 🔒 endpoints.

### 3.5 Error responses
- `400`: `Please enter signup type` / `Please enter happimynd code.` / `Please enter nick name.` / `Username is already taken` / `Please select language.` etc.
- `400`: `Invalid referral code` (when a bad `referral_code` is supplied)
- `400`: `Invalid or expired mobile verification.` (when `mobile`/`mobile_verified_token` mismatch or token expired)

---

## 4. Flow to implement (recommended)

**Individual:**
1. Load `/user-profile` and `/language-list` for dropdowns.
2. If mobile verification is wanted: `/send-login-otp` → `/verify-login-otp` → store `mobile_verified_token`.
3. `POST /signup` with `signup_type: "individual"` + optional `mobile`, `mobile_verified_token`, `referral_code`.
4. On success, store the JWT and continue to the screening flow.

**Organization:**
1. Load `/organizer-list` and `/user-profile` + `/language-list`.
2. User enters a happimynd code → optional `/entry-via-org` to validate early (shows "code verified / invalid / max limit reached").
3. `POST /signup` with `signup_type: "organization"` + `happimyndCode`.
4. On success, store the JWT (org plan entitlements are granted via token assignment server-side).

---

## 5. Backend reference
- Controller: `app/Http/Controllers/api/v1/UserAuthenticationController.php`
  - `signup()` line 128 · `entryViaOrg()` line 336
  - `sendLoginOtp()` line 1002 · `verifyLoginOtp()` line 1052
- Routes: `routes/api.php`
- Related models: `User`, `Token`, `UserToken`, `UserProfile`, `UserLanguage`, `MobileOtp`, `VerifyUser`, `Organization`
