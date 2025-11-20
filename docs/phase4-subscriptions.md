# Phase 4 – Subscriptions & Commerce Implementation

## Overview
This document provides an overview of the subscription and billing system implemented in Phase 4.

## Database Tables

### 1. subscription_plans
Stores all available subscription plans with features and pricing.

**Key Fields:**
- `name_ar`, `name_en`: Plan names in Arabic and English
- `price`, `currency`: Pricing information
- `billing_period`: monthly, quarterly, or yearly
- `trial_days`: Number of free trial days
- `max_profiles`, `max_devices`: Usage limits
- `video_quality`: sd, hd, or uhd
- `download_enabled`, `ads_enabled`, `live_tv_enabled`: Feature flags

### 2. plan_limitations
Flexible limitations system for plans (e.g., max concurrent streams, allowed qualities).

### 3. user_subscriptions
Tracks user subscriptions with status, dates, and payment information.

**Statuses:**
- `pending`: Created but not paid
- `trial`: In trial period
- `active`: Fully active subscription
- `canceled`: User canceled
- `expired`: Subscription ended
- `suspended`: Temporarily suspended

### 4. plan_content_access
Controls which content (categories, movies, series) users can access based on their plan.

### 5. user_active_devices
Tracks registered devices and enforces concurrent stream limits.

### 6. coupons
Discount coupons with usage limits and expiration dates.

### 7. coupon_redemptions
Tracks coupon usage by users.

---

## API Endpoints

### Public Endpoints

#### GET /api/v1/plans
List all active subscription plans.

#### GET /api/v1/plans/{id}
Get details of a specific plan.

### Authenticated Endpoints

#### POST /api/v1/subscriptions
Create a new subscription.
- **Body:** `plan_id`, `coupon_code` (optional)
- **Response:** Subscription object with status `pending`

#### GET /api/v1/subscriptions/me
Get current user's active subscription.

#### POST /api/v1/subscriptions/cancel
Cancel current subscription.
- **Body:** `reason` (optional)

#### GET /api/v1/subscriptions/check-quality
Check if user can use specific video quality.
- **Query:** `quality` (sd, hd, uhd)

#### POST /api/v1/billing/checkout
Create payment checkout session.
- **Body:** `subscription_id`, `success_url`, `cancel_url`
- **Response:** `payment_url` to redirect user

#### POST /api/v1/coupons/validate
Validate a coupon code.
- **Body:** `code`, `plan_id`
- **Response:** Validation result with discount calculation

#### POST /api/v1/devices/register
Register a new device.
- **Body:** `device_id`, `profile_id` (optional)

#### POST /api/v1/devices/heartbeat
Update device activity (keep-alive).
- **Body:** `device_id`

#### GET /api/v1/devices
List user's registered devices.

#### POST /api/v1/devices/{deviceId}/deactivate
Deactivate a device.

### Webhook Endpoints

#### POST /api/v1/billing/webhook
Receive payment gateway webhooks (no authentication required).

---

## Payment Gateways

### Supported Gateways
1. **Paylink** (Saudi Arabia)
2. **Telr** (UAE/Middle East)

### Configuration

Add these to your `.env` file:

```env
# Default gateway
SUBSCRIPTION_DEFAULT_GATEWAY=paylink

# Test mode (true = sandbox, false = live)
SUBSCRIPTION_TEST_MODE=true

# Paylink
PAYLINK_API_KEY=your_key
PAYLINK_SECRET_KEY=your_secret

# Telr
TELR_STORE_ID=your_store_id
TELR_AUTH_KEY=your_auth_key
```

### Gateway Manager
The `PaymentGatewayManager` service handles switching between gateways automatically based on configuration.

### Test Mode
When `SUBSCRIPTION_TEST_MODE=true`:
- No real charges occur
- Payments are simulated
- All logic flows are tested
- Logs are generated for debugging

---

## Access Control

### SubscriptionAccessService
Centralized service for checking user access permissions:

- `canAccessCategory($user, $categoryId)`
- `canAccessMovie($user, $movieId)`
- `canAccessSeries($user, $seriesId)`
- `canUseQuality($user, $quality)`
- `canDownload($user)`
- `canAccessLiveTV($user)`
- `canStartStream($user, $deviceId)`
- `canRegisterDevice($user)`

### Usage Example

```php
use App\Services\Subscriptions\SubscriptionAccessService;

$accessService = app(SubscriptionAccessService::class);

if ($accessService->canAccessMovie($user, $movieId)) {
    // Allow access
} else {
    // Deny access
}
```

---

## Models

### SubscriptionPlan
Represents a subscription plan with features and pricing.

**Relationships:**
- `limitations()`: hasMany PlanLimitation
- `contentAccess()`: hasMany PlanContentAccess
- `activeSubscriptions()`: hasMany UserSubscription
- `coupons()`: hasMany Coupon

### UserSubscription
User's subscription instance.

**Methods:**
- `canAccessCategory($categoryId)`
- `canAccessMovie($movieId)`
- `canAccessSeries($seriesId)`
- `canUseQuality($quality)`
- `canDownload()`

### Coupon
Discount coupon.

**Methods:**
- `isValid()`: Check if coupon is active and not expired
- `canBeUsedByUser($userId)`: Check if user can use this coupon
- `calculateDiscount($amount)`: Calculate discount amount

---

## Testing

Run subscription tests:

```bash
php artisan test --filter=SubscriptionTest
```

### Test Coverage
- List plans
- View single plan
- Create subscription
- Prevent duplicate subscriptions
- Validate coupons
- Check expired coupons
- Get active subscription

---

## Workflow

### 1. User Selects Plan
1. User browses available plans via `GET /api/v1/plans`
2. User selects a plan
3. Optionally validates a coupon via `POST /api/v1/coupons/validate`

### 2. Create Subscription
1. User creates subscription via `POST /api/v1/subscriptions`
2. System creates `UserSubscription` with status `pending`
3. Coupon discount is applied if valid

### 3. Payment
1. User initiates payment via `POST /api/v1/billing/checkout`
2. System creates payment session with selected gateway
3. User is redirected to payment gateway
4. User completes payment

### 4. Activation
1. Payment gateway sends webhook to `/api/v1/billing/webhook`
2. System verifies payment
3. Subscription status changes to `active`
4. Coupon usage is recorded

### 5. Usage
1. User accesses content
2. System checks permissions via `SubscriptionAccessService`
3. Access is granted or denied based on plan features

---

## Security Considerations

1. **Webhook Verification**: Verify webhook signatures from payment gateways
2. **Device Limiting**: Enforce max devices per user
3. **Concurrent Streams**: Check active devices before allowing stream
4. **Coupon Validation**: Prevent coupon abuse with usage limits
5. **Subscription Status**: Regularly check for expired subscriptions

---

## Future Enhancements

1. Automatic subscription renewal
2. Proration for plan upgrades/downgrades
3. Payment method management
4. Invoice generation
5. Subscription analytics
6. Admin dashboard for subscription management
7. Email notifications for subscription events
8. Grace period for failed payments

---

## Support

For questions or issues, contact the development team or refer to the main project documentation.

