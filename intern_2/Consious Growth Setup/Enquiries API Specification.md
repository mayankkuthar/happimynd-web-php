# Enquiries API Specification

Nov 13, 2026

The website needs 7 REST endpoints on top of the enquiries DB: 3 public endpoints for the form submissions and 4 admin endpoints for managing enquiries. Each endpoint calls exactly one stored procedure from enquiry_db_procedures.sql.

## Overview

All endpoints live under `/api/v1`, accept and return JSON (`Content-Type: application/json`), and use camelCase field names. The backend maps camelCase to the procedure parameters and the snake_case DB columns.

- **Public endpoints** (`/api/v1/enquiries/*`): no login. Need rate limiting and a captcha or honeypot field to block spam.
- **Admin endpoints** (`/api/v1/admin/*`): require `Authorization: Bearer <token>`. The auth method is still to be decided (see Open questions).
- **Dates**: `YYYY-MM-DD`. Timestamps (`createdAt`, `updatedAt`) are ISO 8601, e.g. `2026-11-13T10:30:00Z`.
- **DB connection**: the API connects to AWS RDS MySQL 8 with a DB user that has only `EXECUTE` on the procedures, never direct table access.

| # | Method | Path | Stored procedure | Auth |
| --- | --- | --- | --- | --- |
| 1 | POST | /api/v1/enquiries/general | sp_create_general_enquiry | Public |
| 2 | POST | /api/v1/enquiries/pathway | sp_create_pathway_enquiry | Public |
| 3 | POST | /api/v1/enquiries/speaking | sp_create_speaking_enquiry | Public |
| 4 | GET | /api/v1/admin/enquiries | sp_get_enquiries | Admin |
| 5 | GET | /api/v1/admin/enquiries/{id} | sp_get_enquiry_by_id | Admin |
| 6 | PATCH | /api/v1/admin/enquiries/{id}/status | sp_update_enquiry_status | Admin |
| 7 | GET | /api/v1/admin/enquiries/summary | sp_enquiry_summary | Admin |

`sp_validate_contact` is internal: the three create procedures call it, so it needs no endpoint of its own.

## Public submit APIs

Each form posts to its own endpoint and gets back the new `enquiryId` (the procedure's OUT parameter). The DB trims every text field, lower-cases the email and stores empty optional fields as NULL, so the API can pass values through as typed.

All three share the same success response, **201 Created**:

```json
{ "success": true, "enquiryId": 1042, "message": "Enquiry received" }
```

### 1. POST /api/v1/enquiries/general

Calls `sp_create_general_enquiry`. Used by the General Enquiry modal.

| Field | Type | Required | Max length | Notes |
| --- | --- | --- | --- | --- |
| interest | string | No | 150 | One of: Individual Coaching & Growth, Leadership Development & LEAP, Organisational Programs, Speaking & Event Invitations, Other Enquiries |
| fullName | string | Yes | 150 | |
| email | string | Yes | 255 | Must be a valid email |
| phone | string | No | 30 | |
| organization | string | No | 200 | |
| message | string | Yes | TEXT | |

```json
{
  "interest": "Individual Coaching & Growth",
  "fullName": "Riya Shah",
  "email": "riya@example.com",
  "phone": "+91 98765 43210",
  "organization": "Acme Ltd",
  "message": "Looking for leadership coaching"
}
```

### 2. POST /api/v1/enquiries/pathway

Calls `sp_create_pathway_enquiry`. Used by the Pathway (coaching) modal.

| Field | Type | Required | Max length | Notes |
| --- | --- | --- | --- | --- |
| pathwayId | string | Yes | 100 | One of: students-career-aspirants, working-professionals, managers-leaders, senior-leaders-cxos, organisations-teams |
| fullName | string | Yes | 150 | |
| email | string | Yes | 255 | Must be a valid email |
| phone | string | No (form: Yes) | 30 | The form requires it; the DB does not |
| currentRole | string | No | 200 | Stored in `current_role_title` |
| organization | string | No | 200 | The form does not collect this yet |
| primaryGoal | string | No | TEXT | |
| preferredTimeline | string | No | 50 | One of: Immediate (This month), Within 1-2 months, Currently exploring |

```json
{
  "pathwayId": "working-professionals",
  "fullName": "Riya Shah",
  "email": "riya@example.com",
  "phone": "+91 98765 43210",
  "currentRole": "Senior Product Lead",
  "organization": "Acme Ltd",
  "primaryGoal": "Move into a director role",
  "preferredTimeline": "Within 1-2 months"
}
```

### 3. POST /api/v1/enquiries/speaking

Calls `sp_create_speaking_enquiry`. Used by the Speaking Enquiry modal.

| Field | Type | Required | Max length | Notes |
| --- | --- | --- | --- | --- |
| engagementType | string | No | 100 | One of: Leadership talk, Corporate workshop, Webinar, Panel discussion, Guest session |
| audience | string | No | 150 | One of: Corporate leaders and teams, HR and L&D professionals, Students and emerging professionals, Industry or public audience |
| proposedDate | date | No | | `YYYY-MM-DD`, cannot be in the past |
| eventFormat | string | No | 50 | One of: In person, Virtual, Hybrid |
| notes | string | No | TEXT | |
| fullName | string | Yes | 150 | |
| email | string | Yes | 255 | Must be a valid email |
| phone | string | No | 30 | |
| organization | string | No (form: Yes) | 200 | The form requires it; the DB does not |

```json
{
  "engagementType": "Leadership talk",
  "audience": "Corporate leaders and teams",
  "proposedDate": "2026-12-15",
  "eventFormat": "In person",
  "notes": "Annual offsite, ~200 attendees",
  "fullName": "Riya Shah",
  "email": "riya@example.com",
  "phone": "+91 98765 43210",
  "organization": "Acme Ltd"
}
```

## Admin APIs

These four endpoints back an admin screen where the team reviews enquiries and tracks follow-up. All need a valid admin token; without one they return **401**.

### 4. GET /api/v1/admin/enquiries

Calls `sp_get_enquiries`. Lists enquiries, newest first. Every filter is optional; leave it out to ignore it.

| Query param | Type | Default | Notes |
| --- | --- | --- | --- |
| type | string | all | GENERAL, PATHWAY or SPEAKING |
| status | string | all | NEW, CONTACTED, IN_PROGRESS, CLOSED or SPAM |
| fromDate | date | none | Created on or after this date |
| toDate | date | none | Created on or before this date (whole day included) |
| page | integer | 1 | Minimum 1 |
| pageSize | integer | 20 | 1 to 200; the DB caps larger values at 200 |

Example: `GET /api/v1/admin/enquiries?type=SPEAKING&status=NEW&fromDate=2026-11-01&toDate=2026-11-30&page=1&pageSize=20`

The procedure returns two result sets: the rows for the page, then `total_records`. The API combines them into one response, **200 OK**:

```json
{
  "data": [
    {
      "enquiryId": 1042,
      "enquiryType": "SPEAKING",
      "fullName": "Riya Shah",
      "email": "riya@example.com",
      "phone": "+91 98765 43210",
      "organization": "Acme Ltd",
      "engagementType": "Leadership talk",
      "audience": "Corporate leaders and teams",
      "proposedDate": "2026-12-15",
      "eventFormat": "In person",
      "notes": "Annual offsite, ~200 attendees",
      "status": "NEW",
      "adminRemarks": null,
      "createdAt": "2026-11-13T10:30:00Z",
      "updatedAt": "2026-11-13T10:30:00Z"
    }
  ],
  "pagination": { "page": 1, "pageSize": 20, "totalRecords": 57, "totalPages": 3 }
}
```

Each row carries every column of the `enquiries` table; fields that do not apply to that enquiry type come back as `null`.

### 5. GET /api/v1/admin/enquiries/{id}

Calls `sp_get_enquiry_by_id`. Returns one enquiry in the same shape as a row above, **200 OK**: `{ "data": { ... } }`. The procedure returns no row for an unknown ID, so the API must return **404** itself.

### 6. PATCH /api/v1/admin/enquiries/{id}/status

Calls `sp_update_enquiry_status`. Changes the workflow status and optionally the admin remarks.

| Field | Type | Required | Notes |
| --- | --- | --- | --- |
| status | string | Yes | NEW, CONTACTED, IN_PROGRESS, CLOSED or SPAM |
| adminRemarks | string | No | Leave it out to keep the existing remarks; the DB does not clear them |

```json
{ "status": "CONTACTED", "adminRemarks": "Called on 13 Nov" }
```

Response **200 OK**: `{ "success": true, "enquiryId": 1042, "status": "CONTACTED" }`. An unknown ID returns **404**.

### 7. GET /api/v1/admin/enquiries/summary

Calls `sp_enquiry_summary`. Counts enquiries by type and status for a dashboard. Optional query params: `fromDate`, `toDate` (same rules as the list endpoint).

Response **200 OK**, one row per enquiry type that has data:

```json
{
  "data": [
    { "enquiryType": "GENERAL", "total": 30, "new": 12, "contacted": 8, "inProgress": 5, "closed": 4, "spam": 1 },
    { "enquiryType": "PATHWAY", "total": 18, "new": 6, "contacted": 5, "inProgress": 4, "closed": 3, "spam": 0 },
    { "enquiryType": "SPEAKING", "total": 9, "new": 4, "contacted": 2, "inProgress": 2, "closed": 1, "spam": 0 }
  ]
}
```

A type with no enquiries in the range is missing from the DB result; the API should add it with zeros so the dashboard always shows three rows.

## Errors

The procedures raise validation errors as `SQLSTATE 45000` with a readable message. The API catches these and returns them as **400**, except "Enquiry not found", which becomes **404**. Every error uses one shape:

```json
{ "success": false, "error": { "code": "VALIDATION_ERROR", "message": "A valid email address is required" } }
```

| DB message | Raised by | HTTP | Error code |
| --- | --- | --- | --- |
| Full name is required | All 3 create procedures | 400 | VALIDATION_ERROR |
| A valid email address is required | All 3 create procedures | 400 | VALIDATION_ERROR |
| Message is required | sp_create_general_enquiry | 400 | VALIDATION_ERROR |
| Pathway is required | sp_create_pathway_enquiry | 400 | VALIDATION_ERROR |
| Proposed date cannot be in the past | sp_create_speaking_enquiry | 400 | VALIDATION_ERROR |
| Invalid status value | sp_update_enquiry_status | 400 | VALIDATION_ERROR |
| Enquiry not found | sp_update_enquiry_status | 404 | NOT_FOUND |
| (no row returned) | sp_get_enquiry_by_id | 404 | NOT_FOUND |
| (missing or bad token) | Admin endpoints | 401 | UNAUTHORIZED |
| (too many requests) | Public endpoints | 429 | RATE_LIMITED |
| (any other DB or server error) | Any | 500 | INTERNAL_ERROR |

The API should also check field lengths and the allowed values before calling the DB. MySQL would otherwise reject or cut off over-length text and invalid `type`/`status` filters with a generic error. A 500 response must never include the raw SQL error.

Implementation note: register `/admin/enquiries/summary` before `/admin/enquiries/{id}`, or most routers will treat "summary" as an ID.

## Open questions

The website forms and the DB procedures disagree in a few places. Each needs a decision before the API is built.

- [ ] **Pathway phone:** the form requires phone/WhatsApp, the DB allows NULL. Should the API enforce it?
- [ ] **Speaking organization:** the form requires organization, the DB allows NULL. Should the API enforce it?
- [ ] **Pathway organization:** the DB and API accept it, but the Pathway form has no field for it. Add the field, or drop it?
- [ ] **Pathway timeline default:** the form starts with "Within 2 weeks", which is not one of its 3 options. Which value should be the default?
- [ ] **Admin auth:** how do admins log in (e.g. JWT login, AWS Cognito, or a static API key to start)?
- [ ] **Hosting:** where does the API run (Vercel serverless functions next to the site, or AWS Lambda / EC2 near RDS)? Serverless needs connection pooling such as RDS Proxy.
- [ ] **Notifications:** should a new enquiry send an email to the team or a confirmation to the person?
- [ ] **Spam protection:** captcha (e.g. reCAPTCHA / Turnstile) or a hidden honeypot field on the public forms?
- [ ] **CORS:** which website domain(s) may call the public endpoints?
