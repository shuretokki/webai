# Xendit Payment Gateway Migration Plan

**Date Created:** 2025-12-15
**Status:** Planned
**Priority:** High (Required for monetization)

---

## Why Xendit?

### Problem with Stripe
- ❌ Requires merchant account in supported countries (US, EU, UK, etc.)
- ❌ Developer is in Indonesia - cannot register for payouts
- ❌ No support for Indonesian bank accounts

### Why Xendit
- ✅ Supports Indonesian merchants
- ✅ Accepts Indonesian bank accounts for payouts
- ✅ Supports IDR (Indonesian Rupiah) currency
- ✅ Local payment methods: GoPay, OVO, DANA, ShopeePay, Bank Transfer
- ✅ Also supports credit cards (Visa/Mastercard)
- ✅ Well-documented API and PHP SDK
- ✅ Popular in Southeast Asia

---

## Current Implementation (Stripe/Cashier)

### Files to Replace/Update

**Backend:**
- `composer.json` - Remove Cashier, add Xendit SDK
- `config/cashier.php` - Replace with `config/xendit.php`
- `app/Models/User.php` - Remove Billable trait, add Xendit methods
- `app/Models/Subscription.php` - Update for Xendit structure
- `app/Http/Controllers/SubscriptionController.php` - Rewrite for Xendit API
- `app/Http/Controllers/WebhookController.php` - Handle Xendit webhooks
- `routes/web.php` - Update subscription routes
- `config/pricing.php` - Update pricing structure (IDR focus)
- `.env.example` - Replace Stripe vars with Xendit vars

**Frontend:**
- `resources/js/pages/subscription/Index.vue` - Update checkout flow

**Database:**
- Keep existing migrations (subscriptions table compatible)
- May need to add Xendit-specific fields

**Tests:**
- `tests/Feature/SubscriptionTest.php` - Update for Xendit

---

## Migration Steps

### Phase 1: Setup & Dependencies (30 mins)

1. **Install Xendit SDK:**
   ```bash
   composer remove laravel/cashier
   composer require xendit/xendit-php
   ```

2. **Create Xendit config:**
   ```bash
   touch config/xendit.php
   ```

3. **Update .env variables:**
   ```env
   # Remove Stripe
   # STRIPE_KEY=
   # STRIPE_SECRET=
   # STRIPE_WEBHOOK_SECRET=

   # Add Xendit
   XENDIT_SECRET_KEY=
   XENDIT_PUBLIC_KEY=
   XENDIT_WEBHOOK_TOKEN=
   ```

### Phase 2: Backend Implementation (3-4 hours)

4. **Create Xendit service class:**
   ```bash
   php artisan make:class Services/XenditService
   ```
   - Handle API calls
   - Create invoices
   - Manage recurring payments
   - Customer management

5. **Update User model:**
   - Remove `Billable` trait
   - Add `xendit_customer_id` to fillable
   - Add helper methods: `createXenditCustomer()`, `hasActiveSubscription()`

6. **Rewrite SubscriptionController:**
   - `index()` - Show plans (keep mostly same)
   - `checkout()` - Create Xendit invoice
   - `success()` - Handle payment callback
   - `cancel()` - Cancel recurring payment
   - `resume()` - Reactivate subscription

7. **Rewrite WebhookController:**
   - Verify Xendit webhook signature
   - Handle invoice paid
   - Handle invoice expired
   - Handle recurring payment failed
   - Update subscription_tier on events

8. **Update routes:**
   - Add `/xendit/webhook` route (no CSRF)
   - Keep subscription management routes

### Phase 3: Database Updates (30 mins)

9. **Create migration for Xendit fields:**
   ```bash
   php artisan make:migration add_xendit_fields_to_users
   ```

   Add columns:
   - `xendit_customer_id` (string, nullable)
   - `xendit_invoice_id` (string, nullable)

10. **Update subscriptions table if needed:**
    - May need `xendit_recurring_id` column

### Phase 4: Frontend Updates (2 hours)

11. **Update subscription/Index.vue:**
    - Keep currency selector (USD optional, IDR primary)
    - Update checkout to create Xendit invoice
    - Redirect to Xendit hosted payment page
    - Handle return from Xendit

12. **Add payment method selection:**
    - Credit Card
    - Bank Transfer
    - GoPay
    - OVO
    - DANA
    - ShopeePay

### Phase 5: Testing (2 hours)

13. **Update tests:**
    - Mock Xendit API calls
    - Test invoice creation
    - Test webhook handling
    - Test subscription status updates

14. **Manual testing:**
    - Create test Xendit account
    - Use test payment methods
    - Verify webhook delivery
    - Test full subscription flow

### Phase 6: Configuration (1 hour)

15. **Xendit Dashboard Setup:**
    - Create production account
    - Get API keys
    - Configure webhook URL
    - Set up recurring payment plans
    - Create products/prices

16. **Documentation:**
    - Update README with Xendit setup
    - Document webhook configuration
    - Add troubleshooting guide

---

## Xendit API Basics

### Creating an Invoice
```php
use Xendit\Invoice;

Invoice::create([
    'external_id' => 'subscription_' . $user->id . '_' . time(),
    'amount' => 149000, // IDR 149,000 (Plus plan)
    'description' => 'WebAI Plus Subscription - Monthly',
    'invoice_duration' => 86400, // 24 hours
    'customer' => [
        'given_names' => $user->name,
        'email' => $user->email,
    ],
    'success_redirect_url' => route('subscription.success'),
    'failure_redirect_url' => route('subscription.index'),
]);
```

### Handling Webhooks
```php
// Verify signature
$webhookToken = request()->header('X-Callback-Token');
if ($webhookToken !== config('xendit.webhook_token')) {
    abort(403);
}

// Process event
$event = request()->all();
if ($event['status'] === 'PAID') {
    // Update user subscription
}
```

### Creating Recurring Payments
```php
use Xendit\RecurringPayment;

RecurringPayment::create([
    'external_id' => 'recurring_' . $user->id,
    'amount' => 149000,
    'payer_email' => $user->email,
    'description' => 'WebAI Plus Subscription',
    'interval' => 'MONTH',
    'interval_count' => 1,
]);
```

---

## Pricing Strategy with Xendit

### Recommended Pricing (IDR Focus)

**Free Tier:**
- 100 messages/month
- 10K tokens
- IDR 0

**Plus Tier:**
- 500 messages/month
- 1M tokens
- **IDR 99,000/month** (~$6.50 USD)

**Enterprise Tier:**
- Unlimited messages
- Unlimited tokens
- **IDR 499,000/month** (~$32 USD)

**Why IDR pricing:**
- Target market is Indonesia
- Easier conversion for local users
- Competitive with local SaaS pricing
- Xendit handles IDR natively

---

## Testing Resources

### Xendit Test Mode
- **Test Secret Key:** Get from Xendit Dashboard
- **Test Public Key:** Get from Xendit Dashboard
- **Webhook Testing:** Use ngrok or expose local dev server

### Test Payment Methods
- **Credit Card:** Use test card numbers from Xendit docs
- **E-Wallets:** Test accounts provided by Xendit
- **Bank Transfer:** Simulated via dashboard

### Test Webhook Events
Can manually trigger from Xendit Dashboard:
- Invoice paid
- Invoice expired
- Recurring payment created
- Recurring payment failed

---

## Risk Assessment

### Low Risk
- ✅ Architecture stays mostly the same
- ✅ Frontend UI can stay mostly same
- ✅ Tests provide safety net

### Medium Risk
- ⚠️ Different webhook structure than Stripe
- ⚠️ Need to handle IDR currency formatting
- ⚠️ Different subscription model (invoice-based vs subscription object)

### Mitigation
- Keep Stripe code commented for reference
- Extensive testing before production
- Start with single payment tier to test
- Monitor webhook logs closely

---

## Timeline Estimate

**Total Time:** 2-3 days (16-24 hours)

**Breakdown:**
- Setup & dependencies: 30 mins
- Backend implementation: 3-4 hours
- Database updates: 30 mins
- Frontend updates: 2 hours
- Testing: 2 hours
- Configuration & docs: 1 hour
- Buffer for issues: 2-3 hours

**Recommended Approach:**
- Day 1: Backend (setup, service class, controllers)
- Day 2: Frontend, testing, debugging
- Day 3: Production setup, monitoring

---

## Post-Migration Checklist

- [ ] All tests passing
- [ ] Webhooks receiving correctly
- [ ] Subscription creation works
- [ ] Payment methods all functional
- [ ] Cancellation flow works
- [ ] Resume subscription works
- [ ] Email notifications sent
- [ ] Admin panel shows correct data
- [ ] Currency formatting correct (IDR)
- [ ] Mobile responsive checkout
- [ ] Error handling tested
- [ ] Logging configured
- [ ] Monitoring alerts set up
- [ ] Documentation updated
- [ ] .env.example updated

---

## References

- **Xendit Docs:** https://docs.xendit.co/
- **PHP SDK:** https://github.com/xendit/xendit-php
- **Invoice API:** https://docs.xendit.co/api-reference/invoice/
- **Recurring Payments:** https://docs.xendit.co/recurring-payments/
- **Webhooks:** https://docs.xendit.co/webhooks/
- **Test Mode:** https://dashboard.xendit.co/settings/developers

---

## Notes

- Keep Stripe code commented out for now (don't delete)
- Can re-evaluate payment gateway after launch
- Consider adding multiple payment gateways later for redundancy
- Xendit also has international payment support if needed later
