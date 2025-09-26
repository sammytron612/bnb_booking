# 🚨 CSP ROLLBACK & EMERGENCY PROCEDURES

## 🔥 EMERGENCY DISABLE (30 SECONDS)

If CSP breaks your site, choose ONE of these options:

### Option 1: Environment Variable (FASTEST)
```bash
# Add to .env file:
CSP_ENABLED=false

# Then clear cache:
php artisan config:clear
```

### Option 2: Config File Disable
```php
// In config/security.php, change line 15:
'enabled' => false, // Was: env('CSP_ENABLED', false)
```

### Option 3: Remove Middleware (NUCLEAR)
```php
// In bootstrap/app.php, comment out these lines:
// $middleware->web(append: [
//     \App\Http\Middleware\ContentSecurityPolicy::class,
// ]);
```

## 📊 SAFE TESTING PROCEDURE

### Step 1: Enable Report-Only Mode (SAFE)
```bash
# In .env:
CSP_ENABLED=true
CSP_REPORT_ONLY=true  # This won't break anything!
```

### Step 2: Test Booking Flow
1. ✅ Open booking form
2. ✅ Select dates  
3. ✅ Fill guest details
4. ✅ Submit booking
5. ✅ Stripe checkout loads
6. ✅ Payment success page

### Step 3: Check Browser Console
- Open DevTools (F12)
- Look for CSP violations (won't block in report-only mode)
- Note any errors for fixing

### Step 4: Enable Full CSP (After Testing)
```bash
# In .env:
CSP_ENABLED=true
CSP_REPORT_ONLY=false  # Now it will actively block
```

## 🛠️ TROUBLESHOOTING COMMON ISSUES

### Issue: Livewire Not Working
**Fix**: Add to CSP script-src:
```
'unsafe-eval' 'unsafe-inline'
```

### Issue: Stripe Checkout Broken
**Fix**: Ensure these domains in CSP:
```
script-src: https://js.stripe.com https://checkout.stripe.com
frame-src: https://js.stripe.com https://checkout.stripe.com  
connect-src: https://api.stripe.com
```

### Issue: Custom JavaScript Blocked
**Fix**: Either:
1. Move JS to external file and add 'self'
2. Add specific script hashes
3. Temporarily add 'unsafe-inline' (less secure)

### Issue: Google Fonts Not Loading  
**Fix**: Ensure these in CSP:
```
style-src: https://fonts.googleapis.com
font-src: https://fonts.gstatic.com
```

## 🔧 CONFIGURATION OPTIONS

### Current Settings Status:
- **CSP Enabled**: `false` (SAFE - Currently disabled)
- **Report Only**: `true` (SAFE - Won't block anything when enabled)
- **Emergency Disable**: Available via `SECURITY_EMERGENCY_DISABLE=true`

### Environment Variables:
```bash
CSP_ENABLED=false              # Main CSP toggle
CSP_REPORT_ONLY=true           # Safe mode toggle
SECURITY_EMERGENCY_DISABLE=false  # Nuclear option
```

## 📋 ROLLBACK CHECKLIST

If you need to rollback:

- [ ] Set `CSP_ENABLED=false` in .env
- [ ] Run `php artisan config:clear`  
- [ ] Test booking form works
- [ ] Test Stripe checkout works
- [ ] Verify no console errors
- [ ] Check payment flow end-to-end

## 🚀 PROGRESSIVE ENABLEMENT PLAN

### Phase 1: Report-Only Testing (SAFE)
```bash
CSP_ENABLED=true
CSP_REPORT_ONLY=true
```
- Monitor browser console for violations
- Fix any issues before Phase 2
- No user impact

### Phase 2: Limited Enforcement  
```bash
CSP_ENABLED=true
CSP_REPORT_ONLY=false
```
- Apply to single page first
- Monitor for issues
- Gradual rollout

### Phase 3: Full Deployment
- Apply to all pages
- Monitor error logs  
- Fine-tune policy as needed

## 🛡️ SECURITY BENEFITS (When Enabled)

- ✅ **XSS Attack Prevention**: Blocks malicious scripts
- ✅ **Code Injection Protection**: Prevents eval() attacks  
- ✅ **Clickjacking Prevention**: Frame protection
- ✅ **Data Exfiltration Prevention**: Controls external requests
- ✅ **Stripe Security**: Ensures only official Stripe domains

## 📞 SUPPORT CONTACTS

**If CSP breaks critical functionality:**
1. Follow emergency disable steps above
2. Document the specific error
3. Test with `CSP_REPORT_ONLY=true` to identify issue
4. Adjust CSP policy in `config/security.php`

**Remember**: CSP is currently **DISABLED** and **SAFE** by default!
