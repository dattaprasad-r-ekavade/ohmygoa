# Security Guidelines - Ohmygoa Project

## 🔒 Security Measures Implemented

### Date: November 30, 2025
**Status:** Initial security audit completed

---

## ✅ Completed Security Actions

### 1. **Removed Exposed API Keys**
- ✅ Removed Google Maps API key from `admin/admin-setting.html`
- ✅ Removed Stripe test API keys from `admin/admin-payment-credentials.html`
- ✅ Removed Razorpay test API keys from `admin/admin-payment-credentials.html`

**Commit:** `[Security] Remove exposed API keys - Google Maps, Stripe, Razorpay test keys`

### 2. **Environment Configuration**
- ✅ `.env` file is in `.gitignore`
- ✅ `.env.backup` and `.env.production` are excluded
- ✅ `.env.example` contains only placeholder values

---

## 🔐 Best Practices for Development

### Never Commit These:
- ❌ `.env` files with real credentials
- ❌ API keys, tokens, or secrets
- ❌ Database passwords
- ❌ Payment gateway live keys
- ❌ Email service passwords
- ❌ AWS access keys
- ❌ OAuth client secrets

### Always Use Environment Variables:
```php
// ✅ Good - Using environment variables
$apiKey = env('GOOGLE_MAPS_API_KEY');
$stripeKey = env('STRIPE_SECRET_KEY');
$razorpayKey = env('RAZORPAY_KEY_ID');

// ❌ Bad - Hardcoded secrets
$apiKey = 'AIzaSyD0FaiydKhFr2FySgBKU1js-ZWX2P-3e88';
```

---

## 📋 Environment Variables Checklist

### Required for Production:

#### Application
```env
APP_NAME=Ohmygoa
APP_ENV=production
APP_KEY=base64:GENERATE_NEW_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

#### Database
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ohmygoa_prod
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password
```

#### Google Services
```env
GOOGLE_MAPS_API_KEY=your_google_maps_key
GOOGLE_ANALYTICS_ID=UA-XXXXXXXXX-X
```

#### Payment Gateways
```env
# Razorpay (Production)
RAZORPAY_KEY_ID=rzp_live_XXXXXXXXXXXX
RAZORPAY_KEY_SECRET=your_secret_key

# Stripe (if used)
STRIPE_KEY=pk_live_XXXXXXXXXXXX
STRIPE_SECRET=sk_live_XXXXXXXXXXXX
```

#### Email Service
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@ohmygoa.com
MAIL_FROM_NAME="${APP_NAME}"
```

#### AWS (if used for file storage)
```env
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=ap-south-1
AWS_BUCKET=ohmygoa-uploads
```

---

## 🚨 What to Do If Secrets Are Exposed

### Immediate Actions:

1. **Revoke the Exposed Credentials**
   - Immediately invalidate/regenerate all exposed API keys
   - Change passwords
   - Rotate secrets

2. **Remove from Git History**
   ```bash
   # Install BFG Repo-Cleaner
   # Download from: https://rtyley.github.io/bfg-repo-cleaner/
   
   # Remove passwords from history
   bfg --replace-text passwords.txt
   
   # Force push (WARNING: This rewrites history)
   git push --force
   ```

3. **Update Environment Variables**
   - Update `.env` with new credentials
   - Update production environment
   - Notify team members

4. **Monitor for Abuse**
   - Check API usage logs
   - Monitor billing for unusual activity
   - Review access logs

---

## 🛡️ Laravel Security Features

### Built-in Protections:

1. **CSRF Protection**
   - All forms automatically protected
   - Use `@csrf` directive in Blade templates

2. **SQL Injection Prevention**
   - Use Eloquent ORM or Query Builder
   - Never concatenate user input into queries

3. **XSS Protection**
   - Blade `{{ }}` auto-escapes output
   - Use `{!! !!}` only for trusted HTML

4. **Password Hashing**
   - Laravel uses bcrypt by default
   - Never store plain text passwords

5. **Rate Limiting**
   - Implement on auth routes
   - Protect API endpoints

---

## 🔍 Pre-Commit Checklist

Before every commit, verify:

- [ ] No `.env` file in commit
- [ ] No API keys in code
- [ ] No passwords in comments
- [ ] No sensitive data in logs
- [ ] All secrets use environment variables
- [ ] `.env.example` has only placeholders

---

## 🧪 Security Testing

### Regular Audits:

```bash
# Scan for secrets in codebase
git secrets --scan

# Check for vulnerable dependencies
composer audit

# Static analysis
./vendor/bin/phpstan analyse

# Security vulnerability scan
./vendor/bin/security-checker security:check
```

---

## 📞 Incident Response

### If Security Breach Occurs:

1. **Contain:** Immediately revoke compromised credentials
2. **Assess:** Identify scope and impact
3. **Eradicate:** Remove vulnerabilities
4. **Recover:** Restore services with new credentials
5. **Document:** Record incident details
6. **Learn:** Update processes to prevent recurrence

---

## 📚 Additional Resources

- [Laravel Security Best Practices](https://laravel.com/docs/security)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [GitHub Secret Scanning](https://docs.github.com/en/code-security/secret-scanning)
- [Razorpay Security Guidelines](https://razorpay.com/docs/security/)

---

## 🔄 Review Schedule

- **Weekly:** Check for dependency vulnerabilities
- **Monthly:** Review access logs and API usage
- **Quarterly:** Complete security audit
- **Annually:** Penetration testing

---

**Last Updated:** November 30, 2025  
**Next Review:** December 30, 2025  
**Maintained By:** Development Team
