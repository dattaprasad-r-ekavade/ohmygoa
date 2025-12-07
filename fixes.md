# Comprehensive Security & Code Audit Report

**Project:** Ohmygoa - Goa's Directory & Community Platform  
**Date:** December 7, 2025  
**Auditor:** Senior Developer Review  
**Status:** CRITICAL ISSUES FOUND

---

## 🚨 Executive Summary

The **Ohmygoa** project is feature-rich and well-structured but contains **critical security vulnerabilities** that make it unsafe for production in its current state. Immediate action is required to secure the application before any public deployment.

**Key Findings:**
- **Critical:** Exposed encryption keys and database credentials in the repository.
- **Critical:** Missing authorization checks allowing unauthorized data deletion.
- **High:** Potential XSS vulnerabilities due to flawed input sanitization.
- **High:** Insecure backup and session configurations.

---

## 🔴 CRITICAL ISSUES (Must Fix Before Production)

### 1. **EXPOSED ENVIRONMENT FILES IN REPOSITORY**
**Severity:** CRITICAL 🔴  
**Location:** Root directory  
**Risk Level:** MAXIMUM

#### Problem:
- `.env` file with real application key is present in the repository
- `.env.production` file with production placeholders is tracked
- Both files should NEVER be committed to Git

#### Current State:
```
.env (1202 bytes, Modified: 30-11-2025)
  - Contains: APP_KEY=base64:ZvgiHY6RJKCEEkXEDoB8jzGoL7pP56IMMZPq8WQwfWE=
  - This is a REAL encryption key being exposed

.env.production (2406 bytes, Modified: 01-12-2025)
  - Contains placeholder credentials but should not be in repo
```

#### Impact:
- **SESSION HIJACKING**: Anyone with the APP_KEY can decrypt session data
- **COOKIE FORGERY**: Attackers can create valid session cookies
- **DATA BREACH**: Encrypted data can be decrypted by unauthorized parties
- **TOTAL SYSTEM COMPROMISE**: If this key is used in production

#### Fix Required:
```bash
# 1. IMMEDIATELY remove from Git history
git rm --cached .env
git rm --cached .env.production
git commit -m "Security: Remove exposed environment files"

# 2. Regenerate APP_KEY
php artisan key:generate

# 3. Verify .gitignore contains:
.env
.env.*
!.env.example

# 4. If production is using this key, rotate it IMMEDIATELY
# 5. Invalidate all existing sessions
php artisan session:flush
```

---

### 2. **HARDCODED CREDENTIALS IN DEPLOYMENT SCRIPTS**
**Severity:** CRITICAL 🔴  
**Location:** `backup.sh`, `deploy.sh`  
**Risk Level:** HIGH

#### Problem:
Database credentials are hardcoded in backup script:

```bash
# Line 26 in backup.sh
DB_PASSWORD="STRONG_PASSWORD_HERE"  # Should be loaded from secure location
```

#### Impact:
- Credentials visible in Git history
- Anyone with repository access can see production database password
- Automated backups may fail if using placeholder password

#### Fix Required:
```bash
# backup.sh - Use secure credential loading
# Load from environment or encrypted config
if [ -f "$PROJECT_DIR/.env" ]; then
    source <(grep -E '^(DB_|APP_)' "$PROJECT_DIR/.env" | sed 's/^/export /')
fi

# Or use a separate secured credentials file
if [ -f "/secure/credentials.sh" ]; then
    source "/secure/credentials.sh"
fi

# Never hardcode passwords
DB_PASSWORD="${DB_PASSWORD:-$(cat /secure/db_password.txt)}"
```

---

### 3. **MISSING AUTHORIZATION CHECKS ON DELETE OPERATIONS**
**Severity:** CRITICAL 🔴  
**Location:** Multiple Controllers  
**Risk Level:** HIGH

#### Problem:
Several delete operations lack proper authorization checks. Examples:

```php
// app/Http/Controllers/BusinessListingController.php:234
public function destroy(BusinessListing $listing)
{
    Gate::authorize('update', $listing); // Uses 'update' instead of 'delete'
    $listing->delete();
    // ...
}

// app/Http/Controllers/ProfileController.php:53
public function destroy(Request $request): RedirectResponse
{
    $request->validateWithBag('userDeletion', [
        'password' => ['required', 'current_password'],
    ]);
    
    $user = $request->user();
    Auth::logout();
    $user->delete(); // Deletes immediately without checking dependencies
    // ...
}
```

#### Impact:
- Users might delete resources owned by others
- No soft-delete implementation for critical data
- Cascade deletion issues not handled
- Data loss without proper audit trail

#### Fix Required:
```php
// 1. Use proper authorization
public function destroy(BusinessListing $listing)
{
    Gate::authorize('delete', $listing); // Use 'delete' policy
    
    // 2. Check dependencies
    if ($listing->hasActiveBookings() || $listing->hasUnresolvedEnquiries()) {
        return back()->with('error', 'Cannot delete listing with active bookings');
    }
    
    // 3. Use soft delete
    $listing->delete();
    
    // 4. Log the action
    Log::info('Listing deleted', [
        'listing_id' => $listing->id,
        'user_id' => auth()->id(),
        'ip' => request()->ip()
    ]);
    
    return redirect()->route('business.listings.index')
        ->with('success', 'Listing deleted successfully');
}
```

---

## 🟠 HIGH PRIORITY ISSUES

### 4. **SQL INJECTION VULNERABILITIES (Potential)**
**Severity:** HIGH 🟠  
**Location:** Multiple Admin Controllers  
**Risk Level:** MEDIUM-HIGH

#### Problem:
While using Eloquent ORM (which provides SQL injection protection), there are multiple raw SQL queries that could be vulnerable:

```php
// app/Http/Controllers/Admin/AdminController.php
$userRegistrations = User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
$monthlyRevenue = Payment::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
->select('categories.name', DB::raw('COUNT(*) as count'))
```

#### Fix Required:
Always use parameter binding for raw queries:
```php
$userRegistrations = User::selectRaw(
    'DATE_FORMAT(created_at, ?) as month, COUNT(*) as count',
    [$format]
)->get();
```

---

### 5. **INSUFFICIENT INPUT SANITIZATION**
**Severity:** HIGH 🟠  
**Location:** `app/Http/Middleware/SanitizeInput.php`  
**Risk Level:** MEDIUM-HIGH

#### Problem:
The sanitization middleware has a flawed approach:
1. **XSS BYPASS**: If user input contains HTML tags, it skips sanitization.
2. **Logic Flaw**: Attacker can inject `<script>` and it will be treated as "rich text".

#### Fix Required:
Use HTML Purifier for rich text fields and strictly escape all other fields. Do not rely on `strip_tags` check to determine if content is safe.

---

### 6. **MISSING RATE LIMITING ON CRITICAL ENDPOINTS**
**Severity:** HIGH 🟠  
**Location:** API Routes, Authentication  
**Risk Level:** MEDIUM

#### Problem:
Authentication endpoints (`/login`, `/register`) lack strict rate limiting, making them vulnerable to brute force and credential stuffing attacks.

#### Fix Required:
Apply strict throttling middleware to auth routes:
```php
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/register', [AuthApiController::class, 'register']);
    Route::post('/login', [AuthApiController::class, 'login']);
});
```

---

### 7. **MISSING CSRF PROTECTION VERIFICATION**
**Severity:** HIGH 🟠  
**Location:** Route definitions, API endpoints  
**Risk Level:** MEDIUM-HIGH

#### Problem:
API routes and AJAX calls may lack proper CSRF token verification.

#### Fix Required:
Ensure `VerifyCsrfToken` middleware is active for web routes and that all AJAX requests include the `X-CSRF-TOKEN` header.

---

### 8. **DATABASE BACKUP SECURITY VULNERABILITIES**
**Severity:** HIGH 🟠  
**Location:** `backup.sh`, deployment configuration  
**Risk Level:** MEDIUM

#### Problem:
- Database password visible in process list during backup.
- Backups include sensitive `.env` files.
- Backups are stored unencrypted.

#### Fix Required:
- Use MySQL config file for credentials.
- Encrypt backup files.
- Exclude `.env` files from backups.

---

### 9. **SESSION SECURITY CONFIGURATION ISSUES**
**Severity:** HIGH 🟠  
**Location:** `config/session.php`, `.env`  
**Risk Level:** MEDIUM

#### Problem:
Sessions are not encrypted and cookies lack `Secure`, `HttpOnly`, and `SameSite` attributes.

#### Fix Required:
Update `.env` and `config/session.php` to enable encryption and secure cookie attributes.

---

## 🟡 MEDIUM & LOW PRIORITY ISSUES

### 10. **MISSING CORS CONFIGURATION**
**Severity:** MEDIUM 🟡  
**Location:** No CORS config file exists  
**Risk Level:** MEDIUM

#### Problem:
No `config/cors.php` file found, allowing API access from any origin.

#### Fix Required:
Install `fruitcake/laravel-cors` and configure allowed origins.

---

### 11. **INADEQUATE ERROR HANDLING & LOGGING**
**Severity:** MEDIUM 🟡  
**Location:** Controllers, Exception Handler  
**Risk Level:** MEDIUM

#### Problem:
Generic try-catch blocks hide critical errors and lack logging context.

#### Fix Required:
Implement proper exception handling with logging context (user ID, IP, URL).

---

### 12. **MISSING DATABASE TRANSACTION MANAGEMENT**
**Severity:** MEDIUM 🟡  
**Location:** Multiple controllers  
**Risk Level:** MEDIUM

#### Problem:
Operations involving multiple database writes (e.g., creating listing + images) lack transaction management, leading to potential data inconsistency.

#### Fix Required:
Wrap multi-step operations in `DB::beginTransaction()` and `DB::commit()`.

---

### 13. **INSECURE CONTENT SECURITY POLICY (CSP)**
**Severity:** MEDIUM 🟡  
**Location:** `nginx-ohmygoa.conf`  
**Risk Level:** MEDIUM

#### Problem:
Nginx config enables `unsafe-inline` and `unsafe-eval`, weakening XSS protection.

#### Fix Required:
Remove unsafe directives and use nonces or hashes for inline scripts.

---

### 14. **LOGGING CONFIGURATION & ROTATION**
**Severity:** MEDIUM 🟡  
**Location:** `config/logging.php`, `supervisor-ohmygoa.conf`  
**Risk Level:** MEDIUM

#### Problem:
Supervisor logs lack rotation settings, risking disk space exhaustion.

#### Fix Required:
Configure log rotation in Supervisor and use `daily` log channel in production.

---

### 15. **HARDCODED PATHS & VERSIONS**
**Severity:** MEDIUM 🟡  
**Location:** `supervisor-ohmygoa.conf`, `crontab-ohmygoa.txt`, `nginx-ohmygoa.conf`  
**Risk Level:** LOW-MEDIUM

#### Problem:
Configuration files contain hardcoded PHP versions (`php8.2`) and paths.

#### Fix Required:
Use symlinks or generic paths to improve maintainability.

---

### 16. **REDUNDANT & BRUTE-FORCE CRON JOBS**
**Severity:** LOW 🟢  
**Location:** `crontab-ohmygoa.txt`  
**Risk Level:** LOW

#### Problem:
Redundant backup tasks and brute-force worker restarts in crontab.

#### Fix Required:
Centralize scheduling in Laravel's `Console/Kernel.php` and use `queue:restart` for graceful worker restarts.

---

### 17. **FILE UPLOAD VALIDATION IMPROVEMENT**
**Severity:** LOW 🟢
**Location:** `app/Http/Controllers/MediaController.php`
**Risk Level:** LOW

#### Problem:
File validation relies on `getMimeType()` which can be spoofed, although `file` rule is also used.

#### Fix Required:
Use the `mimes:jpg,png,pdf...` validation rule in the request validation for robust type checking.

---

## ❗ Implementation & Feature Gaps Identified

### 18. **SERVICE EXPERT VIEWS MISSING**
**Severity:** HIGH 🟠  
**Location:** `routes/web.php`, `resources/views`  
**Risk Level:** HIGH

#### Problem:
Multiple routes (public, business, and booking) render Blade views under the `service-experts.*` namespace (e.g., `return view('service-experts.index')`), but the `resources/views` tree does not contain a `service-experts/` directory at all. Any attempt to load these pages results in a view-not-found error, so the entire Service Expert feature is inaccessible.

#### Fix Required:
Create the missing Blade templates (`index`, `show`, `create`, `edit`, `book`, etc.) or adjust the controllers to point to existing view paths. Add smoke tests to ensure every defined route resolves a view.

### 19. **SERVICE EXPERT SCHEMA/CONTROLLER MISMATCH**
**Severity:** HIGH 🟠  
**Location:** `app/Http/Controllers/ServiceExpertController.php`, `database/migrations/2024_11_30_000021_create_service_experts_table.php`  
**Risk Level:** HIGH

#### Problem:
The controller validates and writes fields such as `years_of_experience`, `contact_phone`, `contact_email`, `skills`, `address`, and `availability`, but the migration/model define different column names (`years_experience`, `phone`, `email`) or lack the columns entirely (`skills`, `address`, `availability`). Because of this mismatch, mass assignment silently drops user input, filters like `->where('years_of_experience', ...)` fail, and data integrity cannot be guaranteed.

#### Fix Required:
Align the migration, model `$fillable`, and controller validation so they use identical field names. Either rename the database columns via a migration (e.g., `years_experience` → `years_of_experience`, add `address`, `skills`, etc.) or update the controller to use the existing schema. Add feature tests to cover create/update flows.

### 20. **MISSING VIEW-COUNT TRAIT & INVALID SORTING COLUMNS**
**Severity:** HIGH 🟠  
**Location:** `app/Http/Controllers/ServiceExpertController.php`, `app/Models/ServiceExpert.php`  
**Risk Level:** MEDIUM-HIGH

#### Problem:
`ServiceExpertController::show()` calls `$expert->incrementViewCount()` and the listing endpoint orders by `views_count` and `years_of_experience`, but the `ServiceExpert` model does not use the `HasViewCount` trait and the `service_experts` table has no `views_count` or `years_of_experience` columns. These calls will trigger `BadMethodCallException` or SQL errors when the routes are hit.

#### Fix Required:
Either add the missing columns/trait (e.g., include `HasViewCount` on the model and add `views_count` + `years_of_experience` columns) or remove the references and adjust the sorting logic. Add automated tests that hit the index/show routes to catch regressions.

### 21. **SERVICE BOOKING PAYLOAD DOES NOT MATCH MODEL**
**Severity:** HIGH 🟠  
**Location:** `app/Http/Controllers/ServiceExpertController.php`, `app/Models/ServiceBooking.php`  
**Risk Level:** HIGH

#### Problem:
`storeBooking()` validates `service_date`, `service_time`, `customer_name`, `customer_phone`, and `customer_email`, but the `ServiceBooking` model expects `preferred_date`, `preferred_time`, `contact_name`, `contact_phone`, and `contact_email`. None of the validated keys line up with the `$fillable` array, so mass assignment produces empty rows and booking creation fails.

#### Fix Required:
Standardize the booking field names between the controller, model, and database migration (rename columns or map the validated data before `ServiceBooking::create()`). Write a feature test that submits the booking form to verify the record persists with the correct columns.

---

## 📊 Summary of Issues

| Severity | Count | Estimated Fix Time |
|----------|-------|-------------------|
| **CRITICAL** | 3 | 5.5 hours |
| **HIGH** | 6 | 10 hours |
| **MEDIUM** | 6 | 11 hours |
| **LOW** | 2 | 1 hour |
| **TOTAL** | **17** | **~27.5 hours** |

**Recommendation:** Address all CRITICAL issues immediately before any further development or deployment.
