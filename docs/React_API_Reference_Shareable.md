# Happimynd Website — API Reference (share with React interns)

Base URL: `https://happimynd.com/api` (dev: use the local `APP_URL`)

All requests/responses are **JSON** (`Content-Type: application/json`).

## Common conventions

- **Response envelope** (public content):
  ```json
  { "status": "success", "message": "…", "data": { … } }
  ```
- **Error responses** use HTTP status `400/404/422`:
  ```json
  { "status": "error", "message": "Human readable error", "data": null }
  ```
- **Auth header** (only where marked 🔒):
  ```
  Authorization: Bearer <JWT>
  ```
  JWT is obtained from the login/signup/OTP endpoints under `/api/v1/*`.
- **Image/media URLs** in responses are already absolute S3 URLs.

---

## PART A — The 4 items you asked for

### 1. Support floating form ("Raise a Query" popup)

`POST /api/v1/website/raise-query` 🔒 (auth required)

Request body:
```json
{
  "category": "payment",
  "query": "I was charged twice for my plan"
}
```
`category` options: `screening`, `payment`, `service`, `others`. Both fields required.

Response:
```json
{
  "status": "success",
  "message": "Query has been raised successfully."
}
```
This also emails the support mailbox (same as the current web form).

### 2. Support page / contact form

`POST /api/v1/submit-contact` (public — already existed)

Request body:
```json
{
  "first_name": "John",
  "last_name": "Doe",
  "phone_number": "9876543210",
  "reason": "Looking for corporate wellness",
  "message": "Optional message",
  "referral": "Optional referral source"
}
```
`first_name`, `last_name`, `phone_number`, `reason` required; `message`, `referral` optional.

Response:
```json
{ "message": "Contact saved successfully." }
```

### 3. Fetch the user's subscriptions

`GET /api/v1/website/subscribed-services` 🔒 (auth required)

No params. Response `data`:
```json
{
  "packages": [
    {
      "id": 4,
      "name": "HappiTALK",
      "description": "…",
      "bundle": 0,
      "is_subscribed": true,
      "plans": [
        {
          "id": 21,
          "package_id": 4,
          "price": 1000,
          "selling_price": 750,
          "per_session_selling_price": 375,
          "offer": { "price": 750, "discount": "25%" },
          "offer_max_discount": 25,
          "duration": { "id": 6, "name": "2 sessions", "type": 1, "value": 2, "frequency": 2 },
          "is_subscribed": true
        }
      ]
    }
  ],
  "assessment": {
    "id": 123, "user_id": 45, "started_at": "…", "ended_at": "…", "score": 68, …
  },
  "user_id": 45,
  "subscribed_plan_ids": [21],
  "organization_plan_ids": []
}
```
Notes:
- Mirror of the web `/subscribedservices` page — only the plans the user/org is subscribed to are kept for multi-plan packages.
- `assessment` is the latest completed assessment (use `ended_at` for the 1-year validity text). `null` if none.
- `organization_plan_ids` is non-empty only for corporate users (plans paid by the company).

### 4. Sponsor signup route

Three calls (all public, `GET` or `POST` both work):

**a) List organizations (for the "select your company" dropdown)**
`GET /api/v1/organizer-list`
```json
{ "message": "Organizer list get sucessfully.", "data": [ { "id": 1, "name": "…", … } ] }
```

**b) List user profiles (for the "who are you" dropdown)**
`GET /api/v1/user-profile`
```json
{ "message": "User profile get sucessfully.", "data": [ { "id": 1, "name": "…", "status": 1, … } ] }
```

**c) Create the account**
`POST /api/v1/signup`

Sponsor (corporate) payload — `signup_type` = `organization`:
```json
{
  "signup_type": "organization",
  "happimyndCode": "COMPANY_CODE",
  "nickname": "John",
  "user_profile_id": 1,
  "username": "john.doe",
  "password": "secret123",
  "confirm_password": "secret123",
  "language": 1
}
```
Individual payload — `signup_type` = `individual`:
```json
{
  "signup_type": "individual",
  "nickname": "John",
  "user_profile_id": 2,
  "username": "john.doe",
  "password": "secret123",
  "confirm_password": "secret123",
  "language": 1
}
```
Optional fields: `mobile`, `mobile_verified_token`, `gender`, `age`, `referral_code`, `device_token`.

Response includes the JWT to use on all 🔒 endpoints:
```json
{
  "status": "success",
  "message": "Signup successful.",
  "token": "<JWT>",
  "data": { "user": { "id": 45, "username": "john.doe", … } }
}
```

---

## PART B — Website content (all public GET)

### Landing page
`GET /api/v1/website/landing-page`
```json
{
  "status": "success",
  "data": {
    "intro_video_link": "https://…mp4",
    "intro_video_thumbnail": "https://…jpg",
    "quotes": { "id": 1, "title": "…", "description": "…", "image_url": "https://…" },
    "sections": { "section_key": { "title": "…", "description": "…", "image_url": "https://…" } },
    "carousel": [ { "dataContents": [ { "title": "…", "content": "…", "image_url": "https://…" } ] } ],
    "clients": [ { "name": "…", "image_url": "https://…" } ],
    "general_faqs": [ { "title": "…", "content": "…" } ],
    "landing_buttons": [ { "id": 1, "page_name": "landing", "button_content": "…" } ],
    "quotes_button": { … },
    "android_hyperlink": "https://…",
    "ios_hyperlink": "https://…"
  }
}
```

### Service buttons (homepage icons)
`GET /api/v1/website/service-buttons`
```json
{ "status": "success", "data": [ { "id": 1, "name": "…", "image_url": "https://…" } ] }
```

### Services page
`GET /api/v1/website/services`
```json
{
  "status": "success",
  "data": {
    "service_images": [ { "id": 1, "name": "…", "image_url": "https://…" } ],
    "explore_service_content": { "id": 1, "name": "explore-services", "content": [ … ] },
    "button_contents": [ { "id": 1, "page_name": "services", "button_content": "…" } ]
  }
}
```

### Explore services (HappiApp / HappiTALK / HappiSPACE / HappiCHAT)
`GET /api/v1/website/explore-services`
```json
{
  "status": "success",
  "data": {
    "happi_app": { "title": "…", "content": "…" },
    "happi_talk": { … },
    "happi_space": { … },
    "happi_chat": { … },
    "service_images": [ … ],
    "button_contents": [ … ]
  }
}
```

### Educational services
`GET /api/v1/website/educational-services`
```json
{
  "status": "success",
  "data": {
    "recommended": { "id": 1, "name": "Recommended", "service": [ … ] },
    "most_popular": { "id": 2, "name": "Most Popular", "service": [ … ] },
    "all_courses": [ … ]
  }
}
```

### Other services
`GET /api/v1/website/other-services`
```json
{
  "status": "success",
  "data": {
    "happimynd": { "name": "HappiMynd Services", "service": [ … ] },
    "other_services": { "name": "Other Services", "service": [ … ] }
  }
}
```

### Other service detail
`GET /api/v1/website/other-services/{id}`
```json
{ "status": "success", "data": { "id": 5, "name": "…", "type_id": 2, "price": 1000, … } }
```
`404` if the id doesn't exist.

### Signup for an other/educational service (creates Razorpay order)
`POST /api/v1/website/other-services-signup` (public)

Request body:
```json
{
  "other_service": 5,
  "name": "John Doe",
  "email": "john@example.com",
  "mobile": "9876543210"
}
```
Response:
```json
{
  "status": "success",
  "data": {
    "subscriber": { "id": 12, "other_service_id": 5, "name": "John Doe", "email": "…", "mobile": "…", "paid": 0 },
    "amount": 1000,
    "currency": "INR",
    "order_id": "order_XXXXXX",
    "razorpay_key": "rzp_XXXXXX",
    "callback_url": "https://happimynd.com/payment/response-other-services"
  }
}
```

### Blogs
`GET /api/v1/website/blog`
```json
{
  "status": "success",
  "data": {
    "blogs": [ { "id": 1, "title": "…", "slug": "…", "content": "…", "featured": 0, "post_category_id": 1, … } ],
    "videos": [ … ],
    "audios": [ … ],
    "featured": { … }
  }
}
```

### Single blog
`GET /api/v1/website/blog/{slug}`
```json
{
  "status": "success",
  "data": {
    "post": { "id": 1, "title": "…", "slug": "…", "content": "…", … },
    "related_articles": [ { … }, { … }, { … } ]
  }
}
```
`404` if slug not found.

### All blogs of a category
`GET /api/v1/website/all-blog/{slug}` (slug = category name)
```json
{ "status": "success", "data": { "id": 1, "name": "…", "post": [ … ] } }
```

### Our team
`GET /api/v1/website/our-team`
```json
{
  "status": "success",
  "data": {
    "founders": [ { "id": 1, "name": "…", "category": "founders", "image_url": "https://…" } ],
    "experts": [ … ],
    "psychologists": [ … ]
  }
}
```

### Organisation page
`GET /api/v1/website/organisation`
```json
{
  "status": "success",
  "data": {
    "organization_faqs": { "name": "faqs-organization", "content": [ … ] },
    "organizations": [ { "id": 1, "title": "…", "description": "…", "image_url": "https://…" } ],
    "logos": [ { "id": 1, "name": "…", "image_url": "https://…" } ],
    "organisation_buttons": [ { "id": 1, "page_name": "organisation", "button_content": "…" } ]
  }
}
```

### Happispace form link
`GET /api/v1/website/happispace-form`
```json
{ "status": "success", "data": { "happy_space_cdnlink": "https://…" } }
```

### FAQs
`GET /api/v1/website/faq`
```json
{
  "status": "success",
  "data": {
    "general_faqs": [ { "title": "…", "content": "…" } ],
    "organization_faqs": [ { "title": "…", "content": "…" } ]
  }
}
```

### Psychologist listing + filters
`GET /api/v1/website/psychologists?search=…&city=…&expert_category=…&specialization=…&language=…&limit=10`

All params optional. Response:
```json
{
  "status": "success",
  "data": [
    {
      "id": 7,
      "full_name": "Dr. Jane Smith",
      "city": { "id": 1, "name": "Mumbai" },
      "languages": [ { "id": 1, "name": "English" } ],
      "expert_level": { "id": 2, "name": "Psychologist" },
      "specialization": [ { "id": 3, "name": "Anxiety" } ],
      "plans": [ { "cost_price": 1000, "session_selling_price": 800, "print_duration": "…" } ],
      "profile_picture_url": "https://…",
      "minimum_session_price": 800,
      "slot1": 1,
      "slot2": 1
    }
  ],
  "filters": {
    "specializations": [ … ],
    "expert_levels": [ … ],
    "languages": [ … ],
    "cities": [ … ]
  }
}
```

---

## PART C — Buyable packages (pricing page)

`GET /api/v1/packages` (public; send Bearer token to get `is_subscribed` flags)

```json
{
  "status": "success",
  "data": [
    {
      "id": 3,
      "name": "HappiLIFE Screening",
      "description": "…",
      "bundle": 0,
      "is_subscribed": false,
      "plans": [
        {
          "id": 12,
          "package_id": 3,
          "price": 1499,
          "selling_price": 999,
          "per_session_selling_price": 999,
          "offer": { "price": 999, "discount": "…" },
          "offer_max_discount": 33,
          "duration": { "id": 1, "name": "Onetime pay", "type": 1, "value": null, "frequency": null },
          "expert_level": null,
          "is_subscribed": false
        }
      ]
    }
  ]
}
```

---

## PART D — Authenticated user

### Dashboard
`GET /api/v1/website/dashboard` 🔒 (auth required)

```json
{
  "status": "success",
  "data": {
    "dashboard_cover_pic": "https://…",
    "hyperlink": "…",
    "user": { "id": 45, "username": "john.doe", "email": "…", "mobile": "…", … },
    "assessment_id": 123,
    "assessment_complete_status": true,
    "appointment_status": true,
    "plan_id": 21,
    "slot_booked": [ "2026-08-20", … ],
    "booked_dates": [ … ],
    "disable_dates": [ … ],
    "show_blinking_text": true,
    "blinking_text": "screening"
  }
}
```
`blinking_text` is one of `screening` / `summary_reading` / `happiapp`.

---

## Quick reference table

| Purpose | Method & URL | Auth |
|---|---|---|
| Raise a query (floating support) | `POST /api/v1/website/raise-query` | 🔒 |
| Contact / support form | `POST /api/v1/submit-contact` | — |
| My subscribed services | `GET /api/v1/website/subscribed-services` | 🔒 |
| Sponsor signup — organizations | `GET /api/v1/organizer-list` | — |
| Sponsor/individual signup — profiles | `GET /api/v1/user-profile` | — |
| Create account (sponsor/individual) | `POST /api/v1/signup` | — |
| Login | `POST /api/v1/login` | — |
| Packages / pricing | `GET /api/v1/packages` | optional |
| Dashboard | `GET /api/v1/website/dashboard` | 🔒 |
| Landing page | `GET /api/v1/website/landing-page` | — |
| All other content pages | `GET /api/v1/website/*` | — |
