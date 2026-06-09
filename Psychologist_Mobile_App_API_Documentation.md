# HappiMynd Mobile App API Documentation

## Base URL
```
https://your-domain.com/api/v1
```

## Authentication

### User Authentication (JWT)
Most user endpoints require a valid JWT token in the `Authorization` header:
```
Authorization: Bearer {token}
```
Token is obtained via `login`, `signup`, or `login-with-code` endpoints.

### Psychologist Authentication (JWT)
Psychologist endpoints use the `psychologist` middleware with the same header format:
```
Authorization: Bearer {token}
```
Token is obtained via `psychologist-login`.

---

## Table of Contents

1. [Public / Onboarding APIs](#1-public--onboarding-apis)
2. [User Authentication APIs](#2-user-authentication-apis)
3. [User Profile & Account APIs](#3-user-profile--account-apis)
4. [User Assessment APIs](#4-user-assessment-apis)
5. [User Notification APIs](#5-user-notification-apis)
6. [User Payment & Subscription APIs](#6-user-payment--subscription-apis)
7. [User Chat (HappiBuddy) APIs](#7-user-chat-happibuddy-apis)
8. [User HappiLearn APIs](#8-user-happilearn-apis)
9. [User HappiSelf APIs](#9-user-happiself-apis)
10. [User HappiTalk APIs](#10-user-happitalk-apis)
11. [User HappiGuide APIs](#11-user-happiguide-apis)
12. [User Video Chat APIs](#12-user-video-chat-apis)
13. [User Rating APIs](#13-user-rating-apis)
14. [User White Labeling APIs](#14-user-white-labeling-apis)
15. [Psychologist Authentication APIs](#15-psychologist-authentication-apis)
16. [Psychologist Profile APIs](#16-psychologist-profile-apis)
17. [Psychologist Chat APIs](#17-psychologist-chat-apis)
18. [Psychologist HappiTalk APIs](#18-psychologist-happitalk-apis)
19. [Psychologist HappiGuide APIs](#19-psychologist-happiguide-apis)
20. [Psychologist User Reports APIs](#20-psychologist-user-reports-apis)
21. [ChatBot APIs](#21-chatbot-apis)
22. [ChatBot Assessment APIs](#22-chatbot-assessment-apis)
23. [Score & Prompt APIs](#23-score--prompt-apis)
24. [Payment Webhooks & Links](#24-payment-webhooks--links)
25. [Error Responses](#25-error-responses)

---

## 1. Public / Onboarding APIs

**Flow Context:** These are the first endpoints called when the app launches. They populate the landing/splash screens, signup forms, and info screens. No authentication required.

### 1.1 On-Off Status

**Endpoint:** `GET|POST /api/v1/on-off-status`

**Description:** Check if the app is open for registration.

**Flow:** Called on app launch (splash screen). If `is_open = 0`, show a "maintenance" screen and block further navigation. Used as a kill-switch by the admin.

**Response (200):**
```json
{
  "status": "success",
  "is_open": 1
}
```

---

### 1.2 Onboarding

**Endpoint:** `GET|POST /api/v1/onboarding`

**Description:** Placeholder onboarding endpoint. Returns `1`.

**Flow:** Called after splash screen if user is not logged in. Currently returns static value — reserved for future onboarding carousel content.

---

### 1.3 Organizer List

**Endpoint:** `GET|POST /api/v1/organizer-list`

**Description:** Get list of all available organizations.

**Flow:** Called on the signup screen when user selects `signup_type = "organization"`. Populates the organization dropdown. The selected org's `happimynd_code` is later verified via `entry-via-org`.

**Response (200):**
```json
{
  "message": "Organizer list get sucessfully.",
  "data": [
    {
      "id": 1,
      "name": "Organization Name",
      ...
    }
  ]
}
```

---

### 1.4 User Profile Types

**Endpoint:** `GET|POST /api/v1/user-profile`

**Description:** Get list of user profile types (e.g., Student, Professional, etc.).

**Flow:** Called on the signup screen. Populates a "I am a..." picker. The selected `user_profile_id` is sent in the signup request. Used both for individual and org signup flows.

**Response (200):**
```json
{
  "message": "User profile get sucessfully.",
  "data": [
    {
      "id": 1,
      "name": "Student",
      "status": 1,
      ...
    }
  ]
}
```

---

### 1.5 Language List

**Endpoint:** `GET|POST /api/v1/language-list`

**Description:** Get list of available languages.

**Flow:** Called on the signup screen to populate the language picker. The selected `language` ID is sent in the signup request. Also called on the profile edit screen.

**Response (200):**
```json
{
  "status": "success",
  "message": "Language list get successfully.",
  "data": [
    {
      "id": 1,
      "name": "English",
      ...
    }
  ]
}
```

---

### 1.6 Reward Instances List

**Endpoint:** `GET|POST /api/v1/reward-instances-list`

**Description:** Get all reward point instances (actions and their point values).

**Flow:** Called from a "Rewards info" or "How to earn points" screen. Read-only informational display — user cannot interact. Shows what actions earn how many points.

**Response (200):**
```json
{
  "status": "success",
  "message": "Reward instance get successfully.",
  "list": [
    {
      "id": 1,
      "action_performed": "When HappiLIFE Assessment is taken up",
      "points_to_be_given": 50,
      ...
    }
  ]
}
```

---

### 1.7 General FAQs

**Endpoint:** `GET|POST /api/v1/general-faqs`

**Description:** Get general FAQs (data_group_id = 4).

**Flow:** Shown on the "Help & Support" or "FAQ" screen. No authentication needed — accessible from login/signup screens as well as from inside the app.

**Response (200):**
```json
{
  "status": "success",
  "message": "General faqs get successfully.",
  "general_faqs": [
    {
      "id": 1,
      "title": "What is HappiMynd?",
      "description": "Answer content here...",
      "data_group_id": 4
    }
  ]
}
```

---

### 1.8 Organization FAQs

**Endpoint:** `GET|POST /api/v1/org-faqs`

**Description:** Get organization-specific FAQs (data_group_id = 5).

**Flow:** Called from the same FAQ screen but filtered for org-specific questions. Only relevant if user signed up via an organization.

**Response (200):**
```json
{
  "status": "success",
  "message": "Organization faqs get successfully.",
  "organization_faqs": [...]
}
```

---

### 1.9 Privacy Policy

**Endpoint:** `GET|POST /api/v1/privacy-policy`

**Description:** Get privacy policy content (data_group_id = 3).

**Flow:** Called from the signup screen ("By signing up, you agree to our Privacy Policy" link) and from the app settings screen. Read-only webview or scrollable content.

**Response (200):**
```json
{
  "status": "success",
  "message": "Privacy policy get successfully.",
  "data": [...]
}
```

---

### 1.10 Terms & Conditions

**Endpoint:** `GET|POST /api/v1/term-conditions`

**Description:** Get terms and conditions content (data_group_id = 6).

**Flow:** Called from the signup screen ("Terms of Service" link) and from the app settings screen. Read-only webview or scrollable content.

**Response (200):**
```json
{
  "status": "success",
  "message": "Terma nd conditions get successfully.",
  "data": [...]
}
```

---

### 1.11 Offer Screen Content

**Endpoint:** `GET|POST /api/v1/offer-screen-content`

**Description:** Get offer screen promotional content.

**Flow:** Called on the splash/landing screen before signup. Shows a promotional banner or offer carousel. Marked with `is_open` status to toggle visibility.

**Response (200):**
```json
{
  "status": "success",
  "message": "Offer Content get successfully.",
  "data": {
    "id": 1,
    "title": "Special Offer",
    "description": "Description here",
    "image": "image_url",
    ...
  }
}
```

---

## 2. User Authentication APIs

**Flow Context:** Entry gate for the app. No authentication required. These endpoints handle the complete user lifecycle: registration (with optional org/guardian flow), login (with device-aware notifications), and password recovery. Most return a JWT `access_token` that is stored and sent in subsequent requests.

### 2.1 Signup

**Endpoint:** `GET|POST /api/v1/signup`

**Description:** Register a new user. Supports individual and organization signup.

**Flow:** Called from the signup screen after the user fills in all required fields. 
- **Prerequisites:** `language-list`, `user-profile`, and optionally `organizer-list` + `entry-via-org` must have been called to populate the form.
- **Guardian flow:** If the user is a minor (<18), you must also call `guardian-verification` + `verify-guardian-otp` before or after signup.
- **On success:** Returns a JWT token — store it and navigate to the dashboard/home screen. Call `checkifany` to determine if assessment is needed.
- **Referral:** Optional `referral_code` — if invalid, request still succeeds but with a 400 warning in the response.

**Request Body:**
```json
{
  "signup_type": "individual|organization",
  "nickname": "JohnDoe",
  "user_profile_id": 1,
  "username": "johndoe",
  "password": "password123",
  "confirm_password": "password123",
  "language": 1,
  "happimyndCode": "ORGCODE123",
  "age": 25,
  "gender": "male|female|other",
  "device_token": "ExponentPushToken[xxxxxxxxxx]",
  "referral_code": "REFCODE123"
}
```

**Validation Rules:**
| Field | Rule |
|-------|------|
| signup_type | Required |
| nickname | Required, min:2, max:200 |
| user_profile_id | Required |
| username | Required, unique:users |
| password | Required, min:6 |
| confirm_password | Required, min:6 |
| language | Required, exists:user_languages,id |
| happimyndCode | Required if signup_type=organization |

**Response (Success - 200):**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "bearer",
  "user": {
    "id": 1,
    "nickname": "JohnDoe",
    "username": "johndoe",
    "email": null,
    "language": "english",
    "platform": "mobile",
    "device_token": "ExponentPushToken[xxxxxxxxxx]",
    "avatar": "male1.svg",
    ...
  }
}
```

**Response (Referral error - 400):**
```json
{
  "status": "error",
  "message": "Invalid referral code"
}
```

**Response (Validation error - 400):**
```json
{
  "message": "Please enter nick name."
}
```

---

### 2.2 Entry Via Organization

**Endpoint:** `GET|POST /api/v1/entry-via-org`

**Description:** Verify an organization's happimynd code before signup.

**Flow:** Called when user selects `signup_type = "organization"`. After entering the happimynd code, call this to validate it.
- **Prerequisites:** User selects an org from `organizer-list` results.
- **On success:** Allow proceeding to the signup form with `happimyndCode` field pre-filled.
- **On failure (max limit):** Show "Max user limit reached" — the org code has been fully redeemed.
- **On failure (invalid):** Show "Invalid code" — prompt user to re-enter.

**Request Body:**
```json
{
  "organization_id": 1,
  "happimynd_code": "ORGCODE123"
}
```

**Response (Success - 200):**
```json
{
  "status": "success",
  "message": "Code verified. Now you can signup now."
}
```

**Response (Max limit - 400):**
```json
{
  "status": "error",
  "message": "Max user limit for this code is over."
}
```

**Response (Invalid code - 400):**
```json
{
  "status": "error",
  "message": "Invalid happimynd code."
}
```

---

### 2.3 Login

**Endpoint:** `GET|POST /api/v1/login`

**Description:** Authenticate user with username and password.

**Flow:** Called from the login screen when user taps "Login".
- **On success:** Store the JWT token. Check `is_account_deleted` in response to prevent deleted accounts.
- **Device notification:** If the same user logs in from a different device (new `device_token`), a push notification is sent to the old device: "Is that you?" — for security awareness.
- **Post-login:** Call `get-profile` to load user data, then `checkifany` to see if the onboarding assessment needs to be taken.

**Request Body:**
```json
{
  "username": "johndoe",
  "password": "password123",
  "device_token": "ExponentPushToken[xxxxxxxxxx]"
}
```

**Validation Rules:** username (required), password (required, min:6)

**Response (Success - 200):**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "bearer",
  "user": {
    "id": 1,
    "nickname": "JohnDoe",
    "username": "johndoe",
    "device_token": "ExponentPushToken[xxxxxxxxxx]",
    ...
  }
}
```

**Response (Failed - 401):**
```json
{
  "status": "failed",
  "message": "Invalid username and password.",
  "error": "Unauthorized"
}
```

Notes:
- If the user logs in from a new device (different device_token), a notification is sent to the old device asking "Is that you?"
- If `is_account_deleted` = 1, login is blocked with 401

---

### 2.4 Login With Code

**Endpoint:** `GET|POST /api/v1/login-with-code`

**Description:** Login using a happimynd organization code (for users who signed up via org).

**Flow:** Alternative login for org users who may not remember their username. Enter the org code to login.
- **Prerequisites:** User must have signed up via an organization (their record must match the org code).
- **On success:** Returns a JWT token just like regular login.
- **Use case:** Quick access for employees/students using a shared org code.

**Request Body:**
```json
{
  "happimynd_code": "ORGCODE123",
  "device_token": "ExponentPushToken[xxxxxxxxxx]"
}
```

**Response (Success - 200):**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "bearer",
  "user": {
    "id": 1,
    "username": "johndoe",
    ...
  }
}
```

**Response (Failed - 400):**
```json
{
  "status": "error",
  "message": "Invalid happimynd code."
}
```

---

### 2.5 Forgot Password

**Endpoint:** `GET|POST /api/v1/forgot-password`

**Description:** Send OTP to user's email or mobile for password reset.

**Flow:** Called from the "Forgot Password" screen. Step 1 of 3 in the reset flow.
- **Step order:** `forgot-password` → `verify-otp` → `reset-password`
- The user provides either their registered email or mobile number.
- System sends a 6-digit OTP to that contact method.
- **On success:** Navigate to the OTP entry screen.

**Request Body (email):**
```json
{
  "type": "email",
  "email": "user@example.com"
}
```

**Request Body (mobile):**
```json
{
  "type": "mobile",
  "mobile": "9876543210"
}
```

**Response (Success - 200):**
```json
{
  "status": "success",
  "message": "OTP has been sent to your registered email address."
}
```

**or**
```json
{
  "status": "success",
  "message": "OTP has been sent to your registered mobile number."
}
```

**Response (Validation error - 400):**
```json
{
  "message": "Please enter registered email address."
}
```

---

### 2.6 Verify OTP

**Endpoint:** `GET|POST /api/v1/verify-otp`

**Description:** Verify the OTP sent for password reset.

**Flow:** Step 2 of 3 in the reset flow. Called from the OTP entry screen after user enters the code.
- **Prerequisites:** `forgot-password` must have been called first to send the OTP.
- **On success:** Navigate to the "New Password" screen.
- **On failure:** Show "Invalid OTP" — allow user to resend (call `forgot-password` again).

**Request Body (email verification):**
```json
{
  "email": "user@example.com",
  "otp": "123456"
}
```

**Request Body (mobile verification):**
```json
{
  "mobile": "9876543210",
  "otp": "123456"
}
```

**Response (Success - 200):**
```json
{
  "status": "success",
  "message": "OTP is verified"
}
```

**Response (Invalid OTP - 400):**
```json
{
  "status": "error",
  "message": "Invalid OTP"
}
```

---

### 2.7 Reset Password

**Endpoint:** `GET|POST /api/v1/reset-password`

**Description:** Set a new password after OTP verification.

**Flow:** Step 3 of 3 in the reset flow. Called after OTP is verified.
- **Prerequisites:** OTP must have been verified via `verify-otp` in the same session.
- **On success:** Navigate to the login screen with a success message.
- The same email/mobile from previous steps is used to identify the user.

**Request Body (email):**
```json
{
  "email": "user@example.com",
  "password": "newPassword123",
  "confirm_password": "newPassword123"
}
```

**Request Body (mobile):**
```json
{
  "mobile": "9876543210",
  "password": "newPassword123",
  "confirm_password": "newPassword123"
}
```

**Response (Success - 200):**
```json
{
  "status": "success",
  "message": "Password has been reset successfully."
}
```

---

### 2.8 Guardian Verification

**Endpoint:** `GET|POST /api/v1/guardian-verification`

**Description:** Send OTP to guardian's email or mobile for verification.

**Flow:** Called during signup when the user is a minor (age < 18). Sends an OTP to the guardian's contact for parental consent.
- **Prerequisites:** Must have a `random_unique_id` generated client-side to track this session.
- **On success:** Navigate to the guardian OTP entry screen.
- The `random_unique_id` ties the guardian verification to the user's signup session.

**Request Body (email):**
```json
{
  "type": "email",
  "random_unique_id": "UNIQUE_SESSION_ID",
  "email": "guardian@example.com"
}
```

**Request Body (mobile):**
```json
{
  "type": "mobile",
  "random_unique_id": "UNIQUE_SESSION_ID",
  "mobile": "9876543210"
}
```

**Response (Success - 200):**
```json
{
  "status": "success",
  "message": "Verification OTP has been sent to provided email address."
}
```

---

### 2.9 Verify Guardian OTP

**Endpoint:** `GET|POST /api/v1/verify-guardian-otp`

**Description:** Verify guardian's OTP.

**Flow:** Called after the guardian enters the OTP they received. Completes the parental consent flow.
- **Prerequisites:** `guardian-verification` must have been called first with the same `unique_id`.
- **On success:** Minors can proceed with signup.
- **On failure:** Allow retry with a new OTP.

**Request Body:**
```json
{
  "otp": "123456",
  "unique_id": "UNIQUE_SESSION_ID"
}
```

**Response (Success - 200):**
```json
{
  "status": "success",
  "message": "Otp verified successfully."
}
```

---

## 3. User Profile & Account APIs

All endpoints in this section require **authentication** (`auth:api` middleware).

**Flow Context:** These are the post-login user management endpoints. Called from the home screen, settings, and profile screens. Most require a valid JWT token obtained from login/signup.

### 3.1 Check (Validate Token)

**Endpoint:** `GET|POST /api/v1/check`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called on app launch to validate the stored JWT token. If this returns a non-200 response, clear the token and redirect to the login screen.

**Response (200):**
```json
34
```

> Note: Currently returns a static integer `34`.

---

### 3.2 Get Profile

**Endpoint:** `GET|POST /api/v1/get-profile`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called after login and any time the profile screen is opened. Populates the user's profile page. Also called by other screens that need user data (e.g., assessment, chat, payment). The `VerifyUser` object shows which contact methods are verified.

**Response (200):**
```json
{
  "status": "success",
  "message": "User detials get successfully.",
  "data": {
    "id": 1,
    "nickname": "JohnDoe",
    "username": "johndoe",
    "email": "user@example.com",
    "mobile": "9876543210",
    "age": 25,
    "gender": "male",
    "language": "english",
    "avatar": "male1.svg",
    "refer_code": "a12b34c",
    "device_token": "ExponentPushToken[xxxxxxxxxx]",
    "profileType": {
      "id": 1,
      "name": "Student"
    },
    "VerifyUser": {
      "id": 1,
      "user_id": 1,
      "email_verify": 1,
      "mobile_verify": 0
    },
    "userToken": {
      "id": 1,
      "user_id": 1,
      "token_id": 1
    }
  }
}
```

---

### 3.3 Edit Profile

**Endpoint:** `GET|POST /api/v1/edit-profile`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called from the "Edit Profile" screen. Send only the fields that changed. 
- **Email/Mobile first-time addition:** Awards reward points to the user.
- **Avatar:** Auto-assigned based on gender — no manual upload endpoint exists.
- **Username uniqueness:** Checked across all users (except the current user).

**Request Body:**
```json
{
  "nickname": "NewNickname",
  "age": 26,
  "gender": "male",
  "username": "newusername",
  "email": "newemail@example.com",
  "mobile": "9876543211"
}
```

**Validation Rules:**
| Field | Rule |
|-------|------|
| nickname | Required, min:2, max:200 |
| age | Required |
| gender | Required |
| username | Required, unique:users (except current user) |

**Response (Success - 200):**
```json
{
  "status": "success",
  "message": "Profile has been updated sucessfully.",
  "data": {
    "nickname": "NewNickname",
    "age": 26,
    "gender": "male",
    "username": "newusername",
    "email": "newemail@example.com",
    "mobile": "9876543211",
    "avatar": "male1.svg"
  }
}
```

Notes:
- Adding email/mobile for the first time grants reward points
- Email uniqueness is checked across all users
- Mobile uniqueness is checked across all users
- Avatar is auto-set based on gender (`male1.svg`, `female1.svg`, or `female1.svg` for other)

---

### 3.4 Save Email

**Endpoint:** `GET|POST /api/v1/save-email`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Quick standalone email save from the profile screen (email-only edit). Unlike `edit-profile` which handles all fields, this is used when the user only wants to add/change their email.

**Request Body:**
```json
{
  "email": "user@example.com"
}
```

**Response (Success - 200):**
```json
{
  "status": "success",
  "message": "Email save successfully"
}
```

**Response (Email taken - 400):**
```json
{
  "status": "error",
  "message": "Email already taken"
}
```

---

### 3.5 Change Password

**Endpoint:** `GET|POST /api/v1/change-password`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called from the "Change Password" screen in settings. Requires the user's current password for verification. The user must be logged in (valid JWT).

**Request Body:**
```json
{
  "old_password": "oldPassword123",
  "new_password": "newPassword123",
  "confirm_password": "newPassword123"
}
```

**Validation Rules:**
| Field | Rule |
|-------|------|
| old_password | Required |
| new_password | Required |
| confirm_password | Required, same:new_password |

**Response (Success - 200):**
```json
{
  "status": "success",
  "message": "Password has been changed successfully."
}
```

**Response (Invalid old password - 400):**
```json
{
  "status": "error",
  "message": "Please enter valid old password."
}
```

---

### 3.6 Send Verification OTP

**Endpoint:** `GET|POST /api/v1/send-verification-otp`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called when the user wants to verify their email or mobile (from profile screen "Verify" button). Sends a 6-digit OTP. The verification status is tracked in the `VerifyUser` object from `get-profile`.
- **Mobile with country_code:** If `country_code` is null or `"91"`, domestic SMS gateway is used. Otherwise, international gateway is used.
- **After OTP verification:** There is no separate "verify OTP" endpoint — the verification is typically handled via the same OTP check flow as password reset, or the system treats the contact as verified when the user successfully receives the OTP.

**Request Body (mobile):**
```json
{
  "type": "mobile",
  "mobile": "9876543210",
  "country_code": "91"
}
```

**Request Body (email):**
```json
{
  "type": "email",
  "email": "user@example.com"
}
```

**Response (Success - 200):**
```json
{
  "status": "success",
  "message": "OTP has been sent to given mobile number."
}
```

**Response (Already exists - 400):**
```json
{
  "status": "error",
  "message": "Mobile number is already exist."
}
```

---

### 3.7 Logout

**Endpoint:** `GET|POST /api/v1/logout`

**Headers:** `Authorization: Bearer {token}`

**Description:** Logout user and clear device token.

**Flow:** Called when the user taps "Logout". Clears the JWT and `device_token` on the server so push notifications are no longer sent to this device. On the client side, also clear the stored JWT token and navigate to the login screen.

**Response (200):**
```json
{
  "status": "success",
  "message": "User logged out successfully"
}
```

---

### 3.8 Delete Account

**Endpoint:** `GET|POST /api/v1/delete-account`

**Headers:** `Authorization: Bearer {token}`

**Description:** Soft-delete user account by setting `is_account_deleted = 1`.

**Flow:** Called from the "Delete Account" option in settings (with a confirmation dialog beforehand). Sets `is_account_deleted = 1` — the account is hidden and login is blocked but data is not permanently removed (soft delete).

**Response (200):**
```json
{
  "status": "success",
  "message": "Account has been deleted Successfully."
}
```

---

### 3.9 My Referral Code

**Endpoint:** `GET|POST /api/v1/my-referral-code`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get or generate user's referral code.

**Flow:** Called from the "Refer & Earn" or "Share" screen to display the user's unique referral code. The user can share this code with friends to earn reward points. Code format: `[letter][day][letter][month][letter]`.

**Response (200):**
```json
{
  "status": "success",
  "message": "Referral code get successfully.",
  "code": "a12b34c"
}
```

---

### 3.10 Mood Emoji List

**Endpoint:** `GET|POST /api/v1/mood-emoji-list`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get list of mood meter emojis sorted by predefined order.

**Flow:** Called when user opens the mood meter (daily check-in) screen. Loads available emoji options. Should be called before showing the mood input UI so the user can select from these.

**Response (200):**
```json
{
  "status": "success",
  "message": "Mood emoji list get successfully.",
  "data": [
    {"id": 1, "name": "Delighted", "emoji": "😄"},
    {"id": 2, "name": "Happy", "emoji": "😊"},
    {"id": 3, "name": "Confused", "emoji": "😕"},
    {"id": 4, "name": "Disappointed", "emoji": "😞"},
    {"id": 5, "name": "Sad", "emoji": "😢"},
    {"id": 6, "name": "Angry", "emoji": "😠"},
    {"id": 7, "name": "Crying", "emoji": "😭"},
    {"id": 8, "name": "Scared", "emoji": "😨"},
    {"id": 9, "name": "Anxious", "emoji": "😰"}
  ]
}
```

---

### 3.11 User Mood (Punch In)

**Endpoint:** `GET|POST /api/v1/user-mood`

**Headers:** `Authorization: Bearer {token}`

**Description:** Record user's daily mood.

**Flow:** Called when the user submits their daily mood check-in. 
- **Prerequisites:** `mood-emoji-list` should have been called first to populate the emoji picker.
- **Safety check:** If the last 6 moods were all negative (`emoji_id` NOT 1 or 5) and the current mood is also non-positive, the system sends a push notification suggesting they talk to their HappiBuddy.
- **Frequency:** Once per day is typical — the backend may reject duplicate same-day entries.

**Request Body:**
```json
{
  "emoji_id": 1,
  "text": "Feeling great today!"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Mood has been recorded."
}
```

---

### 3.12 Raise Query

**Endpoint:** `GET|POST /api/v1/raise-query-app`

**Headers:** `Authorization: Bearer {token}`

**Description:** Submit a support query from the app.

**Flow:** Called from the "Help & Support" → "Raise a Query" screen. The user selects a category and writes their issue. No file attachment support — text only. The query is stored in the admin panel for review.

**Request Body:**
```json
{
  "category": "Technical Issue",
  "description": "I am facing an issue with..."
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Quey has been raised successfully."
}
```

---

### 3.13 Feedback

**Endpoint:** `GET|POST /api/v1/feedback`

**Headers:** `Authorization: Bearer {token}`

**Description:** Submit app feedback with emoji rating.

**Flow:** Called from the "Rate Us" or "Feedback" screen. The user picks an emoji rating and optionally writes a message.
- **Reward:** First feedback submission awards reward points to the user.
- **Note:** This is different from `submit-rating` (section 13.2) — feedback is about the app itself, while rating is about a psychologist session.

**Request Body:**
```json
{
  "application_rate_emoji_id": 3,
  "feedback_message": "Great app! Really helpful."
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Feedback has been submit successfully.",
  "data": {
    "id": 1,
    "user_id": 1,
    "application_rate_emoji_id": 3,
    "feedback_message": "Great app! Really helpful."
  }
}
```

> Note: Reward points are given on first feedback submission.

---

### 3.14 Total Reward Points

**Endpoint:** `GET|POST /api/v1/total-reward-points-user`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get total reward points earned by the user.

**Flow:** Called from a "Rewards" or "My Points" screen to display the user's accumulated reward points. Points are earned for actions like completing assessments, sending messages, giving feedback, and more (see `reward-instances-list`).

**Response (200):**
```json
{
  "status": "success",
  "message": "Reward points get successfully.",
  "total_reward_points": 250
}
```

---

## 4. User Assessment APIs

All endpoints require **authentication** (`auth:api` middleware).

**Flow Context:** The HappiLIFE Awareness Tool is the core onboarding assessment. **Call order:** `checkifany` → `start-assessment` → `save-option` (repeated) → `complete-assessment` → `view-report` / `get-report`. Users can take the assessment up to 6 times max.

### 4.1 Check If Any Assessment Completed

**Endpoint:** `GET|POST /api/v1/checkifany`

**Headers:** `Authorization: Bearer {token}`

**Description:** Check if user has completed at least one assessment.

**Flow:** Called right after login/signup to determine whether to show the assessment screen or the home screen.
- **"Yes":** User has already done the assessment → show home screen
- **"No":** First-time user → redirect to assessment

**Response (Yes - 200):**
```json
{
  "status": "success",
  "message": "Yes"
}
```

**Response (No - 200):**
```json
{
  "status": "success",
  "message": "No"
}
```

---

### 4.2 Start Assessment

**Endpoint:** `GET|POST /api/v1/start-assessment`

**Headers:** `Authorization: Bearer {token}`

**Description:** Start or resume the HappiLIFE Awareness Tool assessment.

**Flow:** Called when the user begins the assessment. Returns the first page of questions (paginated, 5 per page).
- **Prerequisites:** `checkifany` should have been called to confirm the user needs to take an assessment.
- **If max assessments reached (6):** Returns a blocking message — user cannot take more.
- **If resuming:** Returns questions starting from where the user left off (based on `update-last-answer`).
- **Post-call:** The app renders the first batch of questions; user selects answers and calls `save-option` for each.

**Request Body:**
```json
{
  "platform": "mobile"
}
```

**Response (Success - 200):**
```json
{
  "status": "success",
  "message": "Questions get sucessfully.",
  "questions": [
    {
      "id": 1,
      "question": "How often have you felt upset...?",
      "options": [
        {"id": 1, "option": "Never", "score": 0},
        {"id": 2, "option": "Almost Never", "score": 1}
      ]
    }
  ],
  "overview": {
    "perPage": 5,
    "answered": 3,
    "total": 10,
    "current_page": 1
  }
}
```

**Response (Max limit reached - 200):**
```json
{
  "status": "true",
  "message": "You have already completed the maximum number of assessments."
}
```

> Note: Max 6 assessments allowed per user. Platform defaults to "website" if not provided.

---

### 4.3 Save Option

**Endpoint:** `GET|POST /api/v1/save-option`

**Headers:** `Authorization: Bearer {token}`

**Description:** Save answer for a question during assessment.

**Flow:** Called each time the user selects an answer during the assessment. This is a step-by-step save — one question at a time.
- **Prerequisites:** `start-assessment` must have been called to get current questions.
- **Input:** The `option_question_id` is the ID of the selected option (from the options list returned by `start-assessment`).
- **Post-call:** After saving, the app can either load more questions (pagination) or proceed to `complete-assessment` when done.

**Request Body:**
```json
{
  "option_question_id": 42
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Option saved successfully."
}
```

---

### 4.4 Complete Assessment

**Endpoint:** `GET|POST /api/v1/complete-assessment`

**Headers:** `Authorization: Bearer {token}`

**Description:** Mark the current assessment as completed.

**Flow:** Called after all questions have been answered (last `save-option` done). Triggers score calculation and generates the report.
- **Prerequisites:** All questions must be answered via `save-option`.
- **Reward:** Reward points are added once (only if no incomplete assessment remains — e.g., if user had abandoned a previous one).
- **Post-call:** Navigate to the report screen. Call `view-report` or `get-report` to see results.

**Response (200):**
```json
{
  "status": "success",
  "message": "Screening has been completed successfully."
}
```

---

### 4.5 View Report

**Endpoint:** `GET|POST /api/v1/view-report`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get the assessment report URL.

**Flow:** Called after `complete-assessment` to get the report link. This returns a web URL that opens the report in a webview/browser.
- **Alternative:** Use `get-report` (4.6) for a downloadable PDF version.
- **Prerequisites:** Assessment must be completed.

**Response (200):**
```json
{
  "status": "success",
  "link": "https://your-domain.com/calculate-score?assessment_id=1"
}
```

---

### 4.6 Get Report

**Endpoint:** `GET|POST /api/v1/get-report`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get the latest assessment PDF report URL. If report hasn't been generated, it calls a Node.js service to generate it.

**Flow:** Called from the report screen to get a downloadable PDF. If the PDF hasn't been generated yet, the backend triggers an external Node.js service to create it (may take a few seconds).
- **Prerequisites:** Assessment must be completed.
- **Difference from `view-report`:** This returns a downloadable S3 PDF URL; `view-report` returns a web link for in-app viewing.

**Response (Success - 200):**
```json
{
  "status": "success",
  "message": "Report get successfully.",
  "url": "https://s3.amazonaws.com/reports/1_johndoe-ScreeningReport.pdf"
}
```

**Response (Not completed - 400):**
```json
{
  "status": "error",
  "message": "Assesment is  not completed yet."
}
```

---

### 4.7 Get All Reports

**Endpoint:** `GET|POST /api/v1/get-all-report`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get all completed assessment reports for the user.

**Flow:** Called from a "My Reports" or "Assessment History" screen. Shows all past assessment reports with their completion dates. The user can tap any report to view it.

**Response (200):**
```json
{
  "status": "success",
  "message": "Reports fetched successfully.",
  "data": [
    {
      "id": 1,
      "report": "https://s3.amazonaws.com/reports/...pdf",
      "ended_at": "2024-01-15 10:30:00",
      "created_at": "2024-01-15 10:00:00"
    }
  ]
}
```

---

### 4.8 Assessment Status

**Endpoint:** `GET|POST /api/v1/assessment-status`

**Headers:** `Authorization: Bearer {token}`

**Description:** Check if the user's assessment is complete.

**Flow:** Similar to `checkifany` but returns HTTP status codes (200 = complete, 400 = not complete) rather than a JSON message. Useful for conditional navigation logic.

**Response (Completed - 200):**
```json
{
  "status": "success",
  "message": "Assesment is completed."
}
```

**Response (Not complete - 400):**
```json
{
  "status": "error",
  "message": "Assesment is not complete."
}
```

---

### 4.9 Update Last Answer

**Endpoint:** `GET|POST /api/v1/update-last-answer`

**Headers:** `Authorization: Bearer {token}`

**Description:** Update the last submitted answer in the assessment.

**Flow:** Called when the user wants to change their answer to the most recent question (e.g., tapping "back" to review/change). Only updates the last saved answer — not arbitrary questions.
- **Prerequisites:** `save-option` must have been called at least once.
- **Use case:** "Go back and change my last answer" button on the assessment screen.

**Request Body:**
```json
{
  "option_question_id": 55
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Answer has been update successfully."
}
```

---

## 5. User Notification APIs

All endpoints require **authentication** (`auth:api` middleware).

**Flow Context:** Notifications are generated server-side (session reminders, mood alerts, psychologist messages, etc.). These endpoints let the user fetch and manage their in-app notification list. Notifications use Expo Push for push delivery and are stored locally in the `notifications` table.

### 5.1 Notification List

**Endpoint:** `GET|POST /api/v1/notification-list`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get all notifications for the user.

**Flow:** Called from the notification bell/inbox screen. Returns all notifications with read/unread status. The `is_read` field determines whether to show a badge/dot indicator on the list item.

**Response (200):**
```json
{
  "status": "success",
  "message": "Notification list get successfully.",
  "list": [
    {
      "id": 1,
      "user_id": 1,
      "message": "Your session starts in 10 minutes",
      "is_read": 0,
      "created_at": "2024-01-15T10:00:00.000000Z",
      "updated_at": "2024-01-15T10:00:00.000000Z"
    }
  ]
}
```

---

### 5.2 Read Single Notification

**Endpoint:** `GET|POST /api/v1/read-single-notification`

**Headers:** `Authorization: Bearer {token}`

**Description:** Mark a single notification as read.

**Flow:** Called when the user taps on a specific notification to view its details. Sets `is_read = 1` for that notification.

**Request Body:**
```json
{
  "notification_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Notification read successfully.",
  "data": {
    "id": 1,
    "is_read": 1,
    ...
  }
}
```

---

### 5.3 Read All Notifications

**Endpoint:** `GET|POST /api/v1/read-all-notification`

**Headers:** `Authorization: Bearer {token}`

**Description:** Mark all notifications as read.

**Flow:** Called when the user taps "Mark all as read" on the notification list screen. Bulk update — sets `is_read = 1` for all unread notifications belonging to the user.

**Response (200):**
```json
{
  "status": "success",
  "message": "All notifications read successfully."
}
```

---

## 6. User Payment & Subscription APIs

All endpoints require **authentication** (`auth:api` middleware).

**Flow Context:** Payment flow is a multi-step process:
1. Call `buy-plan` to see available plans and subscription status
2. (Optional) Call `apply-coupon` to get a discount
3. Call `payment` (or `payment-for-happitalk` / `payment-for-happiguide`) to create a RazorPay order
4. Redirect user to the RazorPay checkout (via the `payment-link` URL)
5. RazorPay sends a `handle-webhook` notification
6. User is redirected back to `success-payment-page` on completion
7. Call `my-subscribed-services` to verify subscription

### 6.1 Buy Plan (Get Available Plans)

**Endpoint:** `GET /api/v1/buy-plan`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get all available packages/plans for purchase. Shows subscription status.

**Flow:** Called from the "Plans" or "Subscription" screen to display available packages. The `is_subscribed` field indicates the user's current status for each package:
- `0` = Not subscribed → show "Buy" button
- `1` = Subscribed → show "Subscribed" badge
- `2` = Included via organization → show "Included" badge (no purchase needed)

**Response (200):**
```json
{
  "status": "success",
  "message": "Packages get successfully.",
  "data": [
    {
      "id": 1,
      "name": "HappiBUDDY",
      "description": "...",
      "is_subscribed": 0,
      "mobilePlans": [
        {
          "id": 1,
          "package_id": 1,
          "name": "Monthly",
          "price": 299,
          ...
        }
      ]
    }
  ]
}
```

> `is_subscribed` values: 0 = not subscribed, 1 = subscribed, 2 = included via organization token

---

### 6.2 Create Payment

**Endpoint:** `GET|POST /api/v1/payment`

**Headers:** `Authorization: Bearer {token}`

**Description:** Create a RazorPay order for plan purchase.

**Flow:** Step 2 of the payment flow (after selecting a plan). Creates a RazorPay order on the server and returns a redirect link to the RazorPay checkout page.
- **Prerequisites:** User selected a plan from `buy-plan` results. Optionally applied a coupon via `apply-coupon`.
- **On success:** Open the returned `link` URL in a webview/browser to complete payment via RazorPay.
- **Post-payment:** RazorPay calls `handle-webhook`. User is redirected to `success-payment-page`. Then call `my-subscribed-services` to confirm.

**Request Body:**
```json
{
  "plan_id": 1,
  "amount": 299,
  "coupen_id": 5
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Payment link get successfully.",
  "link": "https://your-domain.com/api/v1/payment-link/{order_id}/{user_id}/{plan_id}/{coupen_id}"
}
```

---

### 6.3 Payment for HappiTalk

**Endpoint:** `GET|POST /api/v1/payment-for-happitalk`

**Headers:** `Authorization: Bearer {token}`

**Description:** Create a RazorPay order for HappiTalk session booking.

**Flow:** Similar to 6.2 but specifically for HappiTalk video sessions. Includes psychologist and scheduling info in the order.
- **Prerequisites:** User must have selected a psychologist and time slot from `psychologist-listing` and `get-slots-of-psy`.
- **On success:** Opens the RazorPay checkout with session details embedded in the return URL.
- **Post-payment:** Redirected to `success-payment-page-for-happitalk` which creates the booking and sends notifications.

**Request Body:**
```json
{
  "psychologist_id": 1,
  "plan_id": 5,
  "amount": 500,
  "date": "2024-01-20",
  "time": "10:00 AM - 11:00 AM",
  "session": 5,
  "user_recording_permission": 1,
  "coupen_id": 0
}
```

**Validation Rules:**
| Field | Rule |
|-------|------|
| psychologist_id | Required |
| plan_id | Required |
| amount | Required |
| date | Required |
| time | Required |
| session | Required |
| user_recording_permission | Required |

**Response (200):**
```json
{
  "status": "success",
  "message": "Payment link get successfully.",
  "link": "https://your-domain.com/api/v1/payment-link-for-happitalk/{order_id}/{user_id}/{plan_id}/{psychologist_id}/{date}/{time}/{session}/{user_recording_permission}/{coupen_id}"
}
```

---

### 6.4 Payment for HappiGuide

**Endpoint:** `GET|POST /api/v1/payment-for-happiguide`

**Headers:** `Authorization: Bearer {token}`

**Description:** Create a RazorPay order for HappiGuide session.

**Flow:** Creates a payment order for a HappiGuide (guided wellness) session. No `psychologist_id` is needed — the backend assigns one via round-robin after payment.
- **Error case:** If no psychologist is mapped to HappiGuide, returns 400 with "No psychologist map with HappiGuide".
- **Post-payment:** Redirected to `success-payment-page-for-happiguide` which assigns the psychologist and creates the session.

**Request Body:**
```json
{
  "plan_id": 6,
  "amount": 300,
  "date": "2024-01-20",
  "time": "10:00 AM - 11:00 AM",
  "coupen_id": 0
}
```

**Validation Rules:**
| Field | Rule |
|-------|------|
| plan_id | Required |
| amount | Required |
| date | Required |
| time | Required |

**Response (200):**
```json
{
  "status": "success",
  "message": "Payment link get successfully.",
  "link": "https://your-domain.com/api/v1/payment-link-for-happiguide/{order_id}/{user_id}/{plan_id}/{date}/{time}/{coupen_id}"
}
```

**Response (No psychologist - 400):**
```json
{
  "status": "error",
  "message": "No psychologist map with HappiGuide"
}
```

---

### 6.5 My Subscribed Services

**Endpoint:** `GET|POST /api/v1/my-subscribed-services`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get list of services the user has subscribed to.

**Flow:** Called after payment to verify the subscription is active, or from a "My Services" screen to show what's available. Also used to determine which features to unlock in the UI (e.g., show/hide HappiTalk if not subscribed).

**Response (200):**
```json
{
  "status": "success",
  "message": "My subscribed services get successfully.",
  "data": [
    {
      "id": 1,
      "name": "HappiBUDDY",
      ...
    }
  ]
}
```

---

### 6.6 Apply Coupon

**Endpoint:** `GET|POST /api/v1/apply-coupon`

**Headers:** `Authorization: Bearer {token}`

**Description:** Apply a coupon code to get discount on a plan.

**Flow:** Called from the checkout screen when the user enters a coupon/promo code.
- **Prerequisites:** User must have selected a plan (knows `plan_id`).
- **On success:** The returned `coupon_id` and `discount` are passed to the `payment` endpoint as `coupen_id` to apply the discount.
- **On failure:** Show "Invalid Coupon" message.

**Request Body:**
```json
{
  "plan_id": 1,
  "coupon": "SAVE20"
}
```

**Response (Success - 200):**
```json
{
  "status": "success",
  "message": "The coupon was applied successfully.",
  "data": {
    "coupon_id": 5,
    "plan_id": 1,
    "discount": 20
  }
}
```

**Response (Invalid - 400):**
```json
{
  "status": "error",
  "message": "Invalid Coupon"
}
```

---

### 6.7 Avail Free Service

**Endpoint:** `GET|POST /api/v1/avail-free-services`

**Headers:** `Authorization: Bearer {token}`

**Description:** Subscribe to a free plan/service.

**Flow:** Used when a plan has `price = 0` (free tier) — no RazorPay payment needed. Directly subscribes the user without going through the payment flow.
- **Prerequisites:** The selected `plan_id` must be a free plan.
- **Use case:** Organization-included plans, trial plans, or promotional zero-cost plans.

**Request Body:**
```json
{
  "plan_id": 3,
  "coupen_id": 0
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Plan availed successfully."
}
```

---

### 6.8 Payment for iOS

**Endpoint:** `GET|POST /api/v1/payment-for-ios`

**Headers:** `Authorization: Bearer {token}`

**Description:** Process iOS in-app purchase receipt.

**Flow:** Alternative payment path for iOS users who purchase via Apple IAP instead of RazorPay. After the Apple receipt is validated client-side, the transaction receipt is sent here to be stored and the plan activated.
- **Prerequisites:** iOS transaction must be completed via Apple StoreKit (SKPaymentQueue).
- **`marchant_name`:** The payment processor name (typically "RazorPay" even for IAP reconciliation).

**Request Body:**
```json
{
  "marchant_name": "RazorPay",
  "plan_id": 1,
  "amount": 299,
  "transaction_id": "txn_xxxxxxxxxx",
  "transaction_receipt": "base64_encoded_receipt..."
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Plan has been buy successfully."
}
```

---

## 7. User Chat (HappiBuddy) APIs

All endpoints require **authentication** (`auth:api` middleware).

**Flow Context:** The HappiBuddy chat feature pairs users with a psychologist for text-based support. **Call order:** `assign-psychologist` (to get a buddy) → `psy-whom-user-currently-chatting` (to load active chat) → `send-message-by-user-to-psy` (to send messages). Round-robin assignment ensures load balancing.

### 7.1 Assign Psychologist (Start Chat)

**Endpoint:** `GET|POST /api/v1/assign-psychologist`

**Headers:** `Authorization: Bearer {token}`

**Description:** Assign a HappiBuddy psychologist to the user based on language (round-robin).

**Flow:** Called when the user opens the chat feature for the first time. The backend picks the next available psychologist in a round-robin rotation for the requested language.
- **Prerequisites:** User must have an active subscription or org-included access to HappiBuddy.
- **On success:** Returns `psychologist_detail` (the assigned buddy) and `group_id` (used for all subsequent messages).

**Request Body:**
```json
{
  "language": "english"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Psychologist assign successfully.",
  "psychologist_detail": {
    "id": 1,
    "username": "psy_john",
    "email": "psy@example.com",
    "first_name": "John",
    ...
  },
  "group_id": "123456"
}
```

---

### 7.2 Switch Language While Chat

**Endpoint:** `GET|POST /api/v1/switch-language-while-chat`

**Headers:** `Authorization: Bearer {token}`

**Description:** Switch to a different psychologist based on language preference.

**Flow:** Called when the user wants to change their chat language (and thus their psychologist). Ends the current chat and assigns a new psychologist for the requested language.

**Request Body:**
```json
{
  "language": "hindi"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Psychologist changed successfully.",
  "psychologist_detail": {
    "id": 2,
    ...
  },
  "group_id": "123456"
}
```

---

### 7.3 Currently Chatting Psychologist

**Endpoint:** `GET|POST /api/v1/psy-whom-user-currently-chatting`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get the psychologist the user is currently chatting with.

**Flow:** Called when opening the chat screen to load the active conversation partner. Also used to check unread message count (for badge display). Returns null/empty if no chat is active.

**Response (200):**
```json
{
  "status": "success",
  "message": "Psychologist get successfully.",
  "psychologist_detail": {
    "id": 1,
    "username": "psy_john",
    ...
  },
  "language": "english",
  "group_id": "123456",
  "user_unread_message": 0
}
```

---

### 7.4 All Psychologists User Has Chatted With

**Endpoint:** `GET|POST /api/v1/all-psy-to-whom-user-chat`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get history of all psychologists the user has chatted with.

**Flow:** Called from a "Chat History" screen. Shows all past psychologist assignments (including after language switches). Useful for the user to see who they've talked to before.

**Response (200):**
```json
{
  "status": "success",
  "message": "All psychologist to whom user chat.",
  "list": [
    {
      "id": 1,
      "user_id": 1,
      "psychologist_id": 1,
      "assigned_date_time": "24-01-2024 10:30:00"
    }
  ]
}
```

---

### 7.5 Send Message (User to Psychologist)

**Endpoint:** `GET|POST /api/v1/send-message-by-user-to-psy`

**Headers:** `Authorization: Bearer {token}`

**Description:** Send a chat message from user to assigned psychologist.

**Flow:** Called when the user hits "Send" in the chat interface. Sends push notification to the psychologist via Expo Push.
- **Reward:** Each message awards reward points to the user.
- **Prerequisites:** A chat session must be active (psychologist assigned with a `group_id`).

**Request Body:**
```json
{
  "psychologist_id": 1,
  "group_id": "123456",
  "message": "Hello, I need some advice."
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Message has been sent successfully to psychologist."
}
```

---

### 7.6 Clear Message Batch (User)

**Endpoint:** `GET|POST /api/v1/clear-message-batch-of-user`

**Headers:** `Authorization: Bearer {token}`

**Description:** Reset unread message count for the user.

**Flow:** Called when the user opens the chat screen or views their messages. Resets the `user_unread_message` counter to 0 for the active conversation (clearing the badge count on the chat icon).

**Response (200):**
```json
{
  "status": "success",
  "message": "Message batch cleared successfully of user."
}
```

---

## 8. User HappiLearn APIs

All endpoints require **authentication** (`auth:api` middleware).

**Flow Context:** HappiLearn is a curated library of articles and videos about mental wellness. **Call order:** `happi-learn-content` (browse) → `happi-learn-content-by-id` (view detail) → `like-happi-learn-post` / `unlike-happi-learn-post` (interact). `search-parameters` populates filter/search options.

### 8.1 Get HappiLearn Content

**Endpoint:** `GET|POST /api/v1/happi-learn-content`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get paginated HappiLearn content with filtering. Results are cached.

**Request Parameters (all optional):**
```json
{
  "content_type": "article,video",
  "profile": "Student,Professional",
  "parameters": "Stress,Anxiety",
  "language": "english",
  "search": "anxiety management"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Content fetched successfully.",
  "data": {
    "data": [
      {
        "id": 1,
        "title": "Managing Anxiety",
        "type": "article",
        "content": "...",
        "profile": "Student",
        "parameters": "Stress,Anxiety",
        "language": "english",
        "likes_count": 5,
        "keywords": "anxiety,stress",
        ...
      }
    ],
    "per_page": 10,
    "current_page": 1,
    ...
  },
  "recentlyViewed": {
    "data": [
      {
        "id": 1,
        "user_id": 1,
        "happi_learn_content_id": 2,
        "HappiLearnContent": {
          "id": 2,
          ...
        }
      }
    ]
  }
}
```

---

### 8.2 Get HappiLearn Content By ID

**Endpoint:** `GET|POST /api/v1/happi-learn-content-by-id`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get full content details with suggested content. Cached for 5 minutes.

**Flow:** Called when the user taps on a content item from the list (8.1). Loads the full content body along with suggested/related content. The viewed content is automatically tracked in "recently viewed" history.

**Request Body:**
```json
{
  "content_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Content fetched successfully.",
  "data": {
    "id": 1,
    "title": "Managing Anxiety",
    "type": "article",
    "likes_count": 5,
    ...
  },
  "suggested_content": [
    {
      "id": 2,
      "title": "Stress Relief Techniques",
      "likes_count": 3,
      ...
    }
  ]
}
```

---

### 8.3 Like HappiLearn Content

**Endpoint:** `GET|POST /api/v1/like-happi-learn-post`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called when the user taps the "Like" (heart) button on a content item. Toggle with `unlike-happi-learn-post`.

**Request Body:**
```json
{
  "happi_learn_content_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Liked successfully"
}
```

**Response (Already liked - 200):**
```json
{
  "status": "success",
  "message": "Already liked"
}
```

---

### 8.4 Unlike HappiLearn Content

**Endpoint:** `GET|POST /api/v1/unlike-happi-learn-post`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called when the user taps the "Unlike" (remove heart) button. Toggle with `like-happi-learn-post`.

**Request Body:**
```json
{
  "happi_learn_content_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Unliked successfully"
}
```

---

### 8.5 Search Parameters

**Endpoint:** `GET|POST /api/v1/search-parameters`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get list of searchable parameter keywords.

**Flow:** Called to populate filter/search chips on the HappiLearn browse screen. Returns a flat list of keyword strings that can be used in the `parameters` filter of `happi-learn-content`.

**Response (200):**
```json
{
  "message": "Parameters fetched successfully.",
  "data": [
    "Stress", "Anxiety", "Depression", "Burn Out", "Happiness",
    "Internet Addiction", "Personality", "Self Esteem", "Resilience",
    "Job Satisfaction", "Substance Abuse", "Emotional Regulation",
    "Peer Pressure", "Group Conformity", "Gaming Disorder",
    "Attention and Concentration", "Relationship Issues",
    "Body Image", "Well Being"
  ]
}
```

---

## 9. User HappiSelf APIs

All endpoints require **authentication** (`auth:api` middleware).

**Flow Context:** HappiSelf is a self-guided course library for mental wellness. Courses are sequential (sub-courses must be completed in order). **Call order:** `course-list` (browse courses) → `sub-course-list` (see modules) → `start-sub-course` → `get-sub-course-content` (view lessons) → `end-sub-course` (mark complete). Optional: like/unlike, add notes, save content answers.

### 9.1 Course List

**Endpoint:** `GET|POST /api/v1/course-list`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get all HappiSelf courses in user's language.

**Flow:** Called when opening the HappiSelf section. Shows available courses. Can filter by `course_name` (POST) or show all (GET).

**GET Request:** No parameters
**POST Request:**
```json
{
  "course_name": "Mindfulness"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Course list get successfully.",
  "list": [
    {
      "id": 1,
      "course_name": "Mindfulness Basics",
      "language": "english",
      "likes_count": 10,
      ...
    }
  ]
}
```

---

### 9.2 Sub Course List

**Endpoint:** `GET|POST /api/v1/sub-course-list`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get sub-courses for a specific course, with completion/status tracking.

**Flow:** Called when the user taps on a course. Shows all modules with their lock/open/completed status. Sub-courses are sequential — must complete one to unlock the next.

**Request Body:**
```json
{
  "happiself_course_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Sub course list get successfully.",
  "list": [
    {
      "id": 1,
      "sub_course_name": "Introduction to Mindfulness",
      "happiself_course_id": 1,
      "count_for_sequence": 1,
      "status": "open"
    },
    {
      "id": 2,
      "happiself_course_id": 1,
      "count_for_sequence": 2,
      "status": "locked"
    }
  ]
}
```

> Status values: `open` (first available), `ongoing` (in progress), `locked` (not yet available), `completed`

---

### 9.3 Get Sub Course Content

**Endpoint:** `GET|POST /api/v1/get-sub-course-content`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called when the user opens a sub-course module. Displays the lesson content which can be text, video, or interactive (with options/answers).

**Request Body:**
```json
{
  "happiself_sub_course_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Content get successfully.",
  "data": [
    {
      "id": 1,
      "happiself_sub_course_id": 1,
      "content_type": "text",
      "content": "Content text here...",
      "option": [
        {"id": 1, "option": "Option A"},
        {"id": 2, "option": "Option B"}
      ]
    }
  ]
}
```

---

### 9.4 Start Sub Course

**Endpoint:** `GET|POST /api/v1/start-sub-course`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called when user taps "Start" on a sub-course. Marks it as `ongoing` in the progress tracking. Sequential courses must be started in order (previous sub-course must be `completed`).

**Request Body:**
```json
{
  "happiself_sub_course_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Sub course has been start successfully."
}
```

**Response (Already started - 200):**
```json
{
  "status": "success",
  "message": "This course is already started."
}
```

---

### 9.5 End Sub Course

**Endpoint:** `GET|POST /api/v1/end-sub-course`

**Headers:** `Authorization: Bearer {token}`

**Description:** Mark a sub-course as completed. Awards reward points.

**Flow:** Called when user completes a sub-course. Awards reward points. If all sub-courses in a course are done, extra bonus points and a push notification are sent.

**Request Body:**
```json
{
  "happiself_sub_course_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Happiself sub course complete successfully."
}
```

> Note: When all sub-courses in a course are completed, extra reward points are given and a push notification is sent.

---

### 9.6 Like HappiSelf Course

**Endpoint:** `GET|POST /api/v1/like-happiself-course`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Toggle button on the course detail screen.

**Request Body:**
```json
{
  "happiself_course_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Happiself course like successfully."
}
```

---

### 9.7 Unlike HappiSelf Course

**Endpoint:** `GET|POST /api/v1/unlike-happiself-course`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Toggle button on the course detail screen.

**Request Body:**
```json
{
  "happiself_course_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Post unlike successfully."
}
```

---

### 9.8 HappiSelf Library List

**Endpoint:** `GET|POST /api/v1/happiself-library-list`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called from the "Library" tab. Shows collections of HappiSelf resources. Can filter by name (POST) or show all (GET).

**GET Request:** No parameters
**POST Request:**
```json
{
  "library_name": "Meditation"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Library list get successfully.",
  "list": [...]
}
```

---

### 9.9 HappiSelf Library Content

**Endpoint:** `GET|POST /api/v1/happiself-library-content`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called when the user taps on a library item. Shows the content inside that collection.

**Request Body:**
```json
{
  "happiself_library_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Content get successfully.",
  "data": [...]
}
```

---

### 9.10 Add Notes

**Endpoint:** `GET|POST /api/v1/happiself-add-notes`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called when the user saves a personal note from the course content screen. Notes are user-specific and persist across sessions.

**Request Body:**
```json
{
  "notes": "My personal notes here..."
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Notes added successfully."
}
```

---

### 9.11 Update Notes

**Endpoint:** `GET|POST /api/v1/happiself-update-notes`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called when editing an existing note.

**Request Body:**
```json
{
  "notes_id": 1,
  "notes": "Updated notes content..."
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Notes Update successfully."
}
```

---

### 9.12 Get Notes List

**Endpoint:** `GET|POST /api/v1/happiself-get-notes-list`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called from the "My Notes" screen. Lists all notes the user has created across all courses.

**Response (200):**
```json
{
  "status": "success",
  "message": "Notes list get successfully.",
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "notes": "My notes...",
      "created_at": "...",
      "updated_at": "..."
    }
  ]
}
```

---

### 9.13 Get Notes By ID

**Endpoint:** `GET|POST /api/v1/happiself-get-notes-by-id`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called when the user taps on a specific note to view full content.

**Request Body:**
```json
{
  "notes_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Notes get successfully.",
  "data": {
    "id": 1,
    "user_id": 1,
    "notes": "My notes..."
  }
}
```

---

### 9.14 Delete Notes By ID

**Endpoint:** `GET|POST /api/v1/happiself-delete-notes-by-id`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called when the user deletes a note from the notes list.

**Request Body:**
```json
{
  "notes_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Notes deleted successfully."
}
```

---

### 9.15 Save Content Answer

**Endpoint:** `GET|POST /api/v1/save-happiself-content-answer`

**Headers:** `Authorization: Bearer {token}`

**Description:** Save answer for HappiSelf content (supports short_answer, linear_scale, question_checkbox types).

**Flow:** Called when the user submits an answer to interactive content within a sub-course. Supports different answer types: `short_answer` (text), `linear_scale` (1-10 rating), `question_checkbox` (comma-separated multiple choice).

**Request Body (short_answer / linear_scale):**
```json
{
  "content_id": 1,
  "content_type": "short_answer",
  "answer": "My answer text"
}
```

**Request Body (question_checkbox - multiple answers):**
```json
{
  "content_id": 1,
  "content_type": "question_checkbox",
  "answer": "option1,option2,option3"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Answer has been submitted successfully."
}
```

---

## 10. User HappiTalk APIs

All endpoints require **authentication** (`auth:api` middleware).

**Flow Context:** HappiTalk is the one-on-one video consultation service with psychologists. **Full booking flow:** `psychologist-listing` (browse) → `get-slots-of-psy` (check availability) → `payment-for-happitalk` or `avail-haapitalk-user` (B2B) → RazorPay → `my-booking-user` (view bookings) → `join-talk-room-user` (video call) → `submit-opinion-after-session-user` (rate). The `emoji-and-reason-list` populates the post-session rating screen. Penalty rules from `get-penalty-clause-user` govern cancellations.

### 10.1 Psychologist Listing

**Endpoint:** `GET|POST /api/v1/psychologist-listing`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get list of available HappiTalk psychologists with filtering.

**Flow:** Called from the HappiTalk search/browse screen. Supports filtering by city, specialization, language, and expert level. `user_detail.user_from` determines whether the user sees org-specific or all psychologists.

**Request Parameters (all optional):**
```json
{
  "search": "John",
  "city": "Mumbai,Delhi",
  "expert_category": "Senior,Expert",
  "specialization": "Clinical Psychology",
  "language": "English,Hindi"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Psychologist list get successfully.",
  "user_detail": {
    "user_from": "individual",
    "organization_name": null
  },
  "list": [
    {
      "id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "username": "johndoe",
      "email": "psy@example.com",
      "summary": "Expert psychologist...",
      "profile_picture": "profile.jpg",
      "gender": "male",
      "city_id": 1,
      "expert_level_id": 2,
      "slot1": "09:00",
      "slot2": "17:00",
      "language": [{"id": 1, "name": "English"}],
      "mobileExpertLevel": {"id": 2, "name": "Senior"},
      "city": {"id": 1, "name": "Mumbai"},
      "specialization": [{"id": 1, "name": "Clinical Psychology"}],
      "psy_profile": "https://s3.amazonaws.com/psychologist/profile.jpg"
    }
  ]
}
```

> `user_detail.user_from` can be `individual` or `organization`. Organization users see psychologists assigned to their org.

---

### 10.2 Get Slots of Psychologist

**Endpoint:** `GET|POST /api/v1/get-slots-of-psy`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called when the user selects a psychologist to view their available appointment slots. Only shows future dates.

**Request Body:**
```json
{
  "psychologist_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Slots get successfully.",
  "slot_dates": ["2024-01-20", "2024-01-21"],
  "slot_dates_with_time": {
    "2024-01-20": {
      "time": ["10:00 AM - 11:00 AM", "02:00 PM - 03:00 PM"]
    },
    "2024-01-21": {
      "time": ["11:00 AM - 12:00 PM"]
    }
  }
}
```

> Only shows dates from current date onwards.

---

### 10.3 Psychologist Filter Lists

**Flow:** These 4 endpoints populate filter dropdowns on the psychologist listing screen. Call them in parallel when the browse screen loads.

**Endpoint:** `GET|POST /api/v1/psychologist-city`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
  "status": "success",
  "message": "Language list get successfully.",
  "city": [...]
}
```

**Endpoint:** `GET|POST /api/v1/psychologist-specialization`

**Response (200):**
```json
{
  "status": "success",
  "message": "Language list get successfully.",
  "specialization": [...]
}
```

**Endpoint:** `GET|POST /api/v1/psychologist-expert-category`

**Response (200):**
```json
{
  "status": "success",
  "message": "Language list get successfully.",
  "expert_level": [...]
}
```

**Endpoint:** `GET|POST /api/v1/psychologist-language`

**Response (200):**
```json
{
  "status": "success",
  "message": "Language list get successfully.",
  "language": [...]
}
```

---

### 10.4 My Bookings (User)

**Endpoint:** `GET|POST /api/v1/my-booking-user`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called from the "My Sessions" tab. Filter by `type` to show `past`, `today`, or `future` sessions.

**Request Body:**
```json
{
  "type": "past|today|future"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Session get successfully.",
  "session_detail": [
    {
      "id": 1,
      "happitalk_booking_id": 1,
      "user_id": 1,
      "psychologist_id": 1,
      "date": "2024-01-20",
      "time": "10:00 AM - 11:00 AM",
      "start_time": "10:00 AM",
      "end_time": "11:00 AM",
      "is_req_accepted": 1,
      "is_cancel": 0,
      "is_end": 0,
      "user_recording_permission": 1,
      "psychologistDetail": {
        "id": 1,
        "first_name": "John",
        "last_name": "Doe",
        "psy_profile": "https://s3.amazonaws.com/psychologist/profile.jpg"
      },
      "bookingDetails": {...}
    }
  ]
}
```

---

### 10.5 Reschedule Booking (User)

**Endpoint:** `GET|POST /api/v1/reschedule-booking-user`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called when the user wants to change the date/time of an existing booking. Cannot reschedule if already cancelled.

**Request Body:**
```json
{
  "session_id": 1,
  "date": "2024-01-25",
  "time": "02:00 PM - 03:00 PM"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "The booking was rescheduled successfully."
}
```

**Response (Already cancelled - 400):**
```json
{
  "status": "error",
  "message": "This session has already been cancelled."
}
```

**Response (Same time - 400):**
```json
{
  "status": "error",
  "message": "You are rescheduling the booking for the same time again. Please change the time slot."
}
```

---

### 10.6 Cancel Booking (User)

**Endpoint:** `GET|POST /api/v1/cancel-booking-user`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called when the user cancels a session. Penalty clause determines how many session credits are returned.

**Request Body:**
```json
{
  "session_id": 1,
  "cancel_reason": "Not feeling well"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Booking has been cancelled successfully."
}
```

> Note: Penalty logic applies:
> - If cancelled before `for_b2c_user_for_one_credit` hours: full session credit returned
> - If cancelled between one_credit and half_credit hours: half session (0.5) credit returned
> - If cancelled after half_credit hours: no credit returned

---

### 10.7 List to Book Another Session

**Endpoint:** `GET|POST /api/v1/list-to-book-another-session-user`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get bookings with remaining sessions for booking additional sessions.

**Flow:** Called from the "Book Another Session" screen. Shows bookings that still have remaining sessions available.

**Response (200):**
```json
{
  "status": "success",
  "message": "Session get successfully.",
  "booking_details": [
    {
      "id": 1,
      "user_id": 1,
      "psychologist_id": 1,
      "remaining_session": 3,
      "total_no_of_session": 5,
      "psychologist": {
        "id": 1,
        "first_name": "John",
        "last_name": "Doe",
        "psy_profile": "https://s3.amazonaws.com/psychologist/profile.jpg"
      }
    }
  ]
}
```

---

### 10.8 Book Another Session

**Endpoint:** `GET|POST /api/v1/book-another-session-user`

**Headers:** `Authorization: Bearer {token}`

**Description:** Book an additional session using an existing booking with remaining sessions.

**Flow:** Called when the user books an additional session using an existing multi-session package.

**Request Body:**
```json
{
  "booking_id": 1,
  "date": "2024-01-28",
  "time": "10:00 AM - 11:00 AM",
  "user_recording_permission": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Session request sent to psychologist. We will let you know once accepted.",
  "details": {
    "happitalk_booking_id": 1,
    "user_id": 1,
    "user_type": "b2b",
    "psychologist_id": 1,
    "date": "2024-01-28",
    "time": "10:00 AM - 11:00 AM",
    "start_time": "10:00 AM",
    "end_time": "11:00 AM",
    "user_recording_permission": 1
  }
}
```

---

### 10.9 Emoji and Reason List

**Endpoint:** `GET|POST /api/v1/emoji-and-reason-list`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get list of emojis and reasons for session feedback.

**Flow:** Called when opening the post-session feedback screen. Populates the emoji picker and reason selector.

**Response (200):**
```json
{
  "status": "success",
  "message": "Data get successfully.",
  "emoji_list": [...],
  "reason_list": [
    "Didnot explore concern.",
    "Didnot feel understood.",
    "Average.",
    "Felt understood and happy.",
    "Felt great. Will book again."
  ]
}
```

---

### 10.10 Submit Opinion After Session (User)

**Endpoint:** `GET|POST /api/v1/submit-opinion-after-session-user`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called after a HappiTalk session ends. User rates the experience with emoji, reason, and optional comment.

**Request Body:**
```json
{
  "session_id": 1,
  "emoji_id": 3,
  "reason": "Felt understood and happy.",
  "additional_comment": "Great session!"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Opinion submit successfully."
}
```

---

### 10.11 Join Talk Room (User)

**Endpoint:** `GET|POST /api/v1/join-talk-room-user`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get Twilio video access token to join a HappiTalk session.

**Flow:** Called when the user taps "Join Call" on an upcoming session. Returns a Twilio access token. A Twilio room is created on first join.

**Request Body:**
```json
{
  "session_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Token get sucessfully.",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

**Response (Session ended - 400):**
```json
{
  "status": "error",
  "message": "This session has ended."
}
```

> Note: A Twilio room is created on first join if one doesn't exist. Sets `is_user_join = 1`.

---

### 10.12 Avail HappiTalk (User)

**Endpoint:** `GET|POST /api/v1/avail-haapitalk-user`

**Headers:** `Authorization: Bearer {token}`

**Description:** Book a HappiTalk session directly (B2B flow).

**Flow:** B2B booking flow — directly books a session without RazorPay payment (cost covered by organization). Books the session immediately.

**Request Body:**
```json
{
  "psychologist_id": 1,
  "date": "2024-01-20",
  "time": "10:00 AM - 11:00 AM",
  "session": 5,
  "user_recording_permission": 1,
  "coupen_id": 0
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Your HappiTALK session has been booked successfully."
}
```

---

### 10.13 Get Penalty Clause

**Endpoint:** `GET|POST /api/v1/get-penalty-clause-user`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get cancellation penalty rules.

**Flow:** Called before showing the cancel/reschedule confirmation to display the applicable cancellation policy. Shows hour-based thresholds for full credit, half credit, and no credit returns.

**Response (200):**
```json
{
  "status": "success",
  "message": "Penalty clause get successfully.",
  "data": {
    "id": 1,
    "for_b2c_user_for_one_credit": 24,
    "for_b2c_user_for_half_credit": 12,
    "for_b2b_user_for_one_credit": 24,
    "for_b2b_user_for_half_credit": 12
  }
}
```

---

## 11. User HappiGuide APIs

All endpoints require **authentication** (`auth:api` middleware).

**Flow Context:** HappiGuide is a guided wellness session service. Same pattern as HappiTalk but for guided sessions. **Call order:** `avail-happiguide-user` (subscribe/book) → `happiguide-session-user` (view) → `join-guide-room-user` (video call) → `submit-opinion-after-guide-session-user` (rate).

### 11.1 HappiGuide Session (User)

**Endpoint:** `GET|POST /api/v1/happiguide-session-user`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get user's HappiGuide session details.

**Flow:** Called from the HappiGuide section to load the user's current/pending guide session.

**Response (200):**
```json
{
  "status": "success",
  "message": "Happiguide session get successfully.",
  "list": {
    "id": 1,
    "user_id": 1,
    "psychologist_id": 1,
    "date": "2024-01-20",
    "time": "10:00 AM - 11:00 AM",
    "is_start": 0,
    "is_end": 0,
    "psychologistDetail": {
      "id": 1,
      "first_name": "John",
      "psy_profile": "https://s3.amazonaws.com/psychologist/profile.jpg"
    }
  }
}
```

**Response (No session - 400):**
```json
{
  "status": "error",
  "message": "No Session available."
}
```

---

### 11.2 Avail HappiGuide (User)

**Endpoint:** `GET|POST /api/v1/avail-happiguide-user`

**Headers:** `Authorization: Bearer {token}`

**Description:** Subscribe to a HappiGuide plan (free/paid via bundle).

**Flow:** Called to subscribe/book a HappiGuide session. Psychologist is auto-assigned via round-robin from psychologists mapped to "HappiGuide".

**Request Body:**
```json
{
  "plan_id": 6,
  "date": "2024-01-20",
  "time": "10:00 AM - 11:00 AM",
  "coupen_id": 0
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "HappiGUIDE session avail successfully."
}
```

> Note: Psychologist is assigned via round-robin from `AssignPsyToPlan` where `plan_name = 'HappiGuide'`.

---

### 11.3 Reschedule HappiGuide Session (User)

**Endpoint:** `GET|POST /api/v1/happiguide-reschedule-session-user`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called to change the date/time of a guide session.

**Request Body:**
```json
{
  "session_id": 1,
  "date": "2024-01-25",
  "time": "02:00 PM - 03:00 PM"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "HappiGUIDE session has been Rescheduled successfully."
}
```

---

### 11.4 Join Guide Room (User)

**Endpoint:** `GET|POST /api/v1/join-guide-room-user`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get Twilio video access token for HappiGuide session.

**Flow:** Called when the user taps "Join" to start a HappiGuide video session. Returns a Twilio access token.

**Request Body:**
```json
{
  "session_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Token get sucessfully.",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

---

### 11.5 Submit Opinion After Guide Session (User)

**Endpoint:** `GET|POST /api/v1/submit-opinion-after-guide-session-user`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called after a guide session ends. Post-session feedback with emoji, reason, and optional comment.

**Request Body:**
```json
{
  "session_id": 1,
  "emoji_id": 4,
  "reason": "Felt understood and happy.",
  "additional_comment": "Very helpful session"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Opinion submit successfully."
}
```

---

## 12. User Video Chat APIs

All endpoints require **authentication** (`auth:api` middleware).

**Flow Context:** Low-level Twilio Video API wrappers. Used by both HappiTalk and HappiGuide sessions. Not typically called directly — the session join endpoints (`join-talk-room-user`, `join-guide-room-user`) handle token generation. These are for advanced/admin use: creating rooms, managing participants, and recording.

### 12.1 Create Video Room

**Endpoint:** `GET|POST /api/v1/create-video-room`

**Headers:** `Authorization: Bearer {token}`

**Description:** Create a new Twilio video room.

**Flow:** Direct Twilio room creation. Normally rooms are created automatically on first participant join. Use this for pre-creating rooms.

**Response (200):**
```json
{
  "message": "Room created sucessfully.",
  "room_id": "RMxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "room_name": "12345testRoom"
}
```

---

### 12.2 Grant Room Access

**Endpoint:** `GET|POST /api/v1/grant-room-access`

**Headers:** `Authorization: Bearer {token}`

**Description:** Generate a Twilio access token for a specific video room.

**Flow:** Generate a Twilio access token for a specific room. Alternative to the session-based join endpoints.

**Request Body:**
```json
{
  "room_name": "RMxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```

**Response (200):**
```json
{
  "message": "Token get sucessfully.",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

---

### 12.3 Check Participant in Room

**Endpoint:** `GET|POST /api/v1/check-participant-in-room`

**Headers:** `Authorization: Bearer {token}`

**Description:** Count connected participants in a video room.

**Flow:** Called to verify if participants are still connected. Used before ending a session to ensure no one is still in the call.

**Request Body:**
```json
{
  "room_sid": "RMxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```

**Response (200):** Returns integer count of participants (e.g., `2`).

---

### 12.4 Disconnect All Users From Room

**Endpoint:** `GET|POST /api/v1/disconnect-all-user-from-room`

**Headers:** `Authorization: Bearer {token}`

**Description:** Disconnect all participants from a Twilio video room.

**Flow:** Force-disconnect all participants from a room. Called during session end/cleanup.

**Request Body:**
```json
{
  "room_sid": "RMxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```

**Response:** No content (void).

---

### 12.5 Make Composition of Room

**Endpoint:** `GET|POST /api/v1/make-composition-of-room`

**Headers:** `Authorization: Bearer {token}`

**Description:** Start recording/composition of a video room.

**Flow:** Start recording a video room via Twilio Composition. Returns the composition SID for status tracking.

**Request Body:**
```json
{
  "roomID": "RMxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```

**Response:** Returns the composition SID string.

---

### 12.6 Download Composition

**Endpoint:** `GET|POST /api/v1/download-composition`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get download URL for a completed video composition/recording.

**Flow:** Check composition status and get download URL when completed. Poll until `state` changes from `processing` to `completed`.

**Request Body:**
```json
{
  "composition_sid": "CJxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "room_sid": "RMxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```

**Response (Completed - 200):**
```json
{
  "status": "success",
  "state": "completed",
  "url": "https://video.twilio.com/v1/Compositions/.../Media?Ttl=3600"
}
```

**Response (Processing - 200):**
```json
{
  "status": "error",
  "state": "processing"
}
```

---

## 13. User Rating APIs

All endpoints require **authentication** (`auth:api` middleware).

**Flow Context:** App-level ratings (not session feedback). Used on the "Rate Us" screen. Different from `feedback` (which is for the app experience) — this is a public star/emoji rating.

### 13.1 Emoji List

**Endpoint:** `GET|POST /api/v1/emoji-list`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get list of application rate emojis.

**Flow:** Called when opening the rate screen. Shows available rating emoji options.

**Response (200):**
```json
{
  "status": "success",
  "message": "Emoji get successfully.",
  "list": [
    {
      "id": 1,
      "emoji": "😄",
      "name": "Excellent"
    }
  ]
}
```

---

### 13.2 Submit Rating

**Endpoint:** `GET|POST /api/v1/submit-rating`

**Headers:** `Authorization: Bearer {token}`

**Flow:** Called when user submits their app rating with an emoji selection and optional review text.

**Request Body:**
```json
{
  "review": "Great app, very helpful!",
  "application_rate_emoji_id": 4
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Review has been submit successfully."
}
```

---

## 14. User White Labeling APIs

All endpoints require **authentication** (`auth:api` middleware).

**Flow Context:** Organization branding configuration. Called after login for org users to customize the app's header/footer/logo with the organization's branding.

### 14.1 White Labeling Status

**Endpoint:** `GET|POST /api/v1/white-labelling-status`

**Headers:** `Authorization: Bearer {token}`

**Description:** Get white labeling configuration for organization users.

**Flow:** Called on app launch (after login) to check if the app should show a custom org logo and branded header/footer. Only org users (`user_from = organization`) get custom logos.

**Response (Individual user - 200):**
```json
{
  "status": "success",
  "message": "Logo get successfully.",
  "header": "0",
  "footer": "1"
}
```

**Response (Org user with custom logo - 200):**
```json
{
  "status": "success",
  "message": "Logo get successfully.",
  "header": "1",
  "footer": "1",
  "logo": "https://s3.amazonaws.com/org_logo.png"
}
```

---

## 15. Psychologist Authentication APIs

**Flow Context:** These are the login and account management endpoints for psychologists (not users). Psychologists log in via `psychologist-login` and receive a JWT that is used for all subsequent psychologist API calls (sections 16-19). Password reset follows the same OTP flow as users but with dedicated endpoints.

### 15.1 Psychologist Login

**Endpoint:** `GET|POST /api/v1/psychologist-login`

**Description:** Authenticate psychologist with email and password.

**Flow:** Called from the psychologist login screen. The returned JWT token is stored and used for all subsequent psychologist API calls in sections 16-19.
- **Device tracking:** Like users, if the same account logs in from a new device, a notification is sent to the old device.

**Request Body:**
```json
{
  "email": "psychologist@example.com",
  "password": "password123",
  "device_token": "ExponentPushToken[xxxxxxxxxx]"
}
```

**Response (Success - 200):**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": {
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "email": "psychologist@example.com",
    "username": "johndoe",
    "profile_picture": "profile.jpg",
    "gender": "male",
    "age": 35,
    "highest_qualification": "PhD Psychology",
    "city_id": 1,
    "expert_level_id": 2,
    "slot1": "09:00",
    "slot2": "17:00",
    "device_token": "ExponentPushToken[xxxxxxxxxx]"
  }
}
```

**Response (Failed - 401):**
```json
{
  "status": "failed",
  "message": "Invalid email address and password.",
  "error": "Unauthorized"
}
```

---

### 15.2 Forgot Password (Psychologist)

**Endpoint:** `GET|POST /api/v1/forgot-pw-p`

**Description:** Send OTP to psychologist's registered email.

**Flow:** Step 1 of 3. Psychologist enters their registered email to receive a password reset OTP.

**Request Body:**
```json
{
  "email": "psychologist@example.com"
}
```

**Response (Success - 200):**
```json
{
  "status": "success",
  "message": "OTP has been sent to psychologist@example.com."
}
```

---

### 15.3 Verify OTP (Psychologist)

**Endpoint:** `GET|POST /api/v1/psy-verify-otp`

**Flow:** Step 2 of 3. Verify the OTP sent to the psychologist's email.

**Request Body:**
```json
{
  "email": "psychologist@example.com",
  "otp": "123456"
}
```

**Response (Success - 200):**
```json
{
  "status": "success",
  "message": "OTP verify."
}
```

---

### 15.4 Set New Password (Psychologist)

**Endpoint:** `GET|POST /api/v1/psy-set-password`

**Flow:** Step 3 of 3. Set a new password after OTP verification.

**Request Body:**
```json
{
  "email": "psychologist@example.com",
  "password": "newSecurePassword123"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Password has been reset successfully."
}
```

---

### 15.5 Assign Email/Password to Existing Psychologists

**Endpoint:** `GET|POST /api/v1/assign-email-pass-to-existing-psy`

**Description:** Auto-generate email/password for psychologists who don't have them (admin utility).

**Flow:** Admin utility endpoint — called from the admin panel to auto-generate login credentials for psychologists who were created without email/password (legacy data migration). Not used in the mobile app flow.

**Response (200):**
```json
{
  "status": "success",
  "message": "Email and password assign to existing psychologists",
  "password": "happimynd@12345"
}
```

---

### 15.6 Psychologist Check (Validate Token)

**Endpoint:** `GET|POST /api/v1/psychologist-check`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Description:** Validate token and get current psychologist details.

**Flow:** Called on app launch to validate the stored psychologist JWT token. If non-200, clear token and redirect to login.

**Response (200):**
```json
{
  "id": 1,
  "first_name": "John",
  "last_name": "Doe",
  "email": "psychologist@example.com",
  "username": "johndoe"
}
```

---

### 15.7 Psychologist Logout

**Endpoint:** `GET|POST /api/v1/psychologist-logout`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called when psychologist taps "Logout". Clears device token on server side.

**Response (200):**
```json
{
  "status": "success",
  "message": "Psychologist logged out successfully"
}
```

---

### 15.8 Change Password (Psychologist)

**Endpoint:** `GET|POST /api/v1/change-pw-p`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called from the psychologist's settings screen. Requires current password for verification.

**Request Body:**
```json
{
  "old_password": "oldPassword123",
  "new_password": "newPassword123",
  "confirm_password": "newPassword123"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Password has been changed successfully."
}
```

---

## 16. Psychologist Profile APIs

**Flow Context:** Profile management for psychologists. Called after login. The profile includes personal info, credentials, specialization, languages, availability slots, and commission/TDS rates.

All endpoints require **psychologist authentication** (`psychologist` middleware).

### 16.1 Get Profile

**Endpoint:** `GET|POST /api/v1/get-psychologist-profile`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called on login and whenever the profile screen is opened. Shows the psychologist's full profile including availability slots, specializations, languages, and financial settings (commission_percentage, tds_percentage, price_per_session).

**Response (200):**
```json
{
  "status": "success",
  "message": "User detials get successfully.",
  "data": {
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "email": "psychologist@example.com",
    "username": "johndoe",
    "profile_picture": "profile.jpg",
    "gender": "male",
    "age": 35,
    "highest_qualification": "PhD Psychology",
    "internation_cert": "certificate.pdf",
    "commission_percentage": 80,
    "tds_percentage": 10,
    "price_per_session": 500,
    "specialization": [
      {"id": 1, "name": "Clinical Psychology"}
    ],
    "language": [
      {"id": 1, "name": "English"}
    ],
    "availability": [
      {
        "id": 1,
        "psychologist_slot_id": 1,
        "date": "2024-01-20",
        "time": "10:00 AM - 11:00 AM"
      }
    ],
    "expertLevel": {
      "id": 2,
      "name": "Senior"
    }
  }
}
```

---

### 16.2 Edit Profile

**Endpoint:** `GET|POST /api/v1/edit-psychologist-profile`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called from the psychologist's "Edit Profile" screen. Only certain fields are editable (age, gender, highest_qualification, internation_cert).

**Request Body:**
```json
{
  "age": 36,
  "gender": "male",
  "highest_qualification": "PhD Clinical Psychology",
  "internation_cert": "new_certificate.pdf"
}
```

**Validation Rules:**
| Field | Rule |
|-------|------|
| age | Required |
| gender | Required |
| highest_qualification | Required |

**Response (200):**
```json
{
  "status": "success",
  "message": "Profile has been updated sucessfully.",
  "data": {
    "age": 36,
    "gender": "male",
    "highest_qualification": "PhD Clinical Psychology"
  }
}
```

---

## 17. Psychologist Chat APIs

**Flow Context:** Psychologist-side chat management for HappiBuddy conversations. **Call order:** `psy-chat-listing` (see all conversations) → `get-group-id-by-psychologist` (get chat ID for a user) → `send-message-by-psy-to-user` (send message). Psychologists can submit monthly buddy reports via `submit-users-buddy-report-psy`.

All endpoints require **psychologist authentication** (`psychologist` middleware).

### 17.1 Chat Listing

**Endpoint:** `GET|POST /api/v1/psy-chat-listing`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Description:** Get list of all users chatting with this psychologist (active groups only).

**Flow:** Called when the psychologist opens their chat inbox. Shows all users they are currently chatting with, including unread message counts for both sides.

**Response (200):**
```json
{
  "status": "success",
  "message": "Chat list get successfully.",
  "list": [
    {
      "id": 1,
      "user_id": 1,
      "user_unread_message": 3,
      "psychologist_unread_message": 0,
      "group_id": "123456",
      "user": {
        "id": 1,
        "nickname": "JohnUser",
        "username": "johnuser"
      }
    }
  ]
}
```

---

### 17.2 Get Group ID

**Endpoint:** `GET|POST /api/v1/get-group-id-by-psychologist`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called when the psychologist taps on a user to open the chat. Gets the group_id needed to send messages.

**Request Body:**
```json
{
  "user_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Group ID get successfully.",
  "group_id": "123456",
  "current_psychologist_of_user": 1
}
```

---

### 17.3 Send Message (Psychologist to User)

**Endpoint:** `GET|POST /api/v1/send-message-by-psy-to-user`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called when the psychologist sends a chat message. Sends push notification to the user via Expo Push.

**Request Body:**
```json
{
  "user_id": 1,
  "group_id": "123456",
  "message": "Hello, how are you feeling today?"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Message has been sent successfully to user."
}
```

---

### 17.4 Clear Message Batch (Psychologist)

**Endpoint:** `GET|POST /api/v1/clear-message-batch-of-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called when the psychologist opens a chat conversation. Resets their unread message count for that conversation.

**Request Body:**
```json
{
  "user_id": 1,
  "group_id": "123456"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Message batch cleared successfully of psychologist."
}
```

---

### 17.5 Submit User's Buddy Report

**Endpoint:** `GET|POST /api/v1/submit-users-buddy-report-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Description:** Submit a monthly HappiBuddy report for a user.

**Flow:** Called from the psychologist's reporting screen. Psychologist submits a monthly progress report for a HappiBuddy user (session status, complaints, summary, homework, next plan).

**Request Body:**
```json
{
  "user_id": 1,
  "session_status": "completed",
  "presenting_complaints": "Anxiety issues",
  "session_summary": "User showed improvement",
  "hardword_asigned": "Practice breathing exercises",
  "plan_for_next_session": "Continue with CBT techniques"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "HappiBUDDY Opinion submit successfully."
}
```

---

### 17.6 Get User's Buddy Report

**Endpoint:** `GET|POST /api/v1/get-users-buddy-report-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called to view the last 3 buddy reports submitted for a specific user. Useful for continuity of care when reviewing past sessions.

**Request Body:**
```json
{
  "user_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Users HappiBUDDY report get successfully.",
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "psychologist_id": 1,
      "session_status": "completed",
      "presenting_complaints": "Anxiety issues",
      "session_summary": "User showed improvement",
      "hardword_asigned": "Practice breathing exercises",
      "plan_for_next_session": "Continue with CBT techniques",
      "created_at": "2024-01-15T10:00:00.000000Z"
    }
  ]
}
```

> Note: Returns the last 3 reports for the user.

---

## 18. Psychologist HappiTalk APIs

**Flow Context:** Psychologist-side HappiTalk session management. **Full flow:** Psychologist logs in → checks `dashboard-psy` for stats → views `my-booking-psychologist` (upcoming) → reviews `my-pending-request-psychologist` → accepts/rejects → at session time calls `join-talk-room-psy` → after session calls `session-mark-as-complete-psy` → submits opinion and session notes. Slots are managed via `add-slots-psy` / `delete-single-slot-psy` / etc.

All endpoints require **psychologist authentication** (`psychologist` middleware).

### 18.1 My Bookings

**Endpoint:** `GET|POST /api/v1/my-booking-psychologist`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Description:** Get bookings filtered by type with search on user nickname.

**Flow:** Called from the psychologist's "My Sessions" screen. Shows bookings filtered by type (past/today/future) with optional user search.

**Request Body:**
```json
{
  "type": "past|today|future",
  "search": "john"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Session get successfully.",
  "session_detail": {
    "data": [
      {
        "id": 1,
        "user_id": 1,
        "psychologist_id": 1,
        "date": "2024-01-20",
        "time": "10:00 AM - 11:00 AM",
        "start_time": "10:00 AM",
        "end_time": "11:00 AM",
        "is_req_accepted": 1,
        "is_cancel": 0,
        "is_end": 0,
        "user_type": "b2c",
        "userDetail": {
          "id": 1,
          "nickname": "JohnUser",
          "username": "johnuser",
          "user_from": "individual",
          "organization_name": null
        }
      }
    ],
    "current_page": 1,
    "last_page": 1,
    "per_page": 20,
    "total": 1
  }
}
```

---

### 18.2 My Pending Requests

**Endpoint:** `GET|POST /api/v1/my-pending-request-psychologist`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Description:** Get all pending session requests (where `is_req_accepted = 0`).

**Flow:** Called to see all session requests that haven't been accepted yet. Psychologist can accept or reject from this list.

**Response (200):**
```json
{
  "status": "success",
  "message": "Pending session request get successfully.",
  "session_detail": [...]
}
```

---

### 18.3 My All Slots

**Endpoint:** `GET|POST /api/v1/my-all-slots-psychologist`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Description:** Get all available time slots set by psychologist.

**Flow:** Called to view all availability slots the psychologist has set up. Used for overview planning.

**Response (200):**
```json
{
  "status": "success",
  "message": "Slots get successfully.",
  "slot_dates": ["2024-01-20", "2024-01-21"],
  "slot_dates_with_time": {
    "2024-01-20": {
      "time": ["10:00 AM - 11:00 AM", "02:00 PM - 03:00 PM"]
    }
  }
}
```

---

### 18.4 Get Slots of Particular Date

**Endpoint:** `GET|POST /api/v1/get-slots-of-perticular-date-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called when viewing a specific date's available slots. Used before adding or deleting slots for that date.

**Request Body:**
```json
{
  "date": "2024-01-20"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Slots get successfully.",
  "slot_dates_with_time": {
    "2024-01-20": {
      "time": ["10:00 AM - 11:00 AM", "02:00 PM - 03:00 PM"]
    }
  }
}
```

---

### 18.5 Session Mark as Complete

**Endpoint:** `GET|POST /api/v1/session-mark-as-complete-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Description:** Mark a HappiTalk session as ended. Triggers Twilio composition recording and notifications.

**Flow:** Called when the session ends. Triggers Twilio composition recording of the video. Sets is_end = 1. Sends notification that session is complete.

**Request Body:**
```json
{
  "session_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Session mark as completed successfully."
}
```

> Note: Creates a Twilio composition recording automatically if the psychologist had joined.

---

### 18.6 Check Room Participant

**Endpoint:** `GET|POST /api/v1/check-room-participant-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Description:** Check if any participants are still connected in a video room (uses `session_id` to find the room).

**Flow:** Called before marking a session as complete. Checks if the user is still connected. If yes, prompts to ask user to disconnect first.

**Request Body:**
```json
{
  "session_id": 1
}
```

**Response (Participants present - 400):**
```json
{
  "status": "error",
  "message": "Ask user to end the call first."
}
```

**Response (Room empty - 200):**
```json
{
  "status": "success",
  "message": "Room is empty."
}
```

---

### 18.7 Get Session of Particular Date

**Endpoint:** `GET|POST /api/v1/get-session-of-perticular-date-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called from a calendar view to see all sessions scheduled for a specific date.

**Request Body:**
```json
{
  "date": "2024-01-20"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Session of perticular date get successfully.",
  "sessions": [...]
}
```

---

### 18.8 Accept Session Request

**Endpoint:** `GET|POST /api/v1/accept-session-request`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called when the psychologist approves a pending session request. Sends notification to the user confirming acceptance.

**Request Body:**
```json
{
  "session_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Session request has been accepted successfully."
}
```

---

### 18.9 Reject Session Request

**Endpoint:** `GET|POST /api/v1/reject-session-request`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called when the psychologist declines a session request. Requires a reason. Returns 1 session credit back to the user's booking.

**Request Body:**
```json
{
  "session_id": 1,
  "reason": "Time slot not available"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Session request has been rejected successfully."
}
```

> Note: Rejected sessions return 1 session credit to the user's booking.

---

### 18.10 Get Session Between Two Dates

**Endpoint:** `GET|POST /api/v1/get-session-between-two-dates-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called for date-range reporting. Returns all sessions within the specified range.

**Request Body:**
```json
{
  "start_date": "2024-01-01",
  "end_date": "2024-01-31"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Session between two dates get successfully.",
  "sessions": [...]
}
```

---

### 18.11 Reschedule Booking (Psychologist)

**Endpoint:** `GET|POST /api/v1/reschedule-booking-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Description:** Psychologist cancels/reschedules a booking, returning session credit to user.

**Flow:** Called when the psychologist needs to cancel/reschedule a session. Returns the session credit to the user so they can rebook.

**Request Body:**
```json
{
  "session_id": 1,
  "cancel_reason": "Emergency - need to reschedule"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Reschedule booking request has been sent to user, and this session has been cancelled."
}
```

---

### 18.12 Delivered Sessions

**Endpoint:** `GET|POST /api/v1/delivered-sessions-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Description:** Get all completed (delivered) sessions.

**Flow:** Called to view all completed (delivered) sessions. Used for reporting and analytics.

**Response (200):**
```json
{
  "status": "success",
  "message": "Previous Sessions get successfully.",
  "session_detail": [...]
}
```

---

### 18.13 Delete Single Slot

**Endpoint:** `GET|POST /api/v1/delete-single-slot-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called when removing a specific availability slot. Cannot delete if a session is already booked for that slot.

**Request Body:**
```json
{
  "date": "2024-01-20",
  "time": "10:00 AM - 11:00 AM"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Slots deleted successfully."
}
```

---

### 18.14 Delete Slots Between Two Dates

**Endpoint:** `GET|POST /api/v1/delete-slot-between-two-dates-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called to bulk-remove slots across a date range.

**Request Body:**
```json
{
  "first_date": "2024-01-20",
  "last_date": "2024-01-25"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Slots between two dates deleted successfully."
}
```

---

### 18.15 Add Slots

**Endpoint:** `GET|POST /api/v1/add-slots-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Description:** Add availability slots. Supports comma-separated dates and times.

**Flow:** Called to add availability slots. Accepts comma-separated dates and times and creates all combinations. Uses firstOrCreate to prevent duplicates.

**Request Body:**
```json
{
  "date": "2024-01-20,2024-01-21",
  "time": "10:00 AM - 11:00 AM,02:00 PM - 03:00 PM"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Slots add successfully."
}
```

> Note: Combines every date with every time (cartesian product). Uses `firstOrCreate` to avoid duplicates.

---

### 18.16 Submit Opinion After Session (Psychologist)

**Endpoint:** `GET|POST /api/v1/submit-opinion-after-session-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called after a session to submit the psychologist's clinical opinion (session status, complaints, summary, homework, next plan).

**Request Body:**
```json
{
  "session_id": 1,
  "session_status": "completed",
  "presenting_complaints": "Anxiety issues",
  "session_summary": "User responded well to CBT",
  "hardword_asigned": "Journaling exercises",
  "plan_for_next_session": "Continue treatment plan"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Opinion submit successfully."
}
```

---

### 18.17 Submit Session Note

**Endpoint:** `GET|POST /api/v1/submit-session-note-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Description:** Submit detailed clinical session notes.

**Flow:** Called to submit detailed clinical session notes including case history, diagnosis, treatment plan. More detailed than the opinion — used for medical records.

**Request Body:**
```json
{
  "session_id": 1,
  "case_history": "Patient history...",
  "username": "johndoe",
  "time": "10:00 AM",
  "duration": "45 minutes",
  "name_of_therapist": "Dr. John",
  "age": "25",
  "gender": "male",
  "occupation": "Software Engineer",
  "qualification": "Graduate",
  "presenting_complaints": "Anxiety and stress",
  "past_psychology_history": "None",
  "medical_history": "None",
  "family_psychological_histroy": "None",
  "session_summary": "Discussed coping strategies",
  "diagnosis": "Generalized Anxiety Disorder",
  "plan_for_therpy_treatment": "CBT weekly sessions"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Notes has been submit successfully for this session"
}
```

---

### 18.18 User's Previous Sessions

**Endpoint:** `GET|POST /api/v1/users-previous-sessions-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called from the user detail screen to see all past sessions the psychologist has had with a specific user.

**Request Body:**
```json
{
  "user_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Users previous sessions get successfully.",
  "list": [
    {
      "id": 1,
      "user_id": 1,
      "psychologist_id": 1,
      "date": "2024-01-10",
      "time": "10:00 AM - 11:00 AM",
      "is_end": 1,
      "psychologistDetail": {
        "id": 1,
        "first_name": "John",
        "psy_profile": "https://s3.amazonaws.com/psychologist/profile.jpg"
      }
    }
  ]
}
```

---

### 18.19 Join Talk Room (Psychologist)

**Endpoint:** `GET|POST /api/v1/join-talk-room-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Description:** Get Twilio video access token and join a HappiTalk session. Sends notification to user when psychologist joins.

**Flow:** Called when the psychologist taps "Join" on a session. Returns a Twilio access token. On first join, sends user a push notification: "I'm waiting for you! Please Join in Quickly."

**Request Body:**
```json
{
  "session_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Token get sucessfully.",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

> Note: When psychologist joins for the first time, user gets push notification: "I'm waiting for you! Please Join in Quickly."

---

### 18.20 Get Session Note

**Endpoint:** `GET|POST /api/v1/get-session-note-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called to retrieve the clinical notes for a specific session. Includes session details and psychologist info.

**Request Body:**
```json
{
  "session_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Logs of session get successfully.",
  "data": {
    "id": 1,
    "session_id": 1,
    "case_history": "...",
    "session_summary": "...",
    "diagnosis": "...",
    "sessionDetail": {
      "id": 1,
      "date": "2024-01-15",
      "time": "10:00 AM - 11:00 AM",
      "psychologistDetail": {
        "id": 1,
        "first_name": "John",
        "psy_profile": "https://s3.amazonaws.com/psychologist/profile.jpg"
      }
    }
  }
}
```

---

### 18.21 Dashboard

**Endpoint:** `GET|POST /api/v1/dashboard-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Description:** Get dashboard statistics. Supports `type` parameter for date range filtering.

**Flow:** Called on the psychologist's home screen. Shows session statistics broken down by B2C/B2B. Can filter by time period (this_month, custom date range, or all-time).

**Request Body:**
```json
{
  "type": "this_month|custom"
}
```

**For custom date range:**
```json
{
  "type": "custom",
  "start_date": "2024-01-01",
  "end_date": "2024-01-31"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Dashboard data get successfully.",
  "data": {
    "b2c_booked_sessions": 10,
    "b2c_delivered_sessions": 8,
    "b2c_amount": 4000,
    "b2b_booked_sessions": 5,
    "b2b_delivered_sessions": 3,
    "b2b_amount": 1500
  }
}
```

> If no type is provided, returns all-time data.

---

### 18.22 Get Session Recording

**Endpoint:** `GET|POST /api/v1/get-session-recording-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called after a session to get the video recording URL. Poll until state changes from "processing" to "completed".

**Request Body:**
```json
{
  "session_id": 1
}
```

**Response (Completed - 200):**
```json
{
  "status": "success",
  "state": "completed",
  "url": "https://video.twilio.com/v1/Compositions/.../Media?Ttl=3600"
}
```

**Response (Processing - 200):**
```json
{
  "status": "success",
  "state": "processing"
}
```

---

### 18.23 User's All Talk Session Notes

**Endpoint:** `GET|POST /api/v1/users-all-talk_notes-by-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called from the user history screen. Shows all session notes the psychologist has submitted for a particular user across all sessions.

**Request Body:**
```json
{
  "user_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Notes list get successfully.",
  "list": [
    {
      "id": 1,
      "session_id": 1,
      "case_history": "...",
      "session_summary": "...",
      "diagnosis": "...",
      "created_at": "2024-01-15T10:45:00.000000Z"
    }
  ]
}
```

---

## 19. Psychologist HappiGuide APIs

**Flow Context:** Psychologist-side HappiGuide session management. Similar to HappiTalk but for guided sessions. **Call order:** `happiguide-session-psy` (view sessions) → `join-guide-room-psy` (join call) → `happiguide-session-mark-as-completed-psy` (end) → `submit-guide-session-note-psy` (notes).

All endpoints require **psychologist authentication** (`psychologist` middleware).

### 19.1 HappiGuide Sessions

**Endpoint:** `GET|POST /api/v1/happiguide-session-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Description:** Get all HappiGuide sessions with search on user nickname.

**Flow:** Called to view all HappiGuide sessions assigned to the psychologist. Supports user search.

**Request Body:**
```json
{
  "search": "john"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Psychologist Guide session get successfully.",
  "session_detail": {
    "data": [
      {
        "id": 1,
        "user_id": 1,
        "psychologist_id": 1,
        "date": "2024-01-20",
        "time": "10:00 AM - 11:00 AM",
        "is_end": 0,
        "userDetail": {
          "id": 1,
          "nickname": "JohnUser"
        }
      }
    ],
    "current_page": 1,
    "last_page": 1,
    "per_page": 20,
    "total": 1
  }
}
```

---

### 19.2 Join Guide Room (Psychologist)

**Endpoint:** `GET|POST /api/v1/join-guide-room-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Description:** Get Twilio video access token for HappiGuide session.

**Flow:** Called when the psychologist joins a HappiGuide video session. Returns Twilio access token.

**Request Body:**
```json
{
  "session_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Token get sucessfully.",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

---

### 19.3 Mark Session as Completed (Guide)

**Endpoint:** `GET|POST /api/v1/happiguide-session-mark-as-completed-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called after the guide session ends. Sets is_end = 1.

**Request Body:**
```json
{
  "session_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Session has been mark completed successfully."
}
```

---

### 19.4 Submit Guide Session Note

**Endpoint:** `GET|POST /api/v1/submit-guide-session-note-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called to submit clinical notes for a HappiGuide session. Same structure as HappiTalk session notes.

**Request Body:**
```json
{
  "guide_session_id": 1,
  "case_history": "Patient history...",
  "username": "johndoe",
  "time": "10:00 AM",
  "duration": "30 minutes",
  "name_of_therapist": "Dr. John",
  "age": "25",
  "gender": "male",
  "occupation": "Engineer",
  "qualification": "Graduate",
  "presenting_complaints": "Work stress",
  "past_psychology_history": "None",
  "medical_history": "None",
  "family_psychological_histroy": "None",
  "session_summary": "Guided meditation session",
  "diagnosis": "Workplace stress",
  "plan_for_therpy_treatment": "Weekly guide sessions"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Notes has been submit successfully for this HappiGUIDE session."
}
```

---

### 19.5 Check Guide Room Participant

**Endpoint:** `GET|POST /api/v1/check-guide-room-participant-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called before marking session as complete. Verifies all participants have left the video room.

**Request Body:**
```json
{
  "session_id": 1
}
```

**Response (Participants present - 400):**
```json
{
  "status": "error",
  "message": "Ask user to end the call first."
}
```

**Response (Room empty - 200):**
```json
{
  "status": "success",
  "message": "Room is empty."
}
```

---

### 19.6 Get HappiGuide Session Recording

**Endpoint:** `GET|POST /api/v1/get-happiguide-session-recording-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called to retrieve the recording URL for a completed HappiGuide session.

**Request Body:**
```json
{
  "session_id": 1
}
```

**Response (200):**
```json
{
  "status": "success",
  "state": "completed",
  "url": "https://video.twilio.com/v1/Compositions/.../Media?Ttl=3600"
}
```

---

### 19.7 Submit Opinion After Guide Session (Psychologist)

**Endpoint:** `GET|POST /api/v1/submit-opinion-after-guide-session-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Flow:** Called after a guide session to submit the psychologist's feedback and plan.

**Request Body:**
```json
{
  "session_id": 1,
  "session_status": "completed",
  "presenting_complaints": "Work stress",
  "session_summary": "User responded well",
  "hardword_asigned": "Deep breathing exercises",
  "plan_for_next_session": "Continue guided sessions"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Opinion submit successfully."
}
```

---

## 20. Psychologist User Reports APIs
**Flow Context:** Allows psychologists to view their assigned users' HappiLIFE assessment reports. Called from the psychologist's user detail screen.

All endpoints require **psychologist authentication** (`psychologist` middleware).

### 20.1 Get User Report
**Flow:** Called when a psychologist wants to view a user's assessment results. Returns the report link if completed.

**Endpoint:** `GET|POST /api/v1/get-user-report-by-psy`

**Headers:** `Authorization: Bearer {psychologist-token}`

**Description:** Get a user's HappiLIFE Awareness Tool assessment report.

**Request Body:**
```json
{
  "user_id": 1
}
```

**Response (Completed - 200):**
```json
{
  "status": "success",
  "message": "Assesment is completed.",
  "report_link": "https://s3.amazonaws.com/reports/assessment_1.pdf"
}
```

**Response (Not complete - 400):**
```json
{
  "status": "error",
  "message": "Assesment is not complete."
}
```

---

## 21. ChatBot APIs
**Flow Context:** These are public (no auth) endpoints used by the AI ChatBot feature. They provide the content/data that powers the chatbot's conversation topics, assessments, breathing exercises, and recommendations. Called by the chatbot UI as the user interacts with it.

Public endpoints (no authentication required).

### 21.1 Discussion Topics
**Flow:** Called when the chatbot loads initial conversation starters. Shows topics the user can select to begin a discussion.

**Endpoint:** `GET /api/v1/chat-bot/discussion-topics`

**Response (200):**
```json
{
  "status": "success",
  "message": "Discussion topics retrieved successfully.",
  "content": [
    {"id": 1, "description": "Topic description..."}
  ]
}
```

---

### 21.2 Suicidal Thoughts
**Flow:** Called when the chatbot detects or the user mentions self-harm/suicidal thoughts. Returns a crisis help message. This is a safety-critical endpoint.

**Endpoint:** `GET /api/v1/chat-bot/suicidal-thoughts`

**Response (200):**
```json
{
  "status": "success",
  "message": "Suicidal thoughts help message retrieved successfully.",
  "content": {
    "description": "If you're having thoughts of harming yourself..."
  }
}
```

---

### 21.3 Assessment Concerns
**Flow:** Called to populate the chatbot's concern/issue selector. The user picks what they're struggling with and the chatbot tailors its questions accordingly.

**Endpoint:** `GET /api/v1/chat-bot/assessment-concerns`

**Response (200):**
```json
{
  "status": "success",
  "message": "Assessment concerns retrieved successfully.",
  "content": [
    "Stressful Situations",
    "Constant Worrying",
    "Feeling Low",
    "Sleep Issues",
    "Relationship Challenges",
    "Low Self Confidence",
    "Anger Management",
    "Getting Bullied",
    "Body Image Issues",
    "Work Life Balance",
    "Frequent Loneliness",
    "Wavering Motivation",
    "Gaining Life Satisfaction",
    "Managing Emotions",
    "Seeking Happiness",
    "Pregnancy related Anxiety",
    "Loss in Life",
    "Exam related Anxiety",
    "Traumatic Past Events"
  ]
}
```

---

### 21.4 Assessment Questions
**Flow:** Called when the chatbot begins an assessment flow. Returns questions grouped by category (Stress, Worry, etc.) based on the selected concern.

**Endpoint:** `GET /api/v1/chat-bot/assessment-questions`

**Response (200):**
```json
{
  "status": "success",
  "message": "Assessment questions retrieved successfully.",
  "content": {
    "Stress": [
      "In the last month, how often have you been upset...",
      "..."
    ],
    "Worry": [
      "Over the last 2 weeks, how often have you felt nervous...",
      "..."
    ]
  }
}
```

---

### 21.5 Recommendation Categories
**Flow:** Called to get available recommendation categories for the chatbot's suggestion engine. Used alongside the assessment results.

**Endpoint:** `GET /api/v1/chat-bot/recommendation-categories`

**Response (200):**
```json
{
  "status": "Success",
  "message": "Recommendations retrieved successfully.",
  "content": [...]
}
```

---

### 21.6 Recommendations
**Flow:** Called with the user's profile and a selected category to get personalized content recommendations. Used after the chatbot assessment is complete.

**Endpoint:** `GET /api/v1/chat-bot/recommendations`

**Description:** Get recommendations based on user profile and category. Requires query parameters.

**Query Parameters:**
```
?profile={user_profile_id}&category={recommendation_category_id}
```

**Response (200):**
```json
{
  "status": "Success",
  "message": "Recommendations retrieved successfully.",
  "content": [...]
}
```

---

### 21.7 Square Breathing Video
**Flow:** Called when the user requests a breathing exercise from the chatbot. Returns a video URL for the guided square breathing exercise.

**Endpoint:** `GET /api/v1/chat-bot/square-breathing`

**Response (200):**
```json
{
  "status": "Success",
  "message": "Video URL retrieved successfully.",
  "content": "https://your-domain.com/video/square-breathing.mp4"
}
```

---

## 22. ChatBot Assessment APIs
**Flow Context:** These are the backend CRUD endpoints for the chatbot's assessment system. They serve data to the chatbot UI and accept assessment results. Public endpoints (no auth required).

Public endpoints (no authentication required).

### 22.1 Categories
**Flow:** Called to get all assessment categories with optional filtering (by id, name, calculation step).

**Endpoint:** `GET /api/v1/chat-bot/categories`

**Query Parameters (optional filters):**
```
?id=1&name=Stress&calculation_step_macro=sum
```

**Response (200):**
```json
{
  "status": "Success",
  "message": "Chat bot categories retrieved successfully.",
  "categories": [...]
}
```

---

### 22.2 Questions
**Flow:** Called to get chatbot assessment questions with optional filters (by id, language, category). Questions include their answer options with scores.

**Endpoint:** `GET /api/v1/chat-bot/questions`

**Query Parameters (optional filters):**
```
?id=1&language=english&chat_bot_category_id=1
```

**Response (200):**
```json
{
  "status": "Success",
  "message": "Chat bot questions retrieved successfully.",
  "questions": [
    {
      "id": 1,
      "question": "Question text...",
      "options": [
        {"id": 1, "option": "Never", "score": 0}
      ]
    }
  ]
}
```

---

### 22.3 Options
**Flow:** Called to get answer options for chatbot questions. Supports filtering by question id, option text, and score value. Note: this endpoint maps to the same controller method as `questions`.

**Endpoint:** `GET /api/v1/chat-bot/options`

**Query Parameters (optional filters):**
```
?id=1&chat_bot_question_id=1&option=Never&score=0
```

**Response (200):**
```json
{
  "status": "Success",
  "message": "Chat bot options retrieved successfully.",
  "options": [...]
}
```

---

### 22.4 Report Characteristics
**Flow:** Called to get report characteristics/descriptions for assessment categories. Used to generate the assessment report text.

**Endpoint:** `GET /api/v1/chat-bot/report-characteristics`

**Query Parameters (optional filters):**
```
?id=1&chat_bot_category_id=1
```

**Response (200):**
```json
{
  "status": "Success",
  "message": "Chat bot report characteristics retrieved successfully.",
  "report_characteristics": [...]
}
```

---

### 22.5 Assessments
**Flow:** Called to retrieve stored chatbot assessment results. Supports filtering by id, user_id, category_id, and score.

**Endpoint:** `GET /api/v1/chat-bot/assessments`

**Query Parameters (optional filters):**
```
?id=1&user_id=1&chat_bot_category_id=1&score=50
```

**Response (200):**
```json
{
  "status": "Success",
  "message": "Chat bot assessments retrieved successfully.",
  "assessments": [...]
}
```

---

### 22.6 Add Assessment
**Flow:** Called when the user completes a chatbot assessment. Saves the user's results (category + score) to the database.

**Endpoint:** `POST /api/v1/chat-bot/add-assessment`

**Request Body:**
```json
{
  "user_id": 1,
  "chat_bot_category_id": 1,
  "score": 75
}
```

**Response (200):**
```json
{
  "status": "Success",
  "message": "Chat bot assessment addedd successfully.",
  "assessment": {
    "id": 1,
    "user_id": 1,
    "chat_bot_category_id": 1,
    "score": 75
  }
}
```

---

## 23. Score & Prompt APIs
**Flow Context:** These are public endpoints for AI/voice analysis features. The Prompt List is used by the AI chatbot for generating responses. The Score endpoint saves voice analysis results from AI-powered assessments.

Public endpoints.

### 23.1 Prompt List
**Flow:** Called to get the list of system prompts used by the AI chatbot or voice analysis system.

**Endpoint:** `GET /api/v1/prompt-list`

**Response (200):**
```json
{
  "status": "success",
  "message": "Prompts retrieved successfully.",
  "list": [...]
}
```

---

### 23.2 Save Score
**Flow:** Called by the voice analysis service after processing a user's voice recording. Saves voice metrics (smoothness, liveliness, clarity, speech rate, etc.) and an overall result.

**Endpoint:** `POST /api/v1/score`

**Description:** Save voice analysis score (used for AI/voice assessment).

**Request Body:**
```json
{
  "user_id": 1,
  "result": "positive",
  "score": 85.5,
  "smoothness": 0.75,
  "liveliness": 0.82,
  "control": 0.65,
  "energy_range": 0.70,
  "clarity": 0.88,
  "crispness": 0.79,
  "speech_rate": 150,
  "pause_duration": 0.3,
  "inferred_at": "2024-01-15T10:30:00Z"
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Score saved successfully."
}
```

---

## 24. Payment Webhooks & Links
**Flow Context:** These are server-to-server and redirect endpoints that handle the RazorPay payment lifecycle. Not called directly by the mobile app — they are the redirect URLs returned by the payment endpoints (section 6) and the webhook URL registered with RazorPay.

### 24.1 Handle Webhook
**Flow:** Called by RazorPay after payment events (e.g., payment.captured). Verifies the digital signature and processes the payment. This is the server-side callback that actually activates subscriptions.

**Endpoint:** `GET|POST /api/v1/handle-webhook`

**Description:** RazorPay webhook handler. Verifies signature and processes `payment.captured` events.

**Request:** RazorPay webhook payload with `x-razorpay-signature` header.

**Response (200):**
```json
{
  "success": true
}
```

---

### 24.2 Payment Link (General)
**Flow:** Redirect URL opened in the user's browser/webview. Shows the RazorPay checkout page for general plan purchases. Parameters are embedded in the URL path.

**Endpoint:** `GET|POST /api/v1/payment-link/{order_id}/{user_id}/{plan_id}/{coupen_id}`

**Description:** Redirects to RazorPay payment page for general plan purchase.

---

### 24.3 Success Payment Page (General)
**Flow:** Called after RazorPay payment completes. Creates BundleStatus records, assigns reward points, and sends confirmation notifications. User is redirected here after successful payment.

**Endpoint:** `GET|POST /api/v1/success-payment-page/{order_id}/{user_id}/{plan_id}/{coupen_id}`

**Description:** Handles successful RazorPay payment callback. Creates BundleStatus records and assigns reward points.

---

### 24.4 Payment Link (HappiTalk)
**Flow:** RazorPay checkout page for HappiTalk session payment. URL embeds all session parameters (psychologist_id, date, time, sessions, recording permission).

**Endpoint:** `GET|POST /api/v1/payment-link-for-happitalk/{order_id}/{user_id}/{plan_id}/{psychologist_id}/{date}/{time}/{session}/{user_recording_permission}/{coupen_id}`

---

### 24.5 Success Payment Page (HappiTalk)
**Flow:** Post-payment handler for HappiTalk. Creates the booking record, calculates commission/TDS for the psychologist, creates the session entry, and sends notifications to both user and psychologist.

**Endpoint:** `GET|POST /api/v1/success-payment-page-for-happitalk/{order_id}/{user_id}/{plan_id}/{psychologist_id}/{date}/{time}/{session}/{user_recording_permission}/{coupen_id}`

**Description:** Handles successful HappiTalk payment. Creates booking, session, calculates commission/TDS, and sends notifications.

---

### 24.6 Payment Link (HappiGuide)
**Flow:** RazorPay checkout page for HappiGuide session payment.

**Endpoint:** `GET|POST /api/v1/payment-link-for-happiguide/{order_id}/{user_id}/{plan_id}/{date}/{time}/{coupen_id}`

---

### 24.7 Success Payment Page (HappiGuide)
**Flow:** Post-payment handler for HappiGuide. Assigns a psychologist via round-robin from psychologists mapped to HappiGuide, creates the session, and sends notifications.

**Endpoint:** `GET|POST /api/v1/success-payment-page-for-happiguide/{order_id}/{user_id}/{plan_id}/{date}/{time}/{coupen_id}`

**Description:** Handles successful HappiGuide payment. Assigns psychologist via round-robin, creates session, and sends notifications.

---

## 25. Error Responses
**Flow Context:** Standard error response formats used across all API endpoints.

### Common Error Codes

| HTTP Status | Description |
|-------------|-------------|
| 200 | Success |
| 400 | Bad Request / Validation error |
| 401 | Unauthorized - Invalid or missing token |
| 404 | Not Found |
| 422 | Validation Error (Laravel default) |
| 500 | Internal Server Error |

### Error Response Formats

**Validation Error (400):**
```json
{
  "message": "Please enter valid email address."
}
```

**Authentication Error (401):**
```json
{
  "status": "failed",
  "message": "Invalid username and password.",
  "error": "Unauthorized"
}
```

**General Error (400):**
```json
{
  "status": "error",
  "message": "Error description here"
}
```

---

## Notes
**Flow Context:** General notes about the API conventions, authentication, formats, and service providers used across the platform.

1. **Authentication:** All protected endpoints require a valid JWT token in the `Authorization` header as `Bearer {token}`.
2. **Request Methods:** Most endpoints accept both GET and POST methods (using `Route::match`).
3. **Date Format:** Use `YYYY-MM-DD` format for all date fields.
4. **Time Format:** Use `HH:MM AM/PM - HH:MM AM/PM` (e.g., `10:00 AM - 11:00 AM`) for time slots.
5. **Device Token:** Firebase/Expo push notification token. Include during login for push notifications.
6. **Profile Pictures:** Stored on AWS S3, accessed via signed URLs.
7. **Push Notifications:** Uses Expo Push API (`https://exp.host/--/api/v2/push/send`).
8. **Video:** Powered by Twilio Video API with group rooms and recording compositions.
9. **Payments:** Processed via RazorPay with INR currency.
10. **Reward Points:** Users earn points for various actions (assessment, messaging, feedback, etc.).

---

**Last Updated:** June 2026
