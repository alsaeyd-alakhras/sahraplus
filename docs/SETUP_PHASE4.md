# Phase 4 Setup Guide - Subscriptions & Commerce

## Prerequisites
- Laravel application running
- Database configured
- PHP 8.1+
- Composer installed

## Installation Steps

### 1. Run Migrations

```bash
php artisan migrate
```

This will create the following tables:
- `subscription_plans`
- `plan_limitations`
- `user_subscriptions`
- `plan_content_access`
- `user_active_devices`
- `coupons`
- `coupon_redemptions`

### 2. Seed Sample Data

```bash
php artisan db:seed --class=SubscriptionPlansSeeder
php artisan db:seed --class=CouponsSeeder
```

Or run all seeders:

```bash
php artisan db:seed
```

This will create:
- 4 subscription plans (Basic, Standard, Premium, Basic-Yearly)
- 5 sample coupons (WELCOME20, SAVE5, PREMIUM30, BLACKFRIDAY50, TEST10)

### 3. Configure Payment Gateways

Add the following to your `.env` file:

```env
# Subscription Settings
SUBSCRIPTION_DEFAULT_GATEWAY=paylink
SUBSCRIPTION_TEST_MODE=true

# Paylink Configuration
PAYLINK_API_URL=https://api.paylink.sa/api
PAYLINK_SANDBOX_URL=https://sandbox.paylink.sa/api
PAYLINK_API_KEY=your_paylink_api_key_here
PAYLINK_SECRET_KEY=your_paylink_secret_key_here

# Telr Configuration
TELR_API_URL=https://secure.telr.com/gateway/order.json
TELR_SANDBOX_URL=https://secure.telr.com/gateway/order.json
TELR_STORE_ID=your_telr_store_id_here
TELR_AUTH_KEY=your_telr_auth_key_here
```

### 4. Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

## Testing

### Run Tests

```bash
php artisan test --filter=SubscriptionTest
```

### Test API Endpoints

#### 1. Get Plans (No Auth Required)
```bash
curl -X GET http://localhost:8000/api/v1/plans
```

#### 2. Create Subscription (Auth Required)
```bash
curl -X POST http://localhost:8000/api/v1/subscriptions \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "plan_id": 1,
    "coupon_code": "WELCOME20"
  }'
```

#### 3. Validate Coupon
```bash
curl -X POST http://localhost:8000/api/v1/coupons/validate \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "WELCOME20",
    "plan_id": 1
  }'
```

#### 4. Get Active Subscription
```bash
curl -X GET http://localhost:8000/api/v1/subscriptions/me \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Test Mode

When `SUBSCRIPTION_TEST_MODE=true`:
- Payment gateways will not process real transactions
- Test URLs will be generated instead of live payment URLs
- All webhook callbacks will be simulated
- Logs will be created for debugging

## Switching to Live Mode

1. Set `SUBSCRIPTION_TEST_MODE=false` in `.env`
2. Update API keys to production keys
3. Update webhook URLs in payment gateway dashboards
4. Test thoroughly before going live

## Webhook Setup

### Paylink Webhook
Set this URL in your Paylink dashboard:
```
https://yourdomain.com/api/v1/billing/webhook?gateway=paylink
```

### Telr Webhook
Set this URL in your Telr dashboard:
```
https://yourdomain.com/api/v1/billing/webhook?gateway=telr
```

## Troubleshooting

### Issue: Migration fails with foreign key constraint
**Solution:** Make sure `users` and `subscription_plans` tables exist before running migrations.

### Issue: Payment gateway returns error
**Solution:** 
1. Check API keys in `.env`
2. Verify you're in test mode if testing
3. Check logs in `storage/logs/laravel.log`

### Issue: Coupon validation fails
**Solution:**
1. Check coupon code spelling
2. Verify coupon is active
3. Check expiration date
4. Verify usage limits

## Next Steps

1. Integrate with frontend application
2. Set up email notifications for subscription events
3. Configure automatic subscription renewal
4. Set up invoice generation
5. Add admin dashboard for subscription management

## Support

For issues or questions, refer to:
- `docs/phase4-subscriptions.md` - Full documentation
- Laravel logs: `storage/logs/laravel.log`
- API documentation: `/docs/api/phase4`

## Security Checklist

- [ ] Webhook signatures verified
- [ ] HTTPS enabled in production
- [ ] API rate limiting configured
- [ ] User authentication validated
- [ ] Payment gateway credentials secured
- [ ] Database backups configured
- [ ] Error logging enabled
- [ ] Test mode disabled in production

## Production Deployment

Before deploying to production:

1. Run migrations on production database
2. Seed only necessary data (skip test coupons)
3. Update `.env` with production credentials
4. Disable test mode
5. Configure webhook URLs in payment dashboards
6. Test payment flow end-to-end
7. Monitor logs for errors
8. Set up alerts for failed payments

---

**Version:** 1.0.0  
**Last Updated:** November 2024  
**Phase:** 4 - Subscriptions & Commerce

