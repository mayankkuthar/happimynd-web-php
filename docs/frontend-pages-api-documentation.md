# Frontend Pages — API Endpoint Documentation

This document describes every backend endpoint used by the **frontend (web/PWA) pages** of the Happimynd website. It covers **payment services**, the **HappiLIFE assessment (screening)**, and **all other** frontend flows (auth, profile, psychologist listing, services, contact, etc.).

> **Note:** The `/api/v1/*` endpoints are consumed by the **mobile app**. They are listed here only where a frontend page uses them. The web frontend (Blade views) calls the **web routes** and three legacy `/api/...` routes (see [Legacy API Routes Used By Frontend](#legacy-api-routes-used-by-the-frontend)).

---

## Table of Contents

1. [How Frontend Pages Call Backend Endpoints](#how-frontend-pages-call-backend-endpoints)
2. [Common Conventions](#common-conventions)
3. [Payment Services](#payment-services)
   - [Payment Pages & Flows](#payment-pages--flows)
   - [Web Payment Endpoints](#web-payment-endpoints)
   - [Payment Link / Success Pages](#payment-link--success-pages)
   - [Razorpay Checkout & Callbacks](#razorpay-checkout--callbacks)
   - [Coupon Endpoints](#coupon-endpoints)
   - [Campaign Payment](#campaign-payment)
   - [Other Services Payment](#other-services-payment)
4. [HappiLIFE Assessment (Screening)](#happilife-assessment-screening)
   - [Screening Page & Flow](#screening-page--flow)
   - [Assessment Endpoints](#assessment-endpoints)
   - [Verification Popups (Report Access)](#verification-popups-report-access)
   - [Report Pages](#report-pages)
5. [Authentication & Account](#authentication--account)
   - [Signup / Login](#signup--login)
   - [Guardian (Under-18) OTP Flow](#guardian-under-18-otp-flow)
   - [Forgot Password](#forgot-password)
   - [Profile & Password](#profile--password)
6. [Dashboard & Misc User Endpoints](#dashboard--misc-user-endpoints)
7. [Psychologist Listing & Booking](#psychologist-listing--booking)
8. [Services & Content Pages](#services--content-pages)
9. [Legacy API Routes Used by the Frontend](#legacy-api-routes-used-by-the-frontend)
10. [Known Issues / Discrepancies](#known-issues--discrepancies)
11. [File Reference Index](#file-reference-index)

---

## How Frontend Pages Call Backend Endpoints

The frontend is a set of **Laravel Blade views** (`resources/views/Frontend/**`) served by `routes/web.php`. Pages make calls using:

- **jQuery `$.ajax` / `$.get` / `$.post`** — most dynamic endpoints (see `public/assets/Frontend/js/main.js`, `verify-popups.js`, `services.js`).
- **Native HTML form submissions** — e.g. signup, login, payment bundle forms (submit to web routes, usually via the shared `formSubmitAjaxEvent` helper in `main.js`).
- **`fetch()`** — only one instance exists and it is commented out (`layouts/app.blade.php:374`).
- **Page navigation / links** — server-rendered endpoints that return full views.

**Base URL:** `APP_URL` from `.env` (production `https://happimynd.com`). Web routes have no prefix; API routes are prefixed with `/api`.

---

## Common Conventions

### Response Envelope (web)
Most web endpoints return one of:

```json
{ "error": false, "message": "..." }        // success (message may be an object/array)
{ "error": true, "message": "..." }         // failure (HTTP 401)
{ "errors": { "field": ["message"] } }      // validation failure (HTTP 422)
```

> Exceptions: `/get-available-dates` returns a bare JSON array; `/get-psychologists/` returns a JSON **string**; `/api/username-exists/` returns `{ "flag": ... }`.

### Authentication
- **`jwt.verify:user` middleware** protects logged-in user routes. The JWT is stored in a cookie named `user-access-token`.
- Public routes (signup, login, OTP, payment-link/success pages) need no auth.

### Payment Gateway
- **Razorpay** is used for all payments.
- Amounts are converted to **paise** (`amount * 100`) when creating Razorpay orders.
- Config keys: `RAZORPAY_KEY`, `RAZORPAY_SECRET` (in `.env`). Webhook secret: `WEBHOOK_SECRET`.

---

## Payment Services

### Payment Pages & Flows

| Page | Route | Purpose |
|---|---|---|
| **Buy Bundles** | `GET /buy-bundles` (`user.payment.buyBundle`) | `PaymentController@buyBundle` (L38) renders `Frontend/payment/payment_bundle.blade.php` with all packages/plans/offers. |
| **Subscribed Services** | `GET /subscribedservices` (`user.subscribedServices`) | `PaymentController@subscribedServices` (L775) renders `Frontend/payment/subscribed_services.blade.php`. |
| **Psychologist page** | `GET /psychologist` | `PsychologistController@getPsychologistView` — includes book-a-session / payment flow. |
| **Other Services** | `GET /other-services/{id}` | `UserController@showOtherServices` — service detail with buy link. |

### Web Payment Endpoints

#### `GET /payment/orderBundle` — Create bundle order (Razorpay checkout)
- **Controller:** `PaymentController@orderBundle` (`app/Http/Controllers/PaymentController.php:78`) · route name `payment.orderBundle` (web.php:169)
- **Auth:** Logged-in user (called from authed pages) / session user
- **Called by:** `payment_bundle.blade.php:13` (form), `subscribed_services.blade.php:13` (form)
- **Request params (query):**
  - `user_id` (int) — current user
  - `plan[]` (array of plan ids) — selected plans
  - `psychologist_plan_id` (int, optional) — for psychologist booking
  - `psychologist_id` (int, optional)
  - `psychologist_session` (int, optional)
  - `coupon_code` (string, optional)
- **Behavior:**
  1. Loads plans + offers; rejects plans already subscribed (redirects to `user.dashboard`).
  2. Price = `plan->getSellingPriceWithDiscount($coupon_code)` per plan; B2B org users get `0` for free entitlements.
  3. If total `amount <= 0` → creates `BundleStatus` directly and redirects to `/dashboard` or `/subscribedservices`.
  4. Else → `PaymentService::paymentRequest(...)` creates a `Receipt` + `ReceiptPackage` + Razorpay order and renders the Razorpay checkout view (`payment/paymentRequest.blade.php`).
- **Response:** Razorpay embedded-checkout view, or redirect.

#### `GET /payment/book-psychologist` — Create psychologist booking payment
- **Controller:** `PaymentController@bookPsychologist` (`PaymentController.php:472`) · route name `payment.bookPsychologist` (web.php:173)
- **Called by:** `psychologist.blade.php:102` (form; currently the submit is commented out — see [Known Issues](#known-issues--discrepancies))
- **Request params:** `user_id`, `psychologist_id`, `plan_id`, `coupon_id` (optional)
- **Behavior:** validates the plan belongs to the psychologist, applies coupon discount (`isValidCoupon`), then `paymentService->paymentRequest(..., route('payment.psychologistPaymentResponse'), ['psychologist_id' => $id])`.
- **Response:** Razorpay checkout view.

#### `POST /other-services-mail` — Other/Education service "buy" (mail list + payment)
- **Controller:** `UserController@saveOtherServicesMailList` (`app/Http/Controllers/UserController.php:1485`) · route name `OtherServicesMailList.post` (web.php:191)
- **Called by:** `includes/popups/other_services/emailinput.blade.php:14`, `includes/popups/education_services/emailinput.blade.php:15`, `services.js:17-26`
- **Request body (form/JSON):** `other_service` (OtherService id), `name`, `email`, `mobile`
- **Behavior:** creates an `OtherServiceSubscriber`, computes the discounted amount, then `PaymentService::paymentServiceRequest($amount, $details, route('payment.responseOtherServices'))` → `ServicesReceipt` + Razorpay order → checkout view.
- **Response:** Razorpay checkout view.

#### `GET /other-services/{id}` — Service detail (used for coupon popup)
- **Controller:** `UserController@showOtherServices` (`UserController.php:1549`) · route name `otherservices.show` (web.php:190)
- **Called by:** `main.js:520-553` (`showCouponInfo`)
- **Response:** `OtherService` model JSON (exposes `coupon`, `discount`, `buy_link`).

#### `POST /other-services-payment` — (Broken) other service payment
- **Controller:** **does not exist** — `PaymentController` has no `otherServicePayment` method. Only referenced by legacy `services.js:58-102` (`paymentOrder()`). Would return 500. See [Known Issues](#known-issues--discrepancies).

### Payment Link / Success Pages

These are server-rendered pages used in the **mobile API** payment flow (`/api/v1/...`). They are returned as the `link` value by the API payment endpoints, so the mobile app opens them in a WebView / browser. They render a Razorpay checkout (`payment/paymentRequestApp.blade.php`).

| Endpoint (route) | Controller @method | Purpose |
|---|---|---|
| `GET/POST /api/v1/payment-link/{order_id}/{user_id}/{plan_id}/{coupen_id}` | `api\v1\PaymentController@paymentLink` (L193) | Renders Razorpay checkout for a generic bundle purchase. |
| `GET/POST /api/v1/payment-link-for-happitalk/{order_id}/{user_id}/{plan_id}/{psychologist_id}/{date}/{time}/{session}/{user_recording_permission}/{coupen_id}` | `paymentLinkForHappitalk` (L606) | Checkout for a HappiTALK session booking. |
| `GET/POST /api/v1/payment-link-for-happiguide/{order_id}/{user_id}/{plan_id}/{date}/{time}/{coupen_id}` | `paymentLinkForHappiguide` (L846) | Checkout for a HappiGUIDE session booking. |

Corresponding **success/callback pages** (verify Razorpay payment and create entitlements):

| Endpoint | Controller @method | Behavior |
|---|---|---|
| `GET/POST /api/v1/success-payment-page/{order_id}/{user_id}/{plan_id}/{coupen_id}` | `successPaymentPage` (L208) | Creates `BundleStatus` (+ child plans if bundle) and `CouponReceipt`; returns `payment-success-page`. |
| `GET/POST /api/v1/success-payment-page-for-happitalk/{order_id}/{user_id}/{plan_id}/{psychologist_id}/{date}/{time}/{session}/{user_recording_permission}/{coupen_id}` | `successPaymentPageForHappitalk` (L619) | Creates `BundleStatus`, `HappitalkSession`, computes psychologist **commission + TDS payout**, reward points, notifications. |
| `GET/POST /api/v1/success-payment-page-for-happiguide/{order_id}/{user_id}/{plan_id}/{date}/{time}/{coupen_id}` | `successPaymentPageForHappiguide` (L859) | Creates `BundleStatus`, round-robin `AssignPsyToPlan`, `HappiguideSession`, reward points, notifications. |

> ⚠️ These payment-link/success URLs **contain user ids and payment data in the URL** and are intentionally unauthenticated (they must be openable from the Razorpay browser redirect).

### Razorpay Checkout & Callbacks

**Embedded checkout (external):** `POST https://api.razorpay.com/v1/checkout/embedded`
- Views: `resources/views/payment/paymentRequest.blade.php`, `payment/paymentRequestApp.blade.php`
- Params: `key_id`, `order_id`, `name`, `description`, `prefill[name|contact|email]`, `callback_url`, `cancel_url`.

**Web callback routes (server-side, verify + finalize):**

| Endpoint | Controller @method | Behavior |
|---|---|---|
| `ANY /payment/responseBundle` | `PaymentController@responseBundle` (L600) | `PaymentService::getPaymentResponse` verifies Razorpay `payment.captured`, marks receipt, creates `BundleStatus` per plan + `PsychologistAppointment` (HappiTALK), Bitrix sync, redirects to `/subscribedservices`. |
| `ANY /payment/psychologist-payment-response` | `PaymentController@psychologistPaymentResponse` (L526) | Same pattern for psychologist bookings; redirects to `/dashboard`. |
| `POST /payment/response-other-services` | `PaymentController@responseOtherServices` (L872) | `getServicePaymentResponse` marks the `OtherServiceSubscriber` paid; redirects back to the service page. |

**Razorpay webhook (server-side):**
| Endpoint | Controller @method | Behavior |
|---|---|---|
| `GET/POST /api/v1/handle-webhook` | `api\v1\PaymentController@handleWebhook` (L989) | Verifies HMAC-SHA256 signature (`x-razorpay-signature`, `WEBHOOK_SECRET`); on `payment.captured` sets `Receipt.status = 1`. Returns `{ "success": true }` or 400. |

### Coupon Endpoints

#### `POST /verify-coupon` — Verify & apply a coupon on web
- **Controller:** `CouponController@verifyCoupon` (`app/Http/Controllers/CouponController.php:24`) · route name `user.verify-coupon` (web.php:148, inside `jwt.verify:user` group)
- **Called by:** `payment_bundle.blade.php:427-430`, `subscribed_services.blade.php:538-541` (jQuery `$.ajax` POST), `psychologist.blade.php:931-934` (uses GET — ⚠️ see [Known Issues](#known-issues--discrepancies))
- **Request body:** `code` (string), `plan_id` (array of plan ids); header `X-CSRF-TOKEN`.
- **Logic:** `CouponService::verifyCoupon($code, $plan_ids)` (`app/Services/CouponService.php:11`) — checks active, `max_uses`, expiry, per-user usage, and plan applicability.
- **Response:**
  - Success: `{ error: false, discount: <percent>, msg: "Coupon Applied", ... }` (frontend also consumes `msg.plans`, `msg.coupon_plan`, `msg.coupon_plan_ids` in the bundle pages).
  - Failure: `{ error: true, msg: "Coupon Expired" | "Coupon already used" | "Invalid Coupon code" | "Following plans to be selected ..." }`.

#### `GET/POST /api/v1/apply-coupon` — Apply coupon (mobile app)
- **Controller:** `api\v1\PaymentController@applyCoupon` (L318) · auth: `auth:api`
- **Request:** `plan_id` (required), `coupon` (required)
- **Response:** `{ status: "success", message, data: { coupon_id, plan_id, discount } }`; errors (HTTP 400): `Invalid Coupon`, `Coupon Expired`, `Coupon not belongs to these plan IDs.`

### Campaign Payment

| Endpoint | Controller @method | Behavior |
|---|---|---|
| `GET /campaign-payment` | `CampaignController@getPlansPage` (L101) | Builds campaign user; if `amount == 0` creates `BundleStatus`, else `PaymentService::paymentRequest` → checkout. |
| `GET /campaign/payment/orderBundle` | `CampaignController@orderBundle` — **method does not exist** (web.php:154) | ⚠️ Broken route. Form at `campaign_payment_bundle.blade.php:14` would error. |
| `ANY /campaign/payment/responseBundle` | `CampaignController@responseBundle` (L183) | Campaign payment callback. |

### Mobile API Payment Endpoints (for reference)

All in `app/Http/Controllers/api/v1/PaymentController.php`, guarded by `auth:api` (JWT) unless noted. These back the mobile app, and their `link` values point to the payment-link pages above.

| Method | Endpoint | Controller @method | Request params | Response |
|---|---|---|---|---|
| GET | `/api/v1/buy-plan` | `buyPlan` (L78) | — | `{status:'success', message, data: packages[]}` with `is_subscribed` 0/1/2. |
| GET/POST | `/api/v1/payment` | `payment` (L123) | `plan_id`*, `amount`*, `coupen_id`? | `{status, message, link}` → `/api/v1/payment-link/{...}` |
| GET/POST | `/api/v1/payment-for-happitalk` | `paymentForHappitalk` (L487) | `psychologist_id`*, `plan_id`*, `amount`*, `date`*, `time`*, `session`*, `user_recording_permission`*, `coupen_id`? | slot-availability errors OR `{status, message, link}` → `/api/v1/payment-link-for-happitalk/{...}` |
| GET/POST | `/api/v1/payment-for-happiguide` | `paymentForHappiguide` (L770) | `plan_id`*, `amount`*, `date`*, `time`*, `coupen_id`? | `{status, message, link}` → `/api/v1/payment-link-for-happiguide/{...}`; error if no `AssignPsyToPlan`. |
| GET/POST | `/api/v1/my-subscribed-services` | `mySubscribedServices` (L361) | — | `{status, message, data: packages[]}` |
| GET/POST | `/api/v1/avail-free-services` | `availFreeService` (L375) | `plan_id`*, `coupen_id`? | `{status:'success', message}` |
| GET/POST | `/api/v1/payment-for-ios` | `PaymentForIos` (L430) | `marchant_name`*, `plan_id`*, `amount`*, `transaction_id`*, `transaction_receipt`* | creates Receipt(status=1) + BundleStatus; `{status:'success', message}` |

---

## HappiLIFE Assessment (Screening)

### Screening Page & Flow

```
Dashboard ("Start Screening")
    ↓  GET /screening  (renders assessment page)
POST /start-assessment            → returns assessment_id
GET  /get-questions?assessment_id → returns page of questions + options
POST /save-option                 → saves answer (per question)
  (repeat until data.message == 'completed')
    ↓
Navigate to Dashboard (sessionStorage flag show_screening_complete_congrats_popup)
```

| Page | Route | Controller @method | Purpose |
|---|---|---|---|
| Screening page | `GET /screening` (`user.assessment`) | `UserController@assessment` (L509) | Renders `Frontend/assessment/assessment.blade.php`; redirects to dashboard if >6 completed assessments. |
| Dashboard | `GET /dashboard` | `UserController@dashboard` | Entry link to screening & reports. |

### Assessment Endpoints

#### `POST /start-assessment` — Start / resume assessment
- **Controller:** `AssessmentController@startAssessment` (`app/Http/Controllers/AssessmentController.php:29`) · route name `user.startAssessment` (web.php:131) · auth `jwt.verify:user`
- **Called by:** `includes/popups/assessment_instruction.blade.php:156-175` (`start_assessment()`)
- **Request body:** `user_id`, `_token`
- **Behavior:** creates/resumes an assessment via `AssessmentService->forUser()->initiateAssessment()`.
- **Response:** `{ "error": false, "message": { "assessment_id": <int> } }`

#### `GET /get-questions?assessment_id=<id>` — Fetch a page of questions
- **Controller:** `AssessmentController@getQuestions` (L38) · route name `user.getQuestions` (web.php:130, `Route::any`) · auth `jwt.verify:user`
- **Called by:** `assessment.blade.php:49-145` (`getQuestions()`)
- **Request params:** `assessment_id` (required, exists:assessments,id)
- **Response:**
  ```json
  {
    "data": [
      { "id": 1, "question": "…", "category": "…",
        "options": [ { "id": 1, "option": "…", "score": 1, "debugData": "…" } ],
        "debugData": "…" }
    ],
    "perPage": 10, "answered": 5, "total": 100, "current_page": 2
  }
  ```
  (Frontend uses `data.current_page`, `data.answered`, `data.total`, `data.perPage`, `data.data` to render paginated sections and the progress bar.)

#### `POST /save-option` — Save one answer
- **Controller:** `AssessmentController@saveAssessmentOption` (L49) · route name `user.saveOption` (web.php:132) · auth `jwt.verify:user`
- **Called by:** `main.js:415-443` (`scrollToNext()`), triggered from radio selection in `assessment.blade.php:155/160`
- **Request body:** `_token`, `option_question_id` (int, required, exists:option_questions,id), `assessment_id` (required)
- **Response:** `{ "error": false, "message": "…" }` — frontend treats `message == 'completed'` as end of assessment.

#### Mobile-app assessment endpoints (for reference)
`POST /api/v1/start-assessment` (`api\v1\UserAuthenticationController@startAssessment`) · `save-option` · `complete-assessment` · `checkifany` · `view-report` · `get-report` · `get-all-report` · `assessment-status` (api\v1\UserControllerApi) · `update-last-answer`. Full details are in `Psychologist_Mobile_App_API_Documentation.md`.

### Verification Popups (Report Access)

After an assessment, the dashboard shows popups that verify the user's **email** and **mobile** before granting report/reading access. Driven by `public/assets/Frontend/js/verify-popups.js`.

| # | Endpoint | Method | Controller @method | Purpose |
|---|---|---|---|---|
| 1 | `/verificationData` | GET | `UserController@verificationData` (L1354) · `user.verificationData.get` | Returns user + verify flags + latest assessment + purchased plans so JS decides which popup to show. |
| 2 | `/booked-dates` | GET | `UserController@getBookedDates` (L1392) · `user.bookedDates.get` | Returns booked/disabled dates for the calltime datepicker. |
| 3 | `/get-available-dates?date=<m-d-Y>` | GET | `UserController@getAvailableDates` (L1228) · `user.availableDates.get` | Returns available time slots for a date (**bare array** — not enveloped). |
| 4 | `/update-calltime` | POST | `AssessmentController@updateCalltime` (L119) · `user.updateCalltime` | Saves the chosen call slot; creates `AssessmentApprove` + Bitrix `addReportReadingToBitrix`. |
| 5 | `/update-email` | POST | `UserController@updateEmail` (L668) · `user.updateEmail` | Updates email (send-full-report popup). |
| 6 | `/update-mobile` | POST | `UserController@updateMobile` (L749) · `user.updateMobile` | Updates mobile + country (report-reading popup). |
| 7 | `/generate-otp-{type}` (`email`/`mobile`) | GET | `UserController@generateOTP` (L798) · `generateOTP` | Sends a 6-digit OTP to the logged-in user. |
| 8 | `/verify-{type}-otp` (`email`/`mobile`) | POST | `UserController@verifyOtpByCode` (L828) · `verifyOtpByCode` | Verifies the OTP; success message is `"Successfully, Email/Mobile OTP is verified.!"`. |

**Request/response detail:**

**1. `GET /verificationData`**
- Response:
  ```json
  {
    "error": false,
    "message": {
      "user":  { "id", "email", "mobile", "verify": { "email_verified": bool, "mobile_verified": bool },
                 "appointment_status": bool, "psychologist_appointment_status": "..." },
      "assessment": { "id", "ended_at", "report", "isCompleted": bool } | null,
      "plans": { "HappiLIFE Screening": { "percentage_covered": 100.0 }, "HappiLIFE Summary Reading": {...} }
    }
  }
  ```
- Frontend: `verify-popups.js:120-134`.

**2. `GET /booked-dates`**
- Response:
  ```json
  { "error": false, "message": {
      "booked_slots": "<json string>", "user_appointment_status": bool,
      "booked_dates": [ { "available_date": "…" } ],
      "disabled_dates": [ { "date": "…" } ] } }
  ```
- Frontend reads `data.message.disableDates` (⚠️ mismatch with `disabled_dates` — see [Known Issues](#known-issues--discrepancies)).

**3. `GET /get-available-dates?date=<m-d-Y>`**
- Response: `[ { "time": "10:00 AM", "id": 12 }, ... ]` (bare array) or `{ "error": true, "message": "No Available dates found.!" }`.
- Frontend: `verify-popups.js:378-396`.

**4. `POST /update-calltime`**
- Request body: `user_id`, `date` (m-d-Y), `slot`, `call_option`, `assessment_id`.
- Response: `{ "error": false, "message": "Successfully, call time updated.!" }`
- Frontend: `verify-popups.js:677-709`; form `verification/calltime.blade.php`.

**5. `POST /update-email`** — body: `user_id`, `email`, `useemail`, `subscribe`. Response: `{ "error": false, "message": "Successfully, Email updated.!" }` (frontend `verify-popups.js:453-491`).

**6. `POST /update-mobile`** — body: `user_id`, `mobile`, `country_id`. Response: `{ "error": false, "message": "Successfully, Mobile No updated.!" }` (frontend `verify-popups.js:523-561`).

**7. `GET /generate-otp-{type}`** — optional query `username` (forgot-password flow). Response: `{ "error": false, "message": "Successfully, Mobile/Email OTP is sent.!" }` (frontend `verify-popups.js:712-727`, `login.blade.php`).

**8. `POST /verify-{type}-otp`** — body: `otp` (6-digit), `user_id` (or `username` for forgot-password). Response: `{ "error": false, "message": "Successfully, Mobile/Email OTP is verified.!" }` (frontend `verify-popups.js:574-675`).

### Report Pages

| Endpoint | Method | Controller @method | Purpose |
|---|---|---|---|
| `/calculate-score?assessment_id=<id>` | GET | `AssessmentController@calculateAssessmentScore` (L60) · `calculateAssessmentScore` | Runs score calculation and renders the full report view (`Frontend/report/report.blade.php`). Linked from `downloadreport.blade.php:87`. |
| `/report-preview?assessment_id=<id>` | GET | `AssessmentController@reportPreview` (L108) · `user.reportPreview` | Renders `Frontend/report/reportPreview.blade.php`. |
| `/report` | GET | anonymous closure (web.php:213) | Renders `Frontend/report/report.blade.php` standalone. |
| `/download-report` | GET | `UserController@downloadReport` (L445) · `user.downloadReport` | Renders `Frontend/profilesetting/downloadreport.blade.php`. |
| `/report_app` page | — | `report_app.blade.php` | Mobile report print view (auto `window.print()`). |

> Report PDF generation happens only in the mobile API (`POST /api/v1/get-report`, Node service `NODE_URL`). Web report pages render server-side HTML and use `window.print()`.

---

## Authentication & Account

### Signup / Login

| Endpoint | Method | Controller @method | Auth | Called by | Purpose |
|---|---|---|---|---|---|
| `/verifyToken` | ANY | `UserController@verifyToken` (L121) · `verifyCode` | public | `signup1.blade.php:49` (`SponserSelectForm`) | Validates a HappiMynd/org code: `{ "error": false, "message": true }`. Query: `happimyndCode`, `organization_id`. |
| `/signup` | POST | `UserController@signup` (L132) · `user.signup.post` | public | `signup1.blade.php:70`, `signup2.blade.php:82` (`signupForm`) | Creates account + sets JWT cookie. Response: `{ "error": false, "message": { "route": "<url>/screening" } }`. |
| `/login` | POST | `UserController@signin` (L245) · `user.login.post` | public | `login.blade.php:45/82-90` (`signinForm`) | Logs in + sets JWT cookie. Response: `{ "error": false, "message": { "route": "<url>/dashboard" } }`; failure 401 `{ "errors": { "password": "invalid Password" } }`. |
| `/logout` | GET | `AuthController@userLogout` · `user.signout` | `jwt.verify:user` | header nav | Logs out. |

**Signup request body:** `signup_type` (`Campaign`/`individual`), `nickname`, `user_profile_id`, `age`, `confirmage`, `under_age`, `confirmcodeparent`, `sessionId`, `gender`, `username`, `password`, `password_confirmation`, `organization_id`, `happimyndCode`, `g-recaptcha-response`.

**Login request body:** `username`, `password`.

### Guardian (Under-18) OTP Flow

On signup for users under 18, parental/guardian consent is collected via OTP to the parent's email/phone.

| Endpoint | Method | Controller @method | Purpose |
|---|---|---|---|
| `/generate-guardian-otp-{type}` | POST | `UserController@generateSendGuardianOTP` (L1285) · `generateSendGuardianOTP` | Sends initial guardian OTP; creates `VerifyGuardian` + session. Body `{ "input": "<email|phone>" }` → `{ "error": false, "message": { "session_id": <int> } }`. Called via `verify-popups.js:752-797`. |
| `/generate-guardian-otp-{type}` | GET (ANY) | `UserController@generateGuardianOTP` (L1411) · `generateGuardianOTP` | Resends guardian OTP for an existing session. |
| `/verify-guardian-otp` | GET | `UserController@verifyGuardianOtpByCode` (L1339) · `verifyGuardianOtpByCode` | Verifies guardian OTP. Query: `session_id`, `otp` → `{ "error": false, "message": "Successfully, Email OTP is verifered.!" }`. Called from `signup1.blade.php:147-152` / `signup2.blade.php:212-231`. |

> ⚠️ Route registration order issue: `Route::any('generate-guardian-otp-{type}')` (web.php:162) is registered **before** `Route::post(...)` (web.php:164), so POST requests are actually handled by `generateGuardianOTP` (resend), not `generateSendGuardianOTP`. See [Known Issues](#known-issues--discrepancies).

### Forgot Password

| Step | Endpoint | Method | Controller @method | Purpose |
|---|---|---|---|---|
| 1 | `/api/username-exists/` | POST | `UserController@usernameExistOrNot` (L1115) · `check-username-exists` | Body `username`. Response: found+verified → `{ "flag": true, "email_permission": 0\|1, "mobile_permission": 0\|1, "username": "…" }`; unverified → `{ "flag": false, "status": "The entered username/e-mail has not been verified" }`; not found → `{ "flag": false, "status": "The entered username/e-mail is invalid." }`. Called by `forgetpassword/usercheck.blade.php:15` → `main.js:238-290`. |
| 0 | `/generate-otp-{type}?username=<u>` | GET | `UserController@generateOTP` | Pre-sends OTP. Called from `login.blade.php:98-117`. |
| 2 | `/verify-{type}-otp` | POST | `UserController@verifyOtpByCode` | Body `otp`, `username`. Called by `verifyotp.blade.php:14` / `verifyotpmobile.blade.php:14` → `main.js:150-226`. |
| 3 | `/api/forget-password-reset/` | POST | `UserController@forgetPasswordReset` (L1142) · `forgetPasswordReset` | Body `username`, `password1` (plus `password2`). Redirects to `user.loginView`. Called by `newpassword.blade.php:13`. |
| (alt) | `/verify-{type}/{user_id}/{otp}` | GET | `UserController@verifyOtpByLink` (web.php:167) | Link-based verification. |

> ⚠️ The `check-username-exists` and `forgetPasswordReset` routes are defined in `routes/api.php` (outside `/v1`), so their URLs are `/api/username-exists/` and `/api/forget-password-reset/` even for web pages.

### Profile & Password

| Endpoint | Method | Controller @method | Purpose |
|---|---|---|---|
| `/edit-profile` | GET / POST | `UserController@editProfileView` (L…) / `editProfile` (L…) · `user.editProfile` | View/update profile (form `profilesetting/editprofile.blade.php`). |
| `/change-password` | GET / POST | `UserController@changePasswordView` / `changePassword` (L…) · `user.changePassword` | Change password (form `profilesetting/changepassword.blade.php`). |
| `/raise-query` | POST | `UserController@postRaiseQuery` · `user.raiseQuery` | Submit a support query (popup `includes/popups/raisequery.blade.php`). |
| `/thrivecode` | GET | `UserController@thriveCode` · `user.thrivecode` | Thrive code page. |
| `/get-thrivecode` | GET | `UserController@getThriveCode` · `user.getThriveCode` | Fetch thrive code. |
| `/check-for-thrive-code` | ANY | `UserController@checkForThriveCode` (L948) · `user.checkForThriveCode` | Validates HappiApp/thrive code availability. Body `avail=1`. Response `{data, message, status}` with status 1-6. Called by `happiAppPopup.blade.php:7`, `exploreservices.blade.php:95`. |

---

## Dashboard & Misc User Endpoints

| Endpoint | Method | Controller @method | Purpose |
|---|---|---|---|
| `/dashboard` | GET | `UserController@dashboard` · `user.dashboard` | Dashboard page. |
| `/check-verify` | GET | `UserController@checkVerify` · `user.checkVerify` | Verification status check. |
| `/mark-as-read` | GET | `NotificationController@markAsRead` · `user.markNotificationsAsRead` | Marks notifications read. |
| `/get-service-button-data` | GET | `UserController@getServiceButtonData` (web.php:81) | Landing/footer service buttons; expects `result.success==1`, maps `result.data[4]→#life1`, `[0]→#app1`, `[3]→#chat1`, `[1]→#talk1`, `[2]→#space1`. Called by `footer.blade.php:124-126`. |
| `/get-available-dates` | GET | `UserController@getAvailableDates` (L1228) | See [Verification Popups](#verification-popups-report-access). |

---

## Psychologist Listing & Booking

| Endpoint | Method | Controller @method | Auth | Called by | Purpose |
|---|---|---|---|---|---|
| `/psychologist` | GET | `PsychologistController@getPsychologistView` (L…) · `user.psychologist` | `jwt.verify:user` | Page render (`psychologist.blade.php:718` builds `?search=` filters) | Psychologist listing page. |
| `/api/get-psychologists/` | GET | `PsychologistController@getPsychologists` (L529) · `getPsychologists` (defined api.php:53) | public | `psychologist.blade.php:694-708` (`loadMorePsychologist()`) | Infinite-scroll listing. Sends filter query params (`search`, `expert_category`, `specialization`, `language`, `city`) + `_token` + `page_number`. Response is a **JSON string** `{status, error_code, msg, psychologists}` (paginated 10/page); JS does `JSON.parse(data)`. |
| `/psychologist-available-dates` | GET | `PsychologistController@getPsychologistAvailableDates` · `user.psychologistAvailableDates.get` | `jwt.verify:user` | `psychologist_popups.blade.php:291` | Available dates for appointment. |
| `/psychologist-save-appointment` | POST | `PsychologistController@updatePsychologistAppointment` · `user.updatePsychologistAppointment.post` | `jwt.verify:user` | `psychologist_popups.blade.php:115` (`psychologist_appointment_form`) | Saves the psychologist appointment. |

---

## Services & Content Pages

| Endpoint | Method | Controller @method | Purpose |
|---|---|---|---|
| `/services` | GET | `UserController@Services` | Services page. |
| `/educationalservices` | GET | `UserController@educationServices` | Educational services page. |
| `/otherservices` | GET | `UserController@otherServices` | Other services page. |
| `/other-services/{id}` | GET | `UserController@showOtherServices` (L1549) | Other service detail (used for coupon popup). |
| `/other-services-mail` | POST | `UserController@saveOtherServicesMailList` (L1485) | See [Web Payment Endpoints](#web-payment-endpoints). |
| `/faq` | GET | `UserController@getFaq` | FAQ page. |
| `/blog`, `/blog/{slug}`, `/allblog/{slug}` | GET | `LandingPageController@freeBlog`/`readFreeBlog`/`allBlog` | Blog pages. |
| `/organisation` | GET | `LandingPageController@organization` | Organisation page. |
| `/aboutus`, `/ourteam`, `/happispaceform`, `/privacy`, `/terms` | GET | `LandingPageController` / `UserController` | Static content pages. |
| `/submit-contact` | POST | `ContactController@store` (web.php:185) | Contact form. **No active frontend caller** — the only caller in `layouts/app.blade.php:374` is inside a Blade comment. Mobile uses `/api/v1/submit-contact`. |
| `/subscribe` | GET/POST | `SubscriptionController@showForm`/`process` | Subscription form (email list). |

---

## Legacy API Routes Used by the Frontend

Three routes defined in `routes/api.php` (outside the `/v1` group) are used by **web frontend pages**:

| Endpoint | Method | Controller @method | Used by |
|---|---|---|---|
| `GET /api/get-psychologists/` | GET | `PsychologistController@getPsychologists` (L529) | Psychologist page infinite scroll (`psychologist.blade.php:694-708`). |
| `POST /api/username-exists/` | POST | `UserController@usernameExistOrNot` (L1115) | Forgot-password step 1 (`forgetpassword/usercheck.blade.php:15`). |
| `POST /api/forget-password-reset/` | POST | `UserController@forgetPasswordReset` (L1142) | Forgot-password step 3 (`newpassword.blade.php:13`). |

> These three have **duplicate route names** in `web.php` and `api.php`; api.php is registered after web.php so `route()` resolves to the `/api/...` URLs.

---

## Known Issues / Discrepancies

1. **`campaign/payment/orderBundle` broken** — web.php:154 points to `CampaignController@orderBundle`, which **does not exist**. The `campaign_payment_bundle.blade.php:14` form would error on submit.
2. **`/other-services-payment` broken** — web.php:192 points to `PaymentController@otherServicePayment`, which **does not exist**. Only reached via legacy `services.js:58-102` (`paymentOrder()`).
3. **`/verify-coupon` GET call mismatch** — `psychologist.blade.php:931-934` calls `verify-coupon` with method **GET** but the route is POST-only (web.php:148) → 405; the success handler also reads `discount_percent`/`coupon_id` which do not match the actual response keys.
4. **`payment/bundle.blade.php` uses POST** (L20) against a GET-only route (`payment.orderBundle`) → 405 on submit. (This is a legacy mobile-internal view.)
5. **`/booked-dates` key mismatch** — controller returns `disabled_dates` (L1392) but JS reads `data.message.disableDates` (`verify-popups.js:304`); JS degrades gracefully.
6. **Guardian OTP shadowing** — `Route::any('generate-guardian-otp-{type}')` (web.php:162) is registered before the POST variant (web.php:164); POSTs are handled by `generateGuardianOTP` (resend), so `generateSendGuardianOTP` may be unreachable.
7. **`/search` dead route** — web.php:184 `GET /search` → `PsychologistController@filterPsychologist`, but that method does not exist.
8. **`getPsychologists` returns a JSON string** — web page must call `JSON.parse(data)` (psychologist.blade.php:701).
9. **`/submit-contact` inactive** — no active frontend caller (only commented-out fetch in `layouts/app.blade.php`).
10. **Email OTP verify failure path** returns `success()` with an error message (`UserController.php:884`).
11. **`/generate-otp-email-one-page`** (`generateOTPEmail`, `UserController.php:1581`) is effectively dead — early-returns `$request->all()`.

---

## File Reference Index

### Frontend pages (Blade)
| File | APIs used |
|---|---|
| `resources/views/Frontend/payment/payment_bundle.blade.php` | `orderBundle`, `verify-coupon` |
| `resources/views/Frontend/payment/subscribed_services.blade.php` | `orderBundle`, `verify-coupon` |
| `resources/views/Frontend/payment/campaign_payment_bundle.blade.php` | `campaign.payment.orderBundle`, `/api/username-exists/` |
| `resources/views/Frontend/psychologist/psychologist.blade.php` | `/api/get-psychologists/`, `verify-coupon`, `book-psychologist`, `psychologist-available-dates`, `psychologist-save-appointment` |
| `resources/views/Frontend/assessment/assessment.blade.php` | `get-questions`, `save-option` (via main.js) |
| `resources/views/Frontend/dashboard/dashboard.blade.php` | links to screening/subscribed/download-report; drives verification popups |
| `resources/views/Frontend/report/*.blade.php` | `calculate-score`, `report-preview` (server-rendered) |
| `resources/views/Frontend/signup/signup1|signup2.blade.php` | `verifyToken`, `/signup`, guardian OTP endpoints |
| `resources/views/Frontend/login/login.blade.php` | `/login`, `/generate-otp-{type}`, `/verify-{type}-otp` |
| `resources/views/Frontend/profilesetting/*.blade.php` | `edit-profile`, `change-password`, `download-report`, `calculate-score` |
| `resources/views/Frontend/services/*.blade.php` | `other-services-mail`, `check-for-thrive-code`, `/other-services/{id}` |
| `resources/views/payment/paymentRequest*.blade.php` | Razorpay embedded checkout |
| `resources/views/payment/payment-success-page.blade.php` | mobile success page |

### Frontend JS
| File | APIs used |
|---|---|
| `public/assets/Frontend/js/main.js` | `formSubmitAjaxEvent` helper; `verifyToken`, signup/login, OTP, coupon info (`/other-services/{id}`), guardian OTP |
| `public/assets/Frontend/js/verify-popups.js` | `verificationData`, `booked-dates`, `get-available-dates`, `update-calltime`, `update-email`, `update-mobile`, `generate-otp-*`, `verify-*-otp`, guardian OTP |
| `public/assets/Frontend/js/services.js` | `other-services-mail`, `other-services-payment` (legacy) |
| `public/assets/Frontend/js/report.js` | no API calls (gauge/print helpers) |
| `public/assets/Frontend/js/blog.js` | no API calls (audio player) |

### Server side
| File | Key methods |
|---|---|
| `app/Http/Controllers/PaymentController.php` | `buyBundle`, `orderBundle`, `bookPsychologist`, `responseBundle`, `psychologistPaymentResponse`, `responseOtherServices`, `subscribedServices`, `otherServicePayment` |
| `app/Http/Controllers/api/v1/PaymentController.php` | `buyPlan`, `payment`, `paymentForHappitalk`, `paymentForHappiguide`, `applyCoupon`, `availFreeService`, `PaymentForIos`, `paymentLink*`, `successPaymentPage*`, `handleWebhook` |
| `app/Http/Controllers/AssessmentController.php` | `startAssessment`, `getQuestions`, `saveAssessmentOption`, `calculateAssessmentScore`, `reportPreview`, `updateCalltime` |
| `app/Http/Controllers/UserController.php` | `signup`, `signin`, `verifyToken`, `assessment`, `dashboard`, `updateEmail`, `updateMobile`, `generateOTP`, `verifyOtpByCode`, `getAvailableDates`, `getBookedDates`, `verificationData`, `usernameExistOrNot`, `forgetPasswordReset`, `saveOtherServicesMailList`, `showOtherServices`, `checkForThriveCode` |
| `app/Http/Controllers/PsychologistController.php` | `getPsychologistView`, `getPsychologists`, `getPsychologistAvailableDates`, `updatePsychologistAppointment` |
| `app/Http/Controllers/CouponController.php` | `verifyCoupon` |
| `app/Http/Controllers/CampaignController.php` | `getPlansPage`, `responseBundle` |
| `app/Services/PaymentService.php` | `paymentRequest`, `getPaymentResponse`, `getPsychologistPaymentResponse`, `getServicePaymentResponse`, `paymentServiceRequest` |
| `app/Services/CouponService.php` | `verifyCoupon` |
| `routes/web.php` | all web routes (lines 75–566 relevant) |
| `routes/api.php` | all API routes (lines 42–393) |
