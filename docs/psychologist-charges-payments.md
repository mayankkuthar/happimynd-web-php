# Psychologist Charges & Payments — Complete Logic Documentation

## Table of Contents

1. [Overview](#overview)
2. [Database Schema — Pricing Tables](#database-schema--pricing-tables)
3. [Price Calculation Hierarchy](#price-calculation-hierarchy)
4. [Plan-Level Pricing Logic (`app\Models\Plan.php`)](#plan-level-pricing-logic-appmodelsplanphp)
5. [Psychologist Commission & Earnings (`app\Models\Psychologist.php`)](#psychologist-commission--earnings-appmodelspsychologistphp)
6. [Custom Psychologist Pricing (`app\Models\PsychologistPlan.php`)](#custom-psychologist-pricing-appmodelspsychologistplanphp)
7. [Psychologist Appointment Session Cost (`app\Models\PsychologistAppointment.php`)](#psychologist-appointment-session-cost-appmodelspsychologistappointmentphp)
8. [Offer Pricing (`app\Models\Offer.php`)](#offer-pricing-appmodelsofferphp)
9. [Coupon Discount Logic](#coupon-discount-logic)
10. [Payment Flow — Web Platform](#payment-flow--web-platform)
11. [Payment Flow — Mobile/API Platform](#payment-flow--mobileapi-platform)
12. [Psychologist Payout Calculation](#psychologist-payout-calculation)
13. [Penalty Clauses (`app\Models\HappitalkPenaltyClause.php`)](#penalty-clauses-appmodelshappitalkpenaltyclausephp)
14. [Bundle Pricing Logic](#bundle-pricing-logic)
15. [API Routes — Payment Endpoints](#api-routes--payment-endpoints)
16. [Web Routes — Payment Endpoints](#web-routes--payment-endpoints)
17. [Complete File Index](#complete-file-index)

---

## Overview

This document describes how charges, payments, and psychologist payouts are calculated in the Happimynd platform. The system uses **Razorpay** as the payment gateway and supports both web (Laravel views) and mobile (REST API) payment flows.

### Key Concepts

| Concept | Description |
|---|---|
| **Plan** | A specific service offering with a base `price`, linked to a `Package` and `DurationType` |
| **Package** | A grouping/category like "HappiTALK", "HappiSELF", "HappiGUIDE" |
| **Offer** | A time-limited discount on a specific plan (`discount` %, `price`) |
| **Custom Price** | Per-psychologist override of plan pricing via `psychologist_plan` pivot |
| **Coupon** | Promo code with `discount_percent` applied to selected plans |
| **Commission** | The percentage of the receipt amount that goes to the psychologist |
| **TDS** | Tax Deducted at Source (% deducted from the commission amount) |

---

## Database Schema — Pricing Tables

### `plans` table
| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `package_id` | bigint | FK to `packages` |
| `duration_type_id` | bigint | FK to `duration_types` |
| `price` | float | Base price of the plan |
| `active` | tinyint | Whether the plan is active |
| `expert_level_id` | bigint | FK to `expert_levels` (nullable) |
| `deleted_at` | timestamp | Soft delete |

### `packages` table
| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `name` | string | e.g. "HappiTALK", "HappiSELF" |
| `bundle` | tinyint | Whether this is a bundle package |
| `regular_price` | float | Original price (legacy) |
| `deleted_at` | timestamp | Soft delete |

### `offers` table
| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `plan_id` | bigint | FK to `plans` |
| `name` | string | Offer name |
| `discount` | float | Discount percentage |
| `price` | float | Offer price (overrides plan price) |
| `special_inaugral_price` | float | Special inaugural price |
| `valid` | tinyint | Whether the offer is active |
| `start` / `end` | datetime | Offer validity period |

### `psychologist_plan` pivot table
| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `psychologist_id` | bigint | FK to `psychologists` |
| `plan_id` | bigint | FK to `plans` |
| `selling_price` | float | Custom selling price for this psychologist-plan pair |
| `cost_price` | float | Custom cost price |
| `discount` | float | Custom discount percentage |
| `day` | string | Day of week (if applicable) |

### `psychologists` table (financial fields)
| Column | Type | Description |
|---|---|---|
| `commission_percentage` | float | % of receipt amount that goes to the psychologist |
| `price_per_session` | float | Base price per session |
| `tds_percentage` | float | TDS % deducted from commission |

### `happitalk_taxes` table
| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `tds_percentage` | float | Global TDS % (seed: 10%) |

### `coupons` table
| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `code` | string | Coupon code |
| `discount_percent` | float | Discount % |
| `status` | tinyint | Active/inactive |
| `max_uses` | int | Max number of uses |
| `expired_at` | datetime | Expiry timestamp |

### `receipts` table
| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `marchant_name` | string | "RazorPay" or "apple_pay" |
| `amount` | float | Paid amount |
| `currency` | string | "INR" |
| `status` | tinyint | 0=pending, 1=captured |
| `order_id` | string | Razorpay order ID |
| `payment_id` | string | Razorpay payment ID |
| `user_id` | bigint | FK to `users` |

### `receipt_packages` table
| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `receipt_id` | bigint | FK to `receipts` |
| `plan_id` | bigint | FK to `plans` |
| `amount` | float | Amount paid for this plan |

### `happitalk_bookings` table
| Column | Type | Description |
|---|---|---|
| `user_id` | bigint | FK to `users` |
| `user_type` | string | "b2c" |
| `psychologist_id` | bigint | FK to `psychologists` |
| `amount` | float | Total receipt amount (before commission/TDS) |
| `amount_after_deduction` | float | Amount after commission and TDS (payout) |
| `plan_id` | bigint | FK to `plans` |
| `total_no_of_session` | int | Total sessions booked |
| `remaining_session` | int | Remaining sessions |

### `bundle_statuses` table
| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `user_id` | bigint | FK to `users` |
| `plan_id` | bigint | FK to `plans` |
| `receipt_id` | bigint | FK to `receipts` (nullable) |
| `valid` | tinyint | Whether the bundle/plan is still valid |
| `percentage_covered` | decimal | Progress percentage (100.00 = completed) |

### `duration_types` table
| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `type` | tinyint | 1=onetime, 2=session, 3=year, 4=month |
| `value` | int | Duration value (e.g. 45 for 45 min session) |
| `frequency` | int | Number of sessions/units (e.g. 1, 2, 4) |

### `happitalk_penalty_clauses` table
| Column | Type | Description |
|---|---|---|
| `for_b2b_user_for_one_credit` | float | Penalty for B2B user (1 credit) |
| `for_b2b_user_for_half_credit` | float | Penalty for B2B user (half credit) |
| `for_b2c_user_for_one_credit` | float | Penalty for B2C user (1 credit) |
| `for_b2c_user_for_half_credit` | float | Penalty for B2C user (half credit) |

---

## Price Calculation Hierarchy

The effective price of a plan is determined by the following priority order (highest to lowest):

```
1. Custom Psychologist Price (psychologist_plan.selling_price)
                    ↓
2. Offer Price (offers.price) — if valid offer exists
                    ↓
3. Base Price (plans.price)
```

Then optionally:
```
4. Coupon Discount → applied on top of the selling price
```

### Flow Diagram

```
User selects a Plan
        │
        ▼
  Does psychologist_customPrice exist? ──yes──→ Use custom selling_price
        │
        no
        ▼
  Does an Offer exist & valid? ──yes──→ Use offer->price
        │
        no
        ▼
  Use plan->base price
        │
        ▼
  Is Coupon applied? ──yes──→ Apply discount_percent on selling price
        │
        no
        ▼
  Final price = selling price
```

---

## Plan-Level Pricing Logic (`app\Models\Plan.php`)

**File:** `app\Models\Plan.php`

### Key Methods

#### `getSellingPrice()` (line 64)
```php
public function getSellingPrice()
{
    // Priority 1: Custom psychologist price
    if ($this->psychologistCustomPrice) {
        return $this->psychologistCustomPrice->selling_price;
    }
    // Priority 2: Offer price
    if ($this->offer) {
        return $this->offer->price;
    }
    // Priority 3: Base price
    return $this->price;  // from plans table
}
```

#### `getCostPrice()` (line 75)
```php
public function getCostPrice()
{
    if ($this->psychologistCustomPrice) {
        return $this->psychologistCustomPrice->cost_price;
    }
    return $this->price;
}
```
Returns the cost price (used for internal tracking). If custom pricing exists, returns the custom `cost_price`, otherwise falls back to the base `price`.

#### `getDiscount()` (line 88)
```php
public function getDiscount()
{
    if ($this->psychologistCustomPrice) {
        return $this->psychologistCustomPrice->discount;
    }
    if ($this->offer) {
        return $this->offer->discount;
    }
    return "";
}
```
Returns the discount percentage from custom pricing or offer.

#### `getPerSessionSellingPrice()` (line 99)
```php
public function getPerSessionSellingPrice()
{
    return (int)($this->selling_price / $this->duration->frequency);
}
```
Divides the total selling price by the number of sessions (`duration.frequency`) to get the per-session cost.

#### `getSellingPriceWithDiscount($discount_code)` (line 162)
```php
public function getSellingPriceWithDiscount($discount_code = '')
{
    if (!empty($discount_code) && $this->couponPlan->count() > 0) {
        $couponPlan = $this->couponPlan()->whereHas('coupon', function ($query) use ($discount_code) {
            return $query->ActiveCoupon()->where('code', $discount_code);
        })->with('coupon')->first();
        if (!empty($couponPlan)) {
            return $this->getCouponDiscountPrice($couponPlan->coupon->discount_percent);
        }
    }
    return $this->getSellingPrice();
}
```
Checks if the coupon code is valid and linked to this plan. If yes, applies the discount on top of the selling price.

#### `getCouponDiscountPrice($discount_percentage)` (line 156)
```php
public function getCouponDiscountPrice($discount_percentage)
{
    $price = $this->getSellingPrice();
    return round($price - ($price * ($discount_percentage / 100)), 2);
}
```
Applies a percentage discount to the selling price: `round(price - (price * discount% / 100), 2)`.

#### `isHappiTalkPlan()` (line 151)
```php
public function isHappiTalkPlan()
{
    return $this->package->name == "HappiTALK";
}
```
Identifies whether this plan belongs to the HappiTALK package (talk therapy with psychologists).

#### `getSessionSellingPrice()` (line 104)
```php
public function getSessionSellingPrice()
{
    if ($this->psychologistCustomPrice) {
        return (int)$this->psychologistCustomPrice->selling_price;
    }
    return (int)$this->offer->price;
}
```
**Note:** This method does NOT fall back to base price (only checks custom and offer).

#### `getSessionCostPrice()` (line 112)
```php
public function getSessionCostPrice()
{
    if ($this->psychologistCustomPrice) {
        return $this->psychologistCustomPrice->cost_price;
    }
    return $this->price;
}
```

### Relationships

```php
public function package()       → BelongsTo(Package::class)
public function duration()      → BelongsTo(DurationType::class)
public function offer()         → HasOne(Offer::class)
public function customPrice()   → HasMany(PsychologistPlan::class, 'plan_id')
public function couponPlan()    → HasMany(CouponPlan::class)
public function expertLevel()   → BelongsTo(ExpertLevel::class)
public function bundleStatus()  → HasMany(BundleStatus::class)
```

The `psychologistCustomPrice` attribute is dynamically set through the `Psychologist::customPrice()` relationship with `->withPivot(['selling_price', 'cost_price', 'discount'])->as('psychologistCustomPrice')`, using the `PsychologistPlan` pivot model.

---

## Psychologist Commission & Earnings (`app\Models\Psychologist.php`)

**File:** `app\Models\Psychologist.php`

### Financial Fields (from `$fillable`, line 43)
```php
protected $fillable = [
    // ...
    'commission_percentage',   // % of receipt that goes to the psychologist
    'price_per_session',       // base price per session
];
```

### Computed Attributes (from `$appends`, line 61)
```php
protected $appends = [
    'total_earned',
    'to_be_shared',
];
```

### Key Methods

#### `getTotalEarnedAttribute()` (line 212)
```php
public function getTotalEarnedAttribute(){
    return $this->hasMany(HappitalkBooking::class)->sum('amount');
}
```
Returns the sum of all `HappitalkBooking.amount` values (total receipt amounts before deductions) for this psychologist.

#### `getToBeSharedAttribute()` (line 216)
```php
public function getToBeSharedAttribute(){
    return $this->hasMany(HappitalkBooking::class)->sum('amount_after_deduction');
}
```
Returns the sum of all `HappitalkBooking.amount_after_deduction` values (amounts after commission + TDS) — this is the **net payout** to the psychologist.

#### `customPrice()` (line 165)
```php
public function customPrice()
{
    return $this->belongsToMany(Plan::class, 'psychologist_plan')
        ->using(PsychologistPlan::class)
        ->withPivot(['selling_price', 'cost_price', 'discount'])
        ->as('psychologistCustomPrice');
}
```
Defines the many-to-many relationship with `Plan` through the `psychologist_plan` pivot table. The pivot data (`selling_price`, `cost_price`, `discount`) is accessible as `$plan->psychologistCustomPrice->selling_price` on the plan object when loaded through this relationship.

#### `hasCustomPrice()` (line 170)
```php
public function hasCustomPrice()
{
    return $this->customPrice->count() > 0;
}
```

#### `getPsychologistPlans()` (line 175)
```php
public function getPsychologistPlans()
{
    $plans = collect();
    $plans = $plans->merge($this->expertLevel->plan()->with('duration')->get());
    if ($this->hasCustomPrice()) {
        $plans = $plans->merge($this->customPrice()->with('duration')->get());
    }
    return $plans->keyBy('duration_type_id');
}
```
Merges plans from the psychologist's expert level with any custom-priced plans. Returns a collection keyed by `duration_type_id`.

#### `getMinimumSessionPrice()` (line 186)
```php
public function getMinimumSessionPrice()
{
    $plans = $this->getPsychologistPlans();
    $min = PHP_INT_MAX;
    foreach ($plans as $plan) {
        $pricePerSession = $plan->getPerSessionSellingPrice();
        if ($min > $pricePerSession) {
            $min = $pricePerSession;
        }
    }
    $this->minPricePerSession = $min;
    return (int)$this->minPricePerSession;
}
```
Iterates through all the psychologist's plans and finds the lowest per-session selling price. Used for displaying starting prices on the UI.

---

## Custom Psychologist Pricing (`app\Models\PsychologistPlan.php`)

**File:** `app\Models\PsychologistPlan.php`

This is a **pivot model** extending `Illuminate\Database\Eloquent\Relations\Pivot`. It connects `psychologists` and `plans` with additional financial columns:

```php
class PsychologistPlan extends Pivot
{
}
```

The pivot table `psychologist_plan` has these financial columns (from migration):
- `selling_price` (float) — Custom selling price for this psychologist
- `cost_price` (float) — Custom cost price
- `discount` (float) — Custom discount percentage
- `day` (string) — Day of week (if day-specific pricing)

---

## Psychologist Appointment Session Cost (`app\Models\PsychologistAppointment.php`)

**File:** `app\Models\PsychologistAppointment.php`

### `baseSessionCost()` (line 28)
```php
public function baseSessionCost()
{
    $psychologist = $this->psychologist;
    $planCost = $psychologist->getPsychologistPlans();
    $base_price = 0;
    foreach($planCost as $plan){
        if($plan['duration']['frequency'] == $this->sessions ){
            $base_price = $plan->getPerSessionSellingPrice();
            break;
        }
    }
    return $base_price;
}
```
Calculates the cost of a single session for an appointment by:
1. Getting all the psychologist's plans
2. Finding the plan whose `duration.frequency` matches the appointment's `sessions` count
3. Returning the per-session selling price

---

## Offer Pricing (`app\Models\Offer.php`)

**File:** `app\Models\Offer.php`

### Fields
```php
protected $fillable = [
    'name',
    'discount',                // Discount percentage
    'price',                   // Offer price (overrides plan price)
    'special_inaugral_price',  // Special inaugural price
    'valid',                   // Boolean: is the offer active
    'start',                   // Start datetime
    'end',                     // End datetime
    'plan_id'                  // FK to Plan
];
```

### `discountedPrice()` on `OtherService` (line 41)
```php
public function discountedPrice()
{
    $discountAmount  = $this->discount;
    $price = $this->price;
    $discountedPrice = $price - (($discountAmount/100) * $price);
    return $discountedPrice;
}
```
This is on the `OtherService` model (non-core services), not on `Offer`, but follows the same pattern: `price - (price * discount% / 100)`.

---

## Coupon Discount Logic

### `CouponService::verifyCoupon()` (`app\Services\CouponService.php`)

```php
public function verifyCoupon($code, $plan_ids)
```
Validates a coupon code against selected plan IDs:
1. Checks coupon is active (`status = 1`)
2. Checks coupon hasn't exceeded `max_uses`
3. Checks coupon hasn't expired (`expired_at`)
4. Checks coupon hasn't already been used by this user
5. Checks coupon is applicable to at least one selected plan
6. Returns the `discount_percent` if valid

### `Coupon` Model (`app\Models\Coupon.php`)

```php
protected $casts = ['expired_at' => 'datetime'];

public function scopeActiveCoupon($query)
{
    return $query->where('status', 1);
}
```

### `CouponPlan` Model (`app\Models\CouponPlan.php`)

Maps a coupon to specific plans it can be applied to.

### `CouponReceipt` Model (`app\Models\CouponReceipt.php`)

Tracks usage: `coupon_id`, `receipt_id`, `user_id`.

```php
public function isPurchased()
{
    if ($this->coupon->discount_percent == 100) {
        return true;  // 100% off coupons don't generate actual receipts
    }
    return $this->receipt->status == 1;
}
```

---

## Payment Flow — Web Platform

**Controller:** `app\Http\Controllers\PaymentController.php`

### Flow: `buyBundle()` → `orderBundle()` → `PaymentService::paymentRequest()` → Razorpay → `responseBundle()`

#### Step 1: `buyBundle()` (line 38)
- Fetches all packages with their plans, offers, and durations
- Sorts packages in a predefined order: `["HappiLIFE Screening", "HappiLIFE Summary Reading", "HappiGUIDE", "HappiBUDDY", "HappiSELF", "HappiTALK", ...]`
- For "HappiTALK", only shows the minimum price plan

#### Step 2: `orderBundle()` (line 78)
**Price calculation logic:**
1. Fetches selected plans with offers
2. Checks if user already subscribed to any of these plans (duplicate prevention)
3. **For organization (B2B) users:**
   - If the plan is a HappiTALK plan and the organization already has it → `price = 0`
   - If the plan is in the organization's free entitlement → `price = 0`
   - Otherwise → `price = $plan->getSellingPriceWithDiscount($coupon_code)`
4. **For individual (B2C) users:**
   - `price = $plan->getSellingPriceWithDiscount($coupon_code)`
5. Sums all prices into `$amount`
6. If `$amount <= 0`: Creates `BundleStatus` records directly (free plan)
7. If `$amount > 0`: Calls `PaymentService::paymentRequest()`

#### Step 3: `PaymentService::paymentRequest()` (line 45)
1. Creates a `Receipt` record with `amount`, `currency`, `user_id`
2. Links plans to the receipt via `ReceiptPackage` records
3. If coupon is present, creates a `CouponReceipt` record
4. Creates a Razorpay order: `amount * 100` (Razorpay expects paise)
5. Saves `order_id` on the receipt
6. Returns the Razorpay checkout view

#### Step 4: `responseBundle()` (line 600)
- Called by Razorpay after payment via callback
- Fetches the order from Razorpay and verifies payment status
- If captured: Creates `BundleStatus` records, creates `PsychologistAppointment` if HappiTALK
- Shows success message

### Flow: `bookPsychologist()` → `psychologistPaymentResponse()`

#### `bookPsychologist()` (line 472)
1. Fetches psychologist's plans via `getPsychologistPlans()`
2. Finds the requested plan
3. Calculates price: `$plan->getSellingPrice()`
4. If coupon is provided, applies discount: `$amount = round($amount * (100 - $discount_percent) / 100, 2)`
5. Calls `PaymentService::paymentRequest()`

#### `psychologistPaymentResponse()` (line 526)
- Similar to `responseBundle()` but specific to psychologist booking
- Creates `BundleStatus` and `PsychologistAppointment`

---

## Payment Flow — Mobile/API Platform

**Controller:** `app\Http\Controllers\api\v1\PaymentController.php`

### Endpoints

#### `payment()` (line 123)
1. Validates `plan_id` and `amount`
2. Receives the amount **from the client** (not server-calculated for generic payments)
3. Creates receipt and Razorpay order
4. Generates a payment link: `api/v1/payment-link/{order_id}/{user_id}/{plan_id}/{coupen_id}`

#### `paymentForHappitalk()` (line 487)
1. Validates psychologist ID, plan ID, amount, date, time, session, recording permission
2. Checks slot availability (start time and end time overlap checks)
3. Creates receipt and Razorpay order (amount from client)
4. Generates payment link: `api/v1/payment-link-for-happitalk/{order_id}/{user_id}/{plan_id}/{psychologist_id}/{date}/{time}/{session}/{recording_permission}/{coupen_id}`

#### `paymentForHappiguide()` (line 770)
1. Validates plan ID, amount, date, time
2. Checks if any psychologist is mapped to HappiGuide
3. Creates receipt and Razorpay order
4. Generates payment link

#### `successPaymentPageForHappitalk()` (line 619)
**This is where the psychologist payout is calculated.**

```php
$psy_details = Psychologist::where('id', $psychologist_id)->first();

$commission_percentage = $psy_details->commission_percentage;
$tds_percentage = $psy_details->tds_percentage;

$amount_with_commission = $receipt->amount / 100 * $commission_percentage;
$amount_after_tds_deduction = $amount_with_commission - ($amount_with_commission / 100 * $tds_percentage);
```

Then creates a `HappitalkBooking` record:
```php
$booking_details = [
    'user_id' => $user_id,
    'user_type' => 'b2c',
    'psychologist_id' => $psychologist_id,
    'amount' => $receipt->amount,
    'amount_after_deduction' => $amount_after_tds_deduction,
    'plan_id' => $plan_id,
    'total_no_of_session' => $session,
    'remaining_session' => $session - 1,
];
HappitalkBooking::create($booking_details);
```

Also creates a `HappitalkSession` record with date/time details.

#### `PaymentForIos()` (line 430)
Handles iOS in-app purchase receipts (Apple Pay / App Store):
1. Creates `Receipt` with `marchant_name = apple_pay` and `status = 1`
2. Creates `ReceiptPackage` and `BundleStatus` directly (payment verified by Apple)

#### `handleWebhook()` (line 989)
Razorpay webhook handler:
- Verifies webhook signature using `WEBHOOK_SECRET` from `.env`
- On `payment.captured` event: updates receipt `status = 1`

#### `applyCoupon()` (line 318)
1. Validates coupon code exists and is active
2. Checks coupon has not expired
3. Checks coupon is applicable to the given `plan_id`
4. Returns coupon ID and discount percentage

---

## Psychologist Payout Calculation

The payout calculation happens in **`api/v1/PaymentController@successPaymentPageForHappitalk()`** (lines 682-703).

### Formula

```
amount_with_commission = receipt.amount × (commission_percentage / 100)

amount_after_tds_deduction = amount_with_commission - (amount_with_commission × tds_percentage / 100)
```

### Example

| Variable | Value |
|---|---|
| Receipt Amount | ₹800 |
| Commission % | 70% |
| TDS % | 10% |
| Amount with Commission | ₹800 × 70% = **₹560** |
| TDS Deduction | ₹560 × 10% = **₹56** |
| Final Payout | ₹560 - ₹56 = **₹504** |

### Recording

The payout is recorded in two fields on `HappitalkBooking`:
- `amount` = ₹800 (the full receipt amount, for reporting)
- `amount_after_deduction` = ₹504 (the actual payout to the psychologist)

### Aggregation

On the `Psychologist` model:
- `total_earned` = sum of all `HappitalkBooking.amount` (gross)
- `to_be_shared` = sum of all `HappitalkBooking.amount_after_deduction` (net payout)

---

## Penalty Clauses (`app\Models\HappitalkPenaltyClause.php`)

**File:** `app\Models\HappitalkPenaltyClause.php`

```php
protected $fillable = [
    'for_b2b_user_for_one_credit',
    'for_b2b_user_for_half_credit',
    'for_b2c_user_for_one_credit',
    'for_b2c_user_for_half_credit',
];
```
Defines penalty fees for session cancellations, separate for B2B and B2C users, and for full vs half credit penalties.

---

## Bundle Pricing Logic

### `Package::getMinimumPricePlan()` (line 50)
```php
public function getMinimumPricePlan()
{
    $plans = $this->plan()->whereHas('expertLevel')->with('expertLevel')->get();
    $minPrice = null;
    foreach ($plans as $plan) {
        if ($plan->expertLevel) {
            if ($minPrice == null) {
                $minPrice = $plan;
            } else if ($plan->getPerSessionSellingPrice() < $minPrice->getPerSessionSellingPrice()) {
                $minPrice = $plan;
            }
        }
    }
    return $minPrice;
}
```
Finds the cheapest plan (by per-session price) within a package. Used to show the starting price for HappiTALK.

### `DynamicBundlePlan`
```php
protected $fillable = ['package_id', 'plan_id'];
```
Maps a bundle package to its constituent plans. When a user purchases a bundle, `BundleStatus` records are created for each plan in the bundle.

---

## API Routes — Payment Endpoints

**File:** `routes/api.php`

| Route | Method | Controller Method | Description |
|---|---|---|---|
| `v1/handle-webhook` | GET, POST | `PaymentController@handleWebhook` | Razorpay webhook |
| `v1/buy-plan` | GET | `PaymentController@buyPlan` | List available plans |
| `v1/payment` | GET, POST | `PaymentController@payment` | Create payment (generic) |
| `v1/payment-for-happitalk` | GET, POST | `PaymentController@paymentForHappitalk` | Create HappiTALK payment |
| `v1/payment-for-happiguide` | GET, POST | `PaymentController@paymentForHappiguide` | Create HappiGUIDE payment |
| `v1/my-subscribed-services` | GET, POST | `PaymentController@mySubscribedServices` | List user's subscriptions |
| `v1/apply-coupon` | GET, POST | `PaymentController@applyCoupon` | Apply coupon code |
| `v1/avail-free-services` | GET, POST | `PaymentController@availFreeService` | Avail free service (₹0) |
| `v1/payment-for-ios` | GET, POST | `PaymentController@PaymentForIos` | iOS payment receipt |
| `v1/payment-link/{order_id}/{user_id}/{plan_id}/{coupen_id}` | GET, POST | `PaymentController@paymentLink` | Show payment page |
| `v1/success-payment-page/{order_id}/{user_id}/{plan_id}/{coupen_id}` | GET, POST | `PaymentController@successPaymentPage` | Payment success callback |
| `v1/payment-link-for-happitalk/{order_id}/{user_id}/{plan_id}/{psy_id}/{date}/{time}/{sessions}/{rec_permission}/{coupen_id}` | GET, POST | `PaymentController@paymentLinkForHappitalk` | HappiTALK payment page |
| `v1/success-payment-page-for-happitalk/{order_id}/{user_id}/{plan_id}/{psy_id}/{date}/{time}/{sessions}/{rec_permission}/{coupen_id}` | GET, POST | `PaymentController@successPaymentPageForHappitalk` | HappiTALK payment + payout |
| `v1/payment-link-for-happiguide/{order_id}/{user_id}/{plan_id}/{date}/{time}/{coupen_id}` | GET, POST | `PaymentController@paymentLinkForHappiguide` | HappiGUIDE payment page |
| `v1/success-payment-page-for-happiguide/{order_id}/{user_id}/{plan_id}/{date}/{time}/{coupen_id}` | GET, POST | `PaymentController@successPaymentPageForHappiguide` | HappiGUIDE payment callback |

All payment routes (except webhook and payment-link pages) are protected by `auth:api` middleware.

---

## Web Routes — Payment Endpoints

**File:** `routes/web.php`

| Route | Method | Controller Method | Description |
|---|---|---|---|
| `buy-bundles/` | GET | `PaymentController@buyBundle` | Show bundle purchase page |
| `subscribedservices` | GET | `PaymentController@subscribedServices` | Show subscribed services |
| `payment/orderBundle` | GET | `PaymentController@orderBundle` | Process bundle order |
| `payment/responseBundle` | ANY | `PaymentController@responseBundle` | Payment callback |
| `payment/response-other-services` | POST | `PaymentController@responseOtherServices` | Other services payment callback |
| `payment/book-psychologist` | GET | `PaymentController@bookPsychologist` | Book psychologist |
| `payment/psychologist-payment-response` | ANY | `PaymentController@psychologistPaymentResponse` | Psychologist booking callback |
| `admin/payment-detail/` | GET | `PaymentController@paymentDetail` | Admin view all Razorpay payments |
| `admin/payment-detail-ios/` | GET | `PaymentController@paymentDetailIos` | Admin view all iOS payments |

---

## Complete File Index

### Models (Pricing & Payment Data)
| File | Purpose |
|---|---|
| `app\Models\Plan.php` | Central pricing model with selling/cost/discount/coupon price methods |
| `app\Models\Package.php` | Package grouping with `getMinimumPricePlan()` |
| `app\Models\Offer.php` | Offer pricing (discount %, price, special_inaugral_price) |
| `app\Models\Coupon.php` | Coupon with `discount_percent`, `ActiveCoupon` scope |
| `app\Models\CouponPlan.php` | Maps coupons to applicable plans |
| `app\Models\CouponReceipt.php` | Tracks coupon usage per receipt |
| `app\Models\Psychologist.php` | Commission %, earnings aggregates, custom price relationship |
| `app\Models\PsychologistPlan.php` | Pivot: custom pricing per psychologist per plan |
| `app\Models\PsychologistAppointment.php` | `baseSessionCost()` calculation |
| `app\Models\HappitalkBooking.php` | Stores `amount` and `amount_after_deduction` per booking |
| `app\Models\HappitalkTax.php` | TDS percentage configuration |
| `app\Models\HappitalkPenaltyClause.php` | Penalty fees for cancellations |
| `app\Models\HappitalkSession.php` | Per-session tracking within a booking |
| `app\Models\Receipt.php` | Payment receipt with amount, order_id, status |
| `app\Models\ReceiptPackage.php` | Line items linking receipts to plans |
| `app\Models\BundleStatus.php` | Tracks user subscription to plans |
| `app\Models\DurationType.php` | Duration/frequency definitions (onetime, session, year, month) |
| `app\Models\ExpertLevel.php` | Expert levels mapped to plans |
| `app\Models\DynamicBundlePlan.php` | Bundle package to plan mappings |
| `app\Models\OtherService.php` | Non-core services with `discountedPrice()` |
| `app\Models\AssignPsyToPlan.php` | Psychologist assignment to plans (HappiGuide) |

### Services (Business Logic)
| File | Purpose |
|---|---|
| `app\Services\PaymentService.php` | Core Razorpay payment orchestration (`paymentRequest`, `getPaymentResponse`, `getPsychologistPaymentResponse`) |
| `app\Services\CouponService.php` | Coupon verification and validation |
| `app\Services\BitrixService.php` | Syncs payment data to Bitrix CRM |

### Controllers (Payment Logic)
| File | Purpose |
|---|---|
| `app\Http\Controllers\PaymentController.php` | Web payment flow (894 lines) — `buyBundle`, `orderBundle`, `bookPsychologist`, `responseBundle`, `psychologistPaymentResponse` |
| `app\Http\Controllers\api\v1\PaymentController.php` | API payment flow (1022 lines) — `payment`, `paymentForHappitalk`, `paymentForHappiguide`, `applyCoupon`, `PaymentForIos`, `successPaymentPageForHappitalk` (payout calc), `handleWebhook` |
| `app\Http\Controllers\CouponController.php` | Coupon CRUD and verification display |
| `app\Http\Controllers\AdminController.php` | Admin bundle management, price updates |

### Routes
| File | Purpose |
|---|---|
| `routes\api.php` | All mobile API payment endpoints |
| `routes\web.php` | All web payment endpoints |

### Migrations (Schema)
| File | Table Created |
|---|---|
| `database\migrations_old\2021_02_18_120313_create_plans_table.php` | `plans` |
| `database\migrations_old\2021_01_31_150859_create_receipts_table.php` | `receipts` |
| `database\migrations_old\2021_02_08_200332_create_receipt_packages_table.php` | `receipt_packages` |
| `database\migrations_old\2021_06_12_205850_create_psychologist_plan_table.php` | `psychologist_plan` (pivot) |
| `database\migrations_old\2021_02_08_182644_create_offers_table.php` | `offers` |
| `database\migrations_old\2021_07_29_145101_create_coupons_table.php` | `coupons` |
| `database\migrations_old\2021_06_05_222742_create_psychologists_table.php` | `psychologists` |
| `database\migrations_old\2021_06_19_154630_create_psychologist_appointments_table.php` | `psychologist_appointments` |
| `database\migrations_old\2021_01_30_131216_create_packages_table.php` | `packages` |
| `database\migrations_old\2021_01_31_154601_create_bundle_statuses_table.php` | `bundle_statuses` |

### Configuration
| File | Purpose |
|---|---|
| `.env` | `RAZORPAY_KEY`, `RAZORPAY_SECRET`, `WEBHOOK_SECRET` |
| `database\seeders\HappiTalkTaxSeeder.php` | Seeds default TDS percentage (10%) |
