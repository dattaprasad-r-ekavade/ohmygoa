# Email System Documentation

## Overview
The Ohmygoa platform uses Laravel's built-in mail system with queued email sending for optimal performance. All emails are beautifully designed with a consistent layout and branding.

## Configuration

### Development Environment (Mailtrap)
For development and testing, we use Mailtrap to capture all outgoing emails without actually sending them to real email addresses.

1. Sign up for a free account at [mailtrap.io](https://mailtrap.io)
2. Get your SMTP credentials from the inbox settings
3. Update your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@ohmygoa.com"
MAIL_FROM_NAME="Ohmygoa"
```

### Production Environment
For production, configure your preferred SMTP provider:

**Gmail:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_specific_password
MAIL_ENCRYPTION=tls
```

**SendGrid:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key
MAIL_ENCRYPTION=tls
```

**Amazon SES:**
```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
```

## Email Templates

### Available Email Types

1. **WelcomeEmail** - Sent when a new user registers
2. **VerificationEmail** - Email verification link
3. **PasswordResetEmail** - Password reset link
4. **PaymentReceiptEmail** - Payment confirmation and receipt
5. **NotificationEmail** - Generic notification template

### Email Layout
All emails extend the `emails.layout` base template which includes:
- Consistent branding and styling
- Responsive design for mobile devices
- Header with Ohmygoa logo
- Footer with social links and unsubscribe options
- Gradient color scheme matching the platform

### Customizing Email Templates
Email templates are located in `resources/views/emails/`. Each template extends the base layout:

```blade
@extends('emails.layout')

@section('title', 'Your Email Title')

@section('content')
    <!-- Your email content here -->
@endsection
```

## Email Service

### Using the EmailService Class

The `EmailService` class provides convenient methods for sending all types of emails:

```php
use App\Services\EmailService;

$emailService = new EmailService();

// Send welcome email
$emailService->sendWelcomeEmail($user);

// Send verification email
$emailService->sendVerificationEmail($user, $verificationUrl);

// Send password reset
$emailService->sendPasswordResetEmail($user, $resetUrl, $token);

// Send payment receipt
$emailService->sendPaymentReceiptEmail($payment);

// Send custom notification
$emailService->sendNotificationEmail(
    $user,
    'Your Custom Title',
    'Your message content',
    'https://example.com/action',
    'Action Button Text'
);
```

### Available Methods

#### User Account Emails
- `sendWelcomeEmail(User $user)` - Welcome new users
- `sendVerificationEmail(User $user, string $url)` - Email verification
- `sendPasswordResetEmail(User $user, string $url, string $token)` - Password reset

#### Business Emails
- `sendListingApprovedEmail(User $user, $listing)` - Listing approved notification
- `sendListingRejectedEmail(User $user, $listing, string $reason)` - Listing rejection notice
- `sendNewReviewEmail(User $owner, $review, $listing)` - New review notification
- `sendEnquiryNotificationEmail(User $owner, $enquiry, $listing)` - New enquiry alert

#### Payment & Subscription Emails
- `sendPaymentReceiptEmail(Payment $payment)` - Payment confirmation
- `sendSubscriptionActivatedEmail(User $user)` - Premium subscription activated
- `sendSubscriptionExpiringEmail(User $user, int $daysLeft)` - Subscription expiration reminder
- `sendPayoutProcessedEmail(User $user, $payout)` - Payout confirmation

#### Generic Notifications
- `sendNotificationEmail(User $user, string $title, string $message, ?string $actionUrl, ?string $actionText)` - Custom notifications

## Queue Configuration

### Setting Up Queue Workers

Emails implement `ShouldQueue` interface and are automatically queued for async sending:

1. Make sure queue is configured in `.env`:
```env
QUEUE_CONNECTION=database
```

2. Run migrations for queue tables (already included):
```bash
php artisan migrate
```

3. Start the queue worker:
```bash
php artisan queue:work
```

4. For production, use Supervisor to keep queue workers running:

**Supervisor Config** (`/etc/supervisor/conf.d/ohmygoa-worker.conf`):
```ini
[program:ohmygoa-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/ohmygoa/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/ohmygoa/storage/logs/worker.log
stopwaitsecs=3600
```

### Monitoring Queue

View failed jobs:
```bash
php artisan queue:failed
```

Retry failed jobs:
```bash
php artisan queue:retry all
```

Clear failed jobs:
```bash
php artisan queue:flush
```

## Testing Emails

### Artisan Tinker
Test emails quickly using tinker:

```bash
php artisan tinker
```

```php
$user = User::first();
$emailService = new App\Services\EmailService();
$emailService->sendWelcomeEmail($user);
```

### Using Mailtrap
All development emails are captured in Mailtrap inbox where you can:
- Preview emails in different email clients
- Check spam score
- View HTML and plain text versions
- Test responsive design

### Mail Preview
Create a test route to preview emails in browser:

```php
Route::get('/email-preview', function() {
    $user = User::first();
    return new App\Mail\WelcomeEmail($user);
});
```

## Best Practices

1. **Always Queue Emails**: Use `ShouldQueue` interface to prevent blocking requests
2. **Handle Failures Gracefully**: Log errors but don't break user flows
3. **Test Before Deploying**: Always test emails in Mailtrap before production
4. **Keep It Simple**: Avoid complex HTML/CSS that breaks in email clients
5. **Mobile First**: Always test on mobile devices
6. **Unsubscribe Links**: Include unsubscribe option in marketing emails
7. **Rate Limiting**: Be mindful of SMTP provider rate limits
8. **Personalization**: Always address users by name when possible

## Troubleshooting

### Emails Not Sending

1. Check queue is running:
```bash
php artisan queue:work
```

2. Check logs:
```bash
tail -f storage/logs/laravel.log
```

3. Verify SMTP credentials in `.env`

4. Test SMTP connection:
```bash
php artisan tinker
Mail::raw('Test email', function($msg) {
    $msg->to('test@example.com')->subject('Test');
});
```

### Emails Going to Spam

1. Use authenticated SMTP provider (SendGrid, Mailgun, SES)
2. Set up SPF, DKIM, and DMARC records for your domain
3. Avoid spam trigger words in subject lines
4. Maintain good sender reputation
5. Include unsubscribe link
6. Use consistent "From" address

### Queue Not Processing

1. Restart queue worker:
```bash
php artisan queue:restart
```

2. Check for stuck jobs:
```bash
php artisan queue:failed
```

3. Increase timeout in supervisor config

## Security Considerations

1. **Never expose email content** in logs (personal data)
2. **Rate limit** password reset and verification emails
3. **Expire verification links** after 24 hours
4. **Use secure tokens** for password resets
5. **Validate email addresses** before sending
6. **Encrypt sensitive data** in queued jobs

## Future Enhancements

- [ ] Email templates in database for easy customization
- [ ] User email preferences management
- [ ] Newsletter subscription system
- [ ] Email analytics and tracking
- [ ] A/B testing for email templates
- [ ] Localization support for multiple languages
- [ ] SMS notifications integration
- [ ] Push notifications for mobile app
