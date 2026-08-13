# React Website — API Endpoint Documentation

This document describes the JSON API endpoints provided for the **React frontend** rebuild of the Happimynd website. These endpoints mirror the data previously served by the Laravel Blade web pages (`routes/web.php`) and return it as JSON.

**Base URL:** `APP_URL` from `.env` (production `https://happimynd.com`). All routes are prefixed with `/api`.

## Conventions

- **Response envelope (public content):**
  ```json
  { "status": "success", "message": "...", "data": { ... } }
  ```
  Errors: `{ "status": "error", "message": "...", "data": null }` with an appropriate HTTP status (400/404/422).
- **Auth:** `GET /api/v1/website/dashboard`, `GET /api/v1/website/subscribed-services` and `POST /api/v1/website/raise-query` require a logged-in user (`Authorization: Bearer <JWT>`). All other `/website/*` endpoints are public. `GET /api/v1/packages` works anonymously and additionally returns `is_subscribed` flags when a Bearer token is supplied.
- **Images/media:** Model-level accessors such as `getImageWithS3Url(...)` / `getContentWithS3Url(...)` are applied where the web pages use them, so URLs are already absolute S3 URLs.
- **CORS:** The API is open (`allowed_origins: *`) in `config/cors.php`. Lock `allowed_origins` to the real React domain before production.

---

## Packages / Bundles

### `GET /api/v1/packages` — List buyable packages & plans (public)

Replaces the web page `GET /buy-bundles` (`PaymentController@buyBundle`).

- **Controller:** `api\v1\PaymentController@packages`
- **Auth:** Optional. When a user JWT is sent, each package/plan gains an `is_subscribed` flag.
- **Behavior:** identical to the web page — org users (`bundle=0`) vs. full list, fixed sort order, and the HappiTALK package returns only its minimum-price expert plan.
- **Response `data`:** array of packages:
  ```json
  [
    {
      "id": 3, "name": "HappiLIFE Screening", "description": "...", "bundle": 0,
      "is_subscribed": false,
      "plans": [
        {
          "id": 12, "package_id": 3,
          "price": 1499, "selling_price": 999,
          "per_session_selling_price": 999,
          "offer": { "price": 999, "discount": "..." },
          "offer_max_discount": 33,
          "duration": { "id": 1, "name": "Onetime pay", "type": 1, "value": null, "frequency": null },
          "expert_level": null,
          "is_subscribed": false
        }
      ]
    }
  ]
  ```

---

## Website Content

All endpoints below are public (`GET` unless stated) and grouped under `/api/v1/website`.

### `GET /api/v1/website/landing-page` — Landing page

Replaces `GET /` (`UserController@landingPageView`).

- **Response `data`:**
  - `intro_video_link`, `intro_video_thumbnail` (S3 URLs)
  - `quotes` — first `Quotes` record (`image_url` resolved)
  - `sections` — static sections keyed by section name (each an object or array of `DataContent`; each has `image_url` resolved)
  - `carousel` — `carouselSection` (each with ordered `dataContents`; each has `image_url` resolved)
  - `clients` — `OurClient` list ordered by `preference` (`image_url` resolved)
  - `general_faqs` — `DataContent[]` from the `faqs-general` group
  - `landing_buttons` — `EditButton` where `page_name = 'landing'`
  - `quotes_button` — `EditButton` where `page_name = 'quotes'`
  - `android_hyperlink`, `ios_hyperlink` — `DataContent` rows (play store / app store links)

### `GET /api/v1/website/service-buttons` — Landing/footer service buttons

Replaces `GET /get-service-button-data` (`UserController@getServiceButtonData`). Returns all `ServiceImage` records. The old footer JS mapped `data[0]→#app1`, `data[1]→#talk1`, `data[2]→#space1`, `data[3]→#chat1`, `data[4]→#life1`.

### `GET /api/v1/website/services` — Services page

Replaces `GET /services` (`UserController@Services`).

- **Response `data`:** `service_images` (`ServiceImage[]`, each with `image_url` resolved), `explore_service_content` (DataGroup `explore-services`), `button_contents` (EditButton `services`).

### `GET /api/v1/website/explore-services` — Explore services page

Replaces `GET /explore-services` (`UserController@exploreServices`).

- **Response `data`:** `happi_app`, `happi_talk`, `happi_space`, `happi_chat` (DataContent records), `service_images`, `button_contents`.

### `GET /api/v1/website/educational-services` — Educational services page

Replaces `GET /educationalservices` (`UserController@educationServices`).

- **Response `data`:** `recommended` and `most_popular` (`ServiceTypeGroup` with `service` = `OtherService[]`), `all_courses` (merged, deduped by merge of both groups).

### `GET /api/v1/website/other-services` — Other services page

Replaces `GET /otherservices` (`UserController@otherServices`).

- **Response `data`:** `happimynd` (`ServiceTypeGroup` "HappiMynd Services" with `service`), `other_services` (`ServiceTypeGroup` "Other Services" with `service`).

### `GET /api/v1/website/other-services/{id}` — Other/education service detail

Replaces `GET /other-services/{id}` (`UserController@showOtherServices`). Returns a single `OtherService` (404 JSON if not found).

### `POST /api/v1/website/other-services-signup` — Buy an other/education service

Replaces `POST /other-services-mail` (`UserController@saveOtherServicesMailList`). Creates the subscriber, computes the discounted amount, creates a `ServicesReceipt` + Razorpay order, and returns the order payload so the React app can run **Razorpay Checkout**.

- **Request body (JSON):** `other_service` (OtherService id), `name`, `email`, `mobile`.
- **Response `data`:** `subscriber`, `amount`, `currency`, `order_id`, `razorpay_key`, `callback_url`.
- **Callback:** after payment the React app should verify via `callback_url` (`/payment/response-other-services`) or the Razorpay webhook; the callback marks `OtherServiceSubscriber.paid = true`.

### `GET /api/v1/website/blog` — Blog listing

Replaces `GET /blog` (`LandingPageController@freeBlog`).

- **Response `data`:** `blogs`, `videos`, `audios` (posts grouped by category, only `restricted_content = 0` and `publish_status = 1`), `featured` (first `featured = 1` post).

### `GET /api/v1/website/blog/{slug}` — Blog post detail

Replaces `GET /blog/{slug}` (`LandingPageController@readFreeBlog`).

- **Response `data`:** `post` (404 JSON if not found), `related_articles` (up to 3 posts in the same category).

### `GET /api/v1/website/all-blog/{slug}` — Posts by category

Replaces `GET /allblog/{slug}` (`LandingPageController@allBlog`). Returns the `PostCategory` (matched by `name`) with its `post` list (404 JSON if the category does not exist).

### `GET /api/v1/website/our-team` — Our team

Replaces `GET /ourteam` (`LandingPageController@ourTeam`).

- **Response `data`:** `founders`, `experts`, `psychologists` (OurTeam lists ordered by `preference`, each with `image_url` resolved).

### `GET /api/v1/website/organisation` — Organisation page

Replaces `GET /organisation` (`LandingPageController@organization`).

- **Response `data`:** `organization_faqs` (DataGroup `faqs-organization`), `organizations` (`OrganizationPageData[]`; `description` is stripped of HTML and, for non-id-1 rows, split on `*` into `lines`; `image_url` resolved), `logos` (`OrganizationLogo[]`, each with `image_url` resolved), `organisation_buttons` (EditButton `organisation`).

### `GET /api/v1/website/happispace-form` — Happispace form link

Replaces `GET /happispaceform` (`LandingPageController@happispaceform`).

- **Response `data`:** `happy_space_cdnlink`.

### `GET /api/v1/website/faq` — FAQ page

Replaces `GET /faq` (`UserController@getFaq`).

- **Response `data`:** `general_faqs`, `organization_faqs` (content lists from DataGroups `faqs-general` / `faqs-organization`).

### `GET /api/v1/website/psychologists` — Psychologist listing & filters

Replaces the web psychologist page (`PsychologistController@getPsychologistView`).

- **Query params (all optional):** `search`, `city`, `expert_category`, `specialization`, `language`, `limit` (default 10).
- **Response `data`:** `PsychoLogistResource[]` — each psychologist includes `id`, `full_name`, `city`, `languages`, `expert_level`, `specialization`, `plans` (with `cost_price`, `session_selling_price`, `print_duration`), `profile_picture_url`, `minimum_session_price`, `slot1`, `slot2`.
- **Response `filters`:** `specializations`, `expert_levels`, `languages`, `cities` for the filter dropdowns.

---

## User Dashboard

### `GET /api/v1/website/dashboard` — Logged-in user dashboard

Replaces `GET /dashboard` (`UserController@dashboard`). **Requires `Authorization: Bearer <JWT>`.**

- **Response `data`:**
  - `dashboard_cover_pic`, `hyperlink` (from DataGroup `dashboard`)
  - `user` — the authenticated user model
  - `assessment_id`, `assessment_complete_status`, `appointment_status`
  - `plan_id` — latest valid bundle plan id
  - `slot_booked` — booked/unavailable slots from `AppointmentService::getBookedAppointmentDates()`
  - `booked_dates`, `disable_dates` — assessment booking / availability dates
  - `show_blinking_text`, `blinking_text` — one of `screening`, `summary_reading`, `happiapp`

### `GET /api/v1/website/subscribed-services` — My subscribed services

Replaces `GET /subscribedservices` (`PaymentController@subscribedServices`). **Requires `Authorization: Bearer <JWT>`.**
The existing mobile `GET /api/v1/my-subscribed-services` only returns bare Package rows; this endpoint mirrors the full web page instead.

- **Behavior:** same filtering as the web page — packages sorted by `$sortOrder`, HappiTALK shows only its minimum-price plan, bundle deals hidden for org users, and multi-plan packages keep only the plans the user/org is subscribed to.
- **Response `data`:**
  - `packages` — `{ id, name, description, bundle, is_subscribed, plans[] }`; each plan is `{ id, package_id, price, selling_price, per_session_selling_price, offer, offer_max_discount, duration, is_subscribed }`
  - `assessment` — the user's latest completed assessment (or `null`); web page shows the 1-year validity based on `ended_at`
  - `user_id`
  - `subscribed_plan_ids` — plan ids from the user's `BundleStatus`
  - `organization_plan_ids` — plan ids subscribed by the user's organization (empty for individual users)

### `POST /api/v1/website/raise-query` — Raise a query (floating support form)

Replaces the "Raise a Query" popup (`POST /raise-query`, `UserController@postRaiseQuery`). **Requires `Authorization: Bearer <JWT>.`**
The existing mobile `POST /api/v1/raise-query-app` does **not** notify the support mailbox; this endpoint does.

- **Body (JSON):** `category` (required — e.g. `screening`, `payment`, `service`, `others`), `query` (required).
- **Behavior:** creates a `RaiseQuery` row (`platform = "website"`) and emails `SUPPORT_MAIL` via the `QueryRaisedToAdmin` mailable, matching the web form.
- **Response:** `{ "status": "success", "message": "Query has been raised successfully." }`

---

## Already covered (no new endpoint needed)

| Request | Web route | Existing API |
|---|---|---|
| Support page / contact form | `POST /submit-contact` (`ContactController@store`) | `POST /api/v1/submit-contact` |
| Sponsor signup page data (`/sponsersignup`) | `UserController@sponserSignupView` | `GET/POST /api/v1/organizer-list` + `/api/v1/user-profile` |
| Individual signup page data (`/individualsignup`) | `UserController@individualSignupView` | `GET/POST /api/v1/user-profile` |
| Sponsor/individual account creation | `POST /signup` | `POST /api/v1/signup` (use `signup_type` = `organization` / `individual`; org requires `happimyndCode`) |

---

## Mapping to Existing Web Pages (quick reference)

| Web page | New API endpoint |
|---|---|
| `/buy-bundles` | `GET /api/v1/packages` |
| `/` (landing) | `GET /api/v1/website/landing-page` |
| `/get-service-button-data` | `GET /api/v1/website/service-buttons` |
| `/services` | `GET /api/v1/website/services` |
| `/explore-services` | `GET /api/v1/website/explore-services` |
| `/educationalservices` | `GET /api/v1/website/educational-services` |
| `/otherservices` | `GET /api/v1/website/other-services` |
| `/other-services/{id}` | `GET /api/v1/website/other-services/{id}` |
| `/other-services-mail` | `POST /api/v1/website/other-services-signup` |
| `/blog`, `/blog/{slug}`, `/allblog/{slug}` | `GET /api/v1/website/blog`, `/blog/{slug}`, `/all-blog/{slug}` |
| `/ourteam` | `GET /api/v1/website/our-team` |
| `/organisation` | `GET /api/v1/website/organisation` |
| `/happispaceform` | `GET /api/v1/website/happispace-form` |
| `/faq` | `GET /api/v1/website/faq` |
| `/psychologist` + `/api/get-psychologists/` | `GET /api/v1/website/psychologists` |
| `/dashboard` | `GET /api/v1/website/dashboard` (auth) |
| `/subscribedservices` | `GET /api/v1/website/subscribed-services` (auth) |
| `POST /raise-query` (floating "Raise a Query") | `POST /api/v1/website/raise-query` (auth) |
| `POST /submit-contact` (support/contact form) | `POST /api/v1/submit-contact` (already existed) |
| `/sponsersignup`, `/individualsignup` | `GET/POST /api/v1/organizer-list`, `/api/v1/user-profile`, `/api/v1/signup` (already existed) |

---

## Files Changed

- `routes/api.php` — new routes (packages + `/v1/website` group + `website/dashboard`).
- `app/Http/Controllers/api/v1/PaymentController.php` — added `packages()`.
- `app/Http/Controllers/api/v1/WebsiteController.php` — **new** controller with all website content endpoints.

> Note: Login/signup, OTP, profile, assessment, notification and the main payment flows already exist under `/api/v1/*` (see `Psychologist_Mobile_App_API_Documentation.md`) and are shared by the React site — auth via `Authorization: Bearer <JWT>`.
