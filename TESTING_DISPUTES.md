# 🧪 Testing Your Dispute System

## Quick Test Commands

### 1. Test Email Notifications
```bash
# Test with any paid booking
php artisan dispute:test

# Test with specific booking
php artisan dispute:test BOOKING123
```
This creates a fake dispute and sends the actual email notification.

### 2. Simulate Webhook Data
```bash
# See what webhook data looks like
php artisan dispute:webhook

# For specific booking
php artisan dispute:webhook BOOKING123
```

## Real Stripe Testing Methods

### Method 1: Stripe Test Mode (Safest)
1. **Use Stripe test cards** to create real test payments
2. **Create disputes in Stripe Dashboard**:
   - Payments → Select test payment → More → Create dispute
3. **Your webhooks will fire** with real test data

### Method 2: Stripe CLI (Advanced)
```bash
# Install Stripe CLI
# Forward webhooks to your local server
stripe listen --forward-to https://yourdomain.com/webhooks/stripe

# In another terminal, trigger test events
stripe trigger charge.dispute.created
```

### Method 3: Production Monitoring (Live)
⚠️ **Only after thorough testing!**
- Monitor logs: `tail -f storage/logs/laravel.log`
- Check dispute emails in real-time
- Watch database for new dispute records

## Test Checklist

### ✅ Pre-Testing Setup
- [ ] Configure `OWNER_EMAIL` in `.env`
- [ ] Test email sending works: `php artisan queue:work`
- [ ] Verify webhooks are added in Stripe Dashboard
- [ ] Run: `php artisan config:cache`

### ✅ Email Testing
- [ ] Run: `php artisan dispute:test`
- [ ] Check email inbox for dispute notification
- [ ] Verify email formatting and links
- [ ] Check database for dispute record

### ✅ Webhook Testing
- [ ] Create test payment in Stripe
- [ ] Create dispute on test payment
- [ ] Check logs: `tail -f storage/logs/laravel.log`
- [ ] Verify dispute record created
- [ ] Confirm email sent

### ✅ Edge Cases
- [ ] Test with missing booking (should log gracefully)
- [ ] Test duplicate disputes (should ignore)
- [ ] Test email failures (should log but not crash)

## Monitoring Commands

```bash
# Watch logs in real-time
tail -f storage/logs/laravel.log | grep -i dispute

# Check recent disputes
php artisan tinker
>>> BookingDispute::latest()->get()

# Check webhook health
>>> \App\Models\PaymentFailure::latest()->take(5)->get()
```

## Cleanup After Testing

```bash
# Remove test disputes
php artisan tinker
>>> BookingDispute::where('stripe_dispute_id', 'LIKE', 'dp_test_%')->delete()
```

## 🚨 Emergency Monitoring

If disputes aren't being captured:

1. **Check webhook endpoint** is accessible
2. **Verify webhook signing secret** in config
3. **Check Laravel logs** for webhook errors
4. **Test webhook URL** manually with Stripe CLI
5. **Ensure queue workers** are running for emails

## Real-World Usage

Once live, you'll automatically receive emails when:
- ❌ Customer disputes a payment
- 📝 Dispute status changes
- ✅ Dispute is resolved (won/lost)

Each email contains direct links to respond in Stripe Dashboard! 🛡️
