# Invoice Project — API Specification

Prepared by Ananya Pandey · 12 Oct 2026

## Overview

The invoice platform needs the REST APIs below on top of the MySQL tables already created from the DB procedures. Every endpoint is listed with method, path, query params, request body, response payload and purpose. They match what the web app calls today.

- **Base URL:** `{API_HOST}/api` (health check at `GET /health`, outside `/api`)
- **Format:** JSON in and out, `Content-Type: application/json`, request body limit 2 MB
- **Auth:** `POST /api/auth/login` returns a JWT. Every other endpoint needs the header `Authorization: Bearer <token>`.
- **Roles:** `VIEWER` < `OPERATOR` < `ADMIN`. Endpoints marked OPERATOR or ADMIN reject lower roles with 403.
- **Paging (list endpoints):** query `page` (default 1) and `pageSize` (default 25, max 100). Response: `{ "data": [...], "page", "pageSize", "total", "totalPages" }`
- **Errors:** `{ "error": "<message>", "details"?: {...} }` with the HTTP status codes listed at the end
- **Dates:** ISO 8601 strings (`2026-10-12T10:30:00.000Z`); money as numbers in INR
- **Rate limits:** a general limit on all `/api` routes and a stricter one on `/api/auth`
- **Realtime:** a Socket.IO server on the same host pushes live updates (sync progress, status changes); it authenticates with the same JWT

## API summary

The project needs 28 endpoints across 16 modules. The last column names the tables each one reads or writes, so each API can be mapped to a procedure.

| # | Module | Method | Endpoint | Description | Min role | Tables used |
| --- | --- | --- | --- | --- | --- | --- |
| 1 | Health | GET | `/health` | Liveness check | None | None |
| 2 | Auth | POST | `/api/auth/login` | Log in with email and password, get a JWT | None | User |
| 3 | Auth | GET | `/api/auth/me` | Current logged-in user | VIEWER | User |
| 4 | Dashboard | GET | `/api/dashboard/stats` | Headline counts, amounts and system health | VIEWER | Client, Session, VoiceProcessing, DataProcessing, SyncRun, SyncRecord, InvoiceTransaction |
| 5 | Clients | GET | `/api/clients` | Paged, filterable client list with latest session, payment and status | VIEWER | Client, Session, Payment, InvoiceTransaction, VoiceProcessing, DataProcessing |
| 6 | Clients | GET | `/api/clients/{id}` | One client with all sessions, payments, invoices, processing and audit history | VIEWER | Client, Session, Payment, InvoiceTransaction, VoiceProcessing, DataProcessing, Template, AuditLog |
| 7 | Sessions | GET | `/api/sessions` | Paged session list | VIEWER | Session, Client, Payment, VoiceProcessing, DataProcessing |
| 8 | Sessions | GET | `/api/sessions/{id}` | One session with client, payments and processing | VIEWER | Session, Client, Payment, VoiceProcessing, DataProcessing |
| 9 | Sessions | GET | `/api/sessions/{id}/source-record` | Raw spreadsheet row the session came from | VIEWER | SyncRecord |
| 10 | Payments | GET | `/api/payments` | Paged payment list with filters | VIEWER | Payment, Client, Session |
| 11 | Invoices | GET | `/api/invoices/stats` | Invoice ledger counts and amounts | VIEWER | InvoiceTransaction, SyncRecord |
| 12 | Invoices | GET | `/api/invoices` | Paged invoice list with filters | VIEWER | InvoiceTransaction, Client |
| 13 | Processing | GET | `/api/processing/stats` | Status counts for voice or data processing | VIEWER | VoiceProcessing / DataProcessing, Session |
| 14 | Processing | GET | `/api/processing` | Paged voice and data processing records | VIEWER | VoiceProcessing, DataProcessing, Client, Session, InvoiceTransaction |
| 15 | Voice | POST | `/api/voice/{id}/retry` | Re-queue a failed voice job | OPERATOR | VoiceProcessing, AuditLog |
| 16 | Data | POST | `/api/data/{id}/retry` | Re-queue a failed invoice (data) job | OPERATOR | DataProcessing, InvoiceTransaction, AuditLog |
| 17 | Data | GET | `/api/data/{id}/pdf` | View or download the invoice PDF | VIEWER | DataProcessing |
| 18 | Data | POST | `/api/data/{id}/send-email` | Email the invoice PDF to a client | OPERATOR | DataProcessing, InvoiceTransaction, Payment, Client, AuditLog |
| 19 | Sync | GET | `/api/sync/status` | Latest and last 20 sync runs | VIEWER | SyncRun |
| 20 | Sync | GET | `/api/sync/source-rows` | Synced spreadsheet rows as raw data | VIEWER | SyncRecord |
| 21 | Sync | POST | `/api/sync/run` | Trigger a spreadsheet sync now | ADMIN | AuditLog (+ job queue) |
| 22 | Revenue | GET | `/api/revenue` | Revenue totals and breakdowns by month, product, state, mode, source | VIEWER | Payment, Client, Session |
| 23 | Verification | POST | `/api/verification/upload` | Upload a Razorpay export and match it to payments | OPERATOR | RazorpayImportBatch, RazorpayPaymentRecord, Payment, AuditLog |
| 24 | Verification | GET | `/api/verification/summary` | Latest import batch and match counts | VIEWER | RazorpayImportBatch, RazorpayPaymentRecord |
| 25 | Verification | GET | `/api/verification/issues` | Paid payments with no or a failed Razorpay match | VIEWER | Payment, Client, RazorpayPaymentRecord |
| 26 | Audit logs | GET | `/api/audit-logs` | Paged audit trail | VIEWER | AuditLog, User |
| 27 | Templates | GET | `/api/templates` | All processing templates | VIEWER | Template |
| 28 | Meta | GET | `/api/meta/filters` | Dropdown values for state, product, payment mode | VIEWER | Client, Session, Payment |

The tables `InvoiceNumberCounter` and `ProcessingJob` are used only by the background worker (invoice numbering and job tracking), not by any API.

## Endpoint details

GET endpoints take query parameters only and have no request body. All except login need `Authorization: Bearer <token>`.

### 1. Auth

**POST `/api/auth/login`** — Validates email and password against `User` (active users only) and returns a JWT.

Request body:

```json
{
  "email": "admin@example.com",
  "password": "secret"
}
```

Response 200:

```json
{
  "token": "eyJhbGciOi...",
  "user": { "id": "usr_1", "email": "admin@example.com", "name": "Admin", "role": "ADMIN" }
}
```

Errors: 400 invalid body, 401 `Invalid credentials`.

**GET `/api/auth/me`** — Returns the logged-in user.

Response 200: `{ "id", "email", "name", "role" }`. 404 if the user no longer exists.

### 2. Dashboard

**GET `/api/dashboard/stats`** — All numbers for the main dashboard cards. Counts only active (current spreadsheet) records.

```json
{
  "totalClients": 120,
  "totalSessions": 480,
  "totalRevenue": 350000,
  "pendingVoice": 4,
  "pendingData": 6,
  "processing": 2,
  "failed": 1,
  "spreadsheetConnected": true,
  "databaseConnected": true,
  "workerRunning": true,
  "lastSyncedAt": "2026-10-12T09:00:00.000Z",
  "totalRecordsSynced": 500,
  "paidTransactions": 300,
  "pendingPayments": 40,
  "invoicesGenerated": 280,
  "invoicesPendingGeneration": 20,
  "duplicateTransactionsDetected": 3,
  "failedInvoiceGenerations": 1,
  "needsReview": 5,
  "totalAmount": 350000,
  "verifiedAmount": 320000,
  "pendingAmount": 30000,
  "requiresVerificationAmount": 8000,
  "confirmedSessionsCount": 300,
  "duplicateOrInvalidCount": 6
}
```

`totalAmount` = verified + pending only; it never includes amounts that need review.

### 3. Clients

**GET `/api/clients`** — Paged client list. One row per client with the latest session, latest payment and processing status.

| Query param | Type | Default | Notes |
| --- | --- | --- | --- |
| page | int | 1 | |
| pageSize | int | 25 | max 100 |
| search | string | | matches client name or phone |
| state | string | | exact match |
| product | string | | client has a session with this product |
| paymentMode | string | | client has a payment with this mode |
| sortBy | enum | updatedAt | clientName, createdAt, updatedAt |
| sortDir | enum | desc | asc, desc |
| includeInactive | boolean | false | include clients removed from the sheet |

Response item in `data[]`:

```json
{
  "id": "cl_1", "clientName": "Ravi Kumar", "phone": "9876543210", "state": "Karnataka", "profile": "...",
  "product": "Counselling", "sessionCount": 3, "totalSessions": 10, "completedSessions": 6, "pendingSessions": 4,
  "paymentAmount": 5900, "paymentDate": "2026-10-01T00:00:00.000Z", "paymentMode": "UPI",
  "invoiceNumber": "INV-0012", "paymentVerified": true, "invoiceStatus": "GENERATED",
  "voiceStatus": "COMPLETED", "dataStatus": "COMPLETED", "sessionStatus": "BOOKED",
  "active": true, "updatedAt": "2026-10-12T09:00:00.000Z"
}
```

**GET `/api/clients/{id}`** — Full client record for the client detail page.

Response 200: `{ "client": { ...Client columns, "sessions": [ { ...Session, "payments": [ { ...Payment, "invoiceTransactions": [InvoiceTransaction] } ], "voiceProcessing": [ { ..., "template" } ], "dataProcessing": [ { ..., "template" } ] } ] }, "auditLogs": [ up to 100 AuditLog rows for the client, its sessions and payments, newest first ] }`. 404 if not found.

### 4. Sessions

**GET `/api/sessions`** — Paged list of active sessions, newest first.

Query: `page`, `pageSize`, `status` (PENDING, BOOKED, COMPLETED, CANCELLED), `clientId`.

Each item: Session columns + `client { clientName, phone }` + latest `payments[1]`, `voiceProcessing[1]`, `dataProcessing[1]`.

**GET `/api/sessions/{id}`** — One session with `client`, all `payments`, `voiceProcessing`, `dataProcessing`. 404 if not found.

**GET `/api/sessions/{id}/source-record`** — The raw spreadsheet row for the session.

```json
{ "source": "GOOGLE_SHEETS", "externalRowId": "row-42", "lastSyncedAt": "2026-10-12T09:00:00.000Z", "rawData": { "Client Name": "Ravi Kumar", "Amount": "5900" } }
```

### 5. Payments

**GET `/api/payments`** — Paged payments for active sessions, newest payment date first.

Query: `page`, `pageSize`, `paymentMode`, `invoiceNumber` (partial match), `dateFrom`, `dateTo` (YYYY-MM-DD).

Each item: Payment columns + `client { clientName, phone }` + `session { product, sessionNumber }`.

### 6. Invoices

**GET `/api/invoices/stats`** — Same ledger figures as the invoice half of the dashboard stats: `totalRecordsSynced`, `paidTransactions`, `pendingPayments`, `invoicesGenerated`, `invoicesPendingGeneration`, `duplicateTransactionsDetected`, `failedInvoiceGenerations`, `needsReview`, `totalAmount`, `verifiedAmount`, `pendingAmount`, `requiresVerificationAmount`, `confirmedSessionsCount`, `duplicateOrInvalidCount`.

**GET `/api/invoices`** — Paged consolidated invoices (one invoice can cover several payments).

Query: `page`, `pageSize`, `clientName` (partial), `invoiceNumber` (partial), `paymentDate` (exact day, YYYY-MM-DD), `paymentStatus` (paid, pending), `status` (PENDING, PAYMENT_VERIFIED, GENERATED, NEEDS_REVIEW, FAILED).

```json
{
  "id": "inv_1", "clientName": "Ravi Kumar", "phone": "9876543210", "state": "Karnataka",
  "invoiceNumber": "INV-0012", "paymentDate": "2026-10-01T00:00:00.000Z",
  "paymentAmount": 5900, "taxableValue": 5000, "igst": 0, "cgst": 450, "sgst": 450,
  "paymentMode": "UPI", "paymentProof": "https://...", "paymentStatusRaw": "Confirm", "paymentVerified": true,
  "status": "GENERATED", "reviewReason": null, "flags": [], "occurrenceCount": 1,
  "dataProcessingId": "dp_1", "generatedAt": "2026-10-01T10:00:00.000Z", "error": null,
  "updatedAt": "2026-10-01T10:00:00.000Z", "sessionsBooked": 4, "paymentsCount": 2
}
```

### 7. Processing (voice and invoice generation)

**GET `/api/processing/stats?type=VOICE|DATA`** — `{ "total", "pending", "processing", "completed", "failed" }`. Defaults to VOICE.

**GET `/api/processing`** — Paged processing records.

Query: `type` (VOICE, DATA; both when omitted), `status` (PENDING, PROCESSING, COMPLETED, FAILED, SKIPPED), `page`, `pageSize`.

Response: `{ "voice": [ {...VoiceProcessing, client, session, "type": "VOICE"} ], "data": [ {...DataProcessing, client, session, invoiceTransaction { invoiceNumber, sessionsBooked, paymentAmount, paymentsCount }, "type": "DATA"} ], "voiceTotal": 40, "dataTotal": 38 }`

**POST `/api/voice/{id}/retry`** and **POST `/api/data/{id}/retry`** — Reset the record to PENDING, clear its error, re-queue the job and write an audit row (`RETRY_VOICE` / `RETRY_DATA`). No body. Response `{ "ok": true }`; 404 if not found.

**GET `/api/data/{id}/pdf`** — Returns the invoice PDF as `application/pdf` (inline, file name `invoice-{id}.pdf`). 404 if no PDF yet.

**POST `/api/data/{id}/send-email`** — Emails the invoice PDF via Gmail, stores `lastEmailedAt` / `lastEmailedTo`, and writes an `INVOICE_EMAILED` audit row.

Request body:

```json
{ "to": "client@example.com" }
```

Response `{ "ok": true }`. Errors: 400 bad email, 404 no PDF, 502 mail provider failure.

### 8. Sync (Google Sheet / Excel)

**GET `/api/sync/status`** — `{ "latest": SyncRun | null, "recent": [ last 20 SyncRun rows ] }`.

**GET `/api/sync/source-rows`** — Synced spreadsheet rows. Query `page`, `pageSize` (default 50, max 200).

```json
{
  "columns": ["Client Name", "Phone", "Amount"],
  "data": [ { "rowKey": "...", "externalRowId": "row-42", "syncStatus": "SYNCED", "lastSyncedAt": "...", "rawData": { } } ],
  "page": 1, "pageSize": 50, "total": 500, "totalPages": 10
}
```

**POST `/api/sync/run`** — Asks the worker to sync now and writes a `SYNC_TRIGGERED` audit row. No body. Response `{ "triggered": true }`.

### 9. Revenue

**GET `/api/revenue`** — Revenue for active sessions.

```json
{
  "totalRevenue": 350000, "totalTaxableValue": 296610, "totalGst": 53390, "totalSessions": 300, "averagePaymentAmount": 1166.67,
  "byMonth": [ { "key": "2026-10", "paymentAmount": 50000, "taxableValue": 42373, "igst": 0, "cgst": 3813, "sgst": 3813, "count": 40 } ],
  "byProduct": [ ], "byState": [ ], "byPaymentMode": [ ], "bySource": [ ]
}
```

Each breakdown uses the same bucket shape, sorted by `paymentAmount` high to low; a missing key shows as `Unspecified`.

### 10. Payment verification (Razorpay)

**POST `/api/verification/upload`** — `multipart/form-data` with one field `file` (.xlsx, .xls or .csv, max 10 MB) from Razorpay's Payments export. Required columns: `id`/`payment_id`, `amount`, `status`; optional `method`, `email`, `contact`, `notes`, `created_at`/`date`. Each row is matched to a Payment by amount and date and upserted into `RazorpayPaymentRecord`.

Response 200:

```json
{ "batch": { "id": "rb_1", "fileName": "payments.xlsx", "rowCount": 120, "matchedCount": 110, "falsePaymentCount": 2, "noMatchCount": 6, "ambiguousCount": 2, "createdAt": "..." } }
```

**GET `/api/verification/summary`** — `{ "latestBatch": RazorpayImportBatch | null, "counts": { "MATCHED": 110, "FALSE_PAYMENT": 2, "NO_MATCH": 6, "AMBIGUOUS": 2 } }`.

**GET `/api/verification/issues`** — Paged payments marked paid that Razorpay does not confirm. Query `page`, `pageSize`.

```json
{ "id": "pay_1", "clientName": "Ravi Kumar", "phone": "9876543210", "invoiceNumber": "INV-0012", "paymentDate": "...", "paymentAmount": 5900, "paymentMode": "UPI", "issueType": "NO_RAZORPAY_RECORD", "razorpayStatus": null }
```

`issueType` is `NO_RAZORPAY_RECORD` or `FALSE_PAYMENT`.

### 11. Audit logs, templates and filters

**GET `/api/audit-logs`** — Query `page`, `pageSize` (default 50), `entity`, `action`. Each item: AuditLog columns + `user { name, email }`.

**GET `/api/templates`** — `{ "data": [ Template rows, oldest first ] }`.

**GET `/api/meta/filters`** — Distinct values from real data for dropdowns:

```json
{ "states": ["Karnataka", "Maharashtra"], "products": ["Counselling"], "paymentModes": ["Card", "UPI"] }
```

## Error codes and open questions

| Status | When | Body |
| --- | --- | --- |
| 400 | Invalid query, body or uploaded file | `{ "error": "Invalid request", "details": {...} }` |
| 401 | Missing, invalid or expired token; wrong login | `{ "error": "Invalid or expired token" }` |
| 403 | Role too low | `{ "error": "Requires OPERATOR role or higher" }` |
| 404 | Record not found | `{ "error": "Client not found" }` |
| 429 | Rate limit hit | `{ "error": "..." }` |
| 500 | Unexpected server or DB error | `{ "error": "Internal server error" }` |
| 502 | Email provider failed | `{ "error": "<provider message>" }` |

Open questions for the DB team:

- Which stored procedure maps to each endpoint? Please add the procedure name against each row of the summary table.
- Should list procedures accept `page` / `pageSize` (LIMIT/OFFSET) and return a total count, so paging matches the response shape above?
- Should procedures filter out inactive records (`active = 0`, rows removed from the spreadsheet), or should the API pass that as a parameter?
- Write endpoints (retry, send-email, Razorpay upload, sync run) also insert an `AuditLog` row. Should the procedure do that, or the API?
