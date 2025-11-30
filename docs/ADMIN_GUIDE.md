# Admin Guide - Ohmygoa Platform

## Table of Contents
1. [Admin Dashboard Overview](#admin-dashboard-overview)
2. [User Management](#user-management)
3. [Content Moderation](#content-moderation)
4. [Category & Location Management](#category--location-management)
5. [Financial Management](#financial-management)
6. [Settings & Configuration](#settings--configuration)
7. [Reports & Analytics](#reports--analytics)

---

## Admin Dashboard Overview

### Accessing Admin Panel

**URL:** `/admin`

**Default Credentials:**
- Email: admin@ohmygoa.com
- Password: password

### Dashboard Widgets

The admin dashboard displays:
- **Total Users** - Count by role (Free, Business, Admin)
- **Total Listings** - Approved, pending, rejected
- **Total Revenue** - Monthly/yearly breakdown
- **Pending Approvals** - Listings, events, jobs, products
- **Recent Activity** - Latest user actions
- **Top Performing Listings** - By views and revenue
- **System Health** - Server status, queue status

---

## User Management

### View All Users

1. Navigate to **Admin → Users**
2. View list of all registered users
3. Filter by:
   - Role (Free, Business, Admin)
   - Status (Active, Suspended, Banned)
   - Registration date
   - Subscription status

### User Actions

**View User Profile:**
- Click on any user name
- See complete profile information
- View user's listings, reviews, transactions

**Edit User:**
1. Click **Edit** next to user
2. Modify user details:
   - Name, email, phone
   - Role assignment
   - Subscription status
3. Click **Update User**

**Suspend User:**
1. Click **Suspend** next to user
2. Enter reason for suspension
3. Confirm action
4. User cannot log in while suspended
5. User receives email notification

**Ban User:**
1. Click **Ban** for permanent account closure
2. Enter reason
3. Confirm action
4. All user's listings are also removed

**Delete User:**
1. Click **Delete**
2. Confirm permanent deletion
3. All associated data is removed

### Manage Subscriptions

**Admin → Subscriptions**

- View all active subscriptions
- Extend subscription periods
- Apply discounts or refunds
- Cancel subscriptions
- View subscription history

### Verify Business Accounts

1. Admin → Users → Pending Verifications
2. Review business documents:
   - Business registration certificate
   - GST number
   - ID proof
3. Click **Approve** or **Reject**
4. Add verification notes
5. User receives email notification

---

## Content Moderation

### Listings Moderation

**Admin → Listings → Pending Approval**

**Review Process:**
1. Click on listing to view full details
2. Check for:
   - Appropriate content
   - Complete information
   - Valid contact details
   - Proper images
   - Correct category/location
3. Actions:
   - **Approve** - Listing goes live
   - **Reject** - Provide rejection reason
   - **Request Changes** - Ask user to edit

**Bulk Actions:**
- Select multiple listings
- Approve/reject in bulk
- Change categories
- Update status

**Manage Approved Listings:**
- Edit any listing
- Feature/unfeature listings
- Suspend listings temporarily
- Delete spam or inappropriate content

### Events Moderation

**Admin → Events → Pending Approval**

**Review Criteria:**
- Valid event dates
- Appropriate content
- Complete venue information
- Ticket pricing (if applicable)
- Event category accuracy

**Actions:**
- Approve/reject events
- Edit event details
- Feature events on homepage
- Cancel events
- View RSVPs and attendees

### Jobs Moderation

**Admin → Jobs → Pending Approval**

**Review Criteria:**
- Legitimate job posting
- Complete job description
- Valid salary range
- Application deadline
- Company information

**Actions:**
- Approve/reject jobs
- Edit job details
- Feature jobs
- Mark as filled
- View applications

### Products Moderation

**Admin → Products → Pending Approval**

**Review Criteria:**
- Appropriate product
- Complete description
- Valid pricing
- Stock availability
- Product images

**Actions:**
- Approve/reject products
- Edit product details
- Update stock levels
- Feature products
- Remove sold-out items

### Reviews Moderation

**Admin → Reviews**

**Actions:**
- View all reviews
- Approve/reject reviews
- Delete spam reviews
- Respond to reviews on behalf of business
- Mark reviews as helpful
- Ban users for fake reviews

### Coupons Moderation

**Admin → Coupons → Pending Approval**

**Review Criteria:**
- Valid discount offer
- Reasonable pricing
- Clear terms and conditions
- Appropriate validity period
- Business is verified

**Actions:**
- Approve/reject coupons
- Edit coupon details
- Extend validity
- Monitor coupon usage
- Disable fraudulent coupons

---

## Category & Location Management

### Manage Categories

**Admin → Categories**

**Add New Category:**
1. Click **Add Category**
2. Enter category details:
   - Name
   - Slug
   - Type (Business, Event, Job, Product, Service)
   - Parent category (if subcategory)
   - Icon
   - Display order
3. Upload category image
4. Set as active/inactive
5. Click **Create Category**

**Edit Category:**
- Update name, icon, or order
- Change parent category
- Deactivate unused categories

**Delete Category:**
- Only empty categories can be deleted
- Move listings to another category first

**Category Display Order:**
- Drag and drop to reorder
- Higher order appears first

### Manage Locations

**Admin → Locations**

**Add New Location:**
1. Click **Add Location**
2. Enter location details:
   - Name
   - Type (Country, State, District, City, Area)
   - Parent location
   - GPS coordinates
   - Display order
3. Set as popular location (optional)
4. Click **Create Location**

**Edit Location:**
- Update name or coordinates
- Mark as popular
- Change parent location

**Popular Locations:**
- Featured on homepage
- Used in search filters
- Quick access for users

---

## Financial Management

### View Revenue Dashboard

**Admin → Finance → Dashboard**

**Metrics:**
- Total revenue
- Subscription revenue
- Commission earnings
- Monthly/yearly trends
- Top earning businesses
- Payment method breakdown

### Manage Payouts

**Admin → Finance → Payouts**

**Pending Payouts:**
1. View payout requests
2. Verify business wallet balance
3. Check bank details
4. Actions:
   - **Approve** - Process payment
   - **Reject** - Return to wallet
   - **Hold** - Request more information

**Payout History:**
- View all processed payouts
- Export to CSV
- Track payment status

### Commission Management

**Admin → Finance → Commissions**

- View all commission deductions
- Track commission per business
- Monthly commission reports
- Adjust commission rates (global or per-business)

**Default Commission:**
- 10% on all coupon sales
- Can be customized per business category

### Transaction History

**Admin → Finance → Transactions**

- View all platform transactions
- Filter by:
  - Transaction type
  - Date range
  - User
  - Status
- Export reports

### Refunds & Disputes

**Admin → Finance → Refunds**

1. Review refund requests
2. Check transaction details
3. Contact business/user
4. Process refund or reject
5. Add resolution notes

---

## Settings & Configuration

### General Settings

**Admin → Settings → General**

- **Site Name:** Ohmygoa
- **Tagline:** Discover. Connect. Experience Goa.
- **Contact Email:** support@ohmygoa.com
- **Contact Phone:** +91-XXX-XXX-XXXX
- **Address:** Company address
- **Social Media Links:** Facebook, Instagram, Twitter
- **Timezone:** Asia/Kolkata
- **Currency:** INR (₹)

### Email Settings

**Admin → Settings → Email**

- SMTP configuration
- Email templates management
- Test email functionality
- Queue settings

### Payment Settings

**Admin → Settings → Payments**

- Razorpay API keys
- Payment gateway mode (test/live)
- Supported payment methods
- Transaction fees
- Currency settings

### Subscription Settings

**Admin → Settings → Subscriptions**

- **Premium Plan Price:** ₹499/month
- Billing cycle (monthly/yearly)
- Trial period (if applicable)
- Feature limits
- Auto-renewal settings

### Commission Settings

**Admin → Settings → Commission**

- **Default Commission Rate:** 10%
- Per-category commission rates
- Minimum payout threshold: ₹1000
- Payout frequency
- Commission calculation method

### Points System Settings

**Admin → Settings → Points**

Configure points for actions:
- Registration: 100 points
- Profile completion: 50 points
- Email verification: 25 points
- Review submission: 10 points
- Photo upload: 5 points
- Daily login: 1 point

**Redemption Rates:**
- 1000 points = ₹100

### SEO Settings

**Admin → Settings → SEO**

- Meta title template
- Meta description template
- Keywords
- Google Analytics ID
- Google Search Console verification
- Social media OG image

### Maintenance Mode

**Admin → Settings → Maintenance**

- Enable/disable maintenance mode
- Custom maintenance message
- Allowed IP addresses (admin access)
- Scheduled maintenance window

---

## Reports & Analytics

### User Reports

**Admin → Reports → Users**

- New registrations (daily/monthly)
- User growth trends
- User engagement metrics
- Subscription conversion rate
- Churn rate

### Content Reports

**Admin → Reports → Content**

- Total listings by category
- Listings by status
- Most viewed listings
- New content submissions
- Content approval times

### Financial Reports

**Admin → Reports → Finance**

- Revenue reports (monthly/yearly)
- Commission earnings
- Payout summary
- Transaction volume
- Top earning businesses
- Payment method analysis

### Performance Reports

**Admin → Reports → Performance**

- Page views and traffic
- Search queries
- Popular categories
- Popular locations
- Bounce rate
- Conversion funnel

### Export Reports

All reports can be exported in:
- CSV
- Excel
- PDF

---

## Best Practices

### Content Moderation

1. **Review within 24 hours** - Quick approvals improve user experience
2. **Be consistent** - Apply rules fairly to all users
3. **Provide feedback** - Explain rejections clearly
4. **Monitor quality** - Regular audits of approved content
5. **Handle disputes** - Quick resolution of user complaints

### User Management

1. **Verify businesses** - Ensure legitimacy before approval
2. **Monitor activity** - Flag suspicious behavior
3. **Respond promptly** - Quick support responses
4. **Educate users** - Provide clear guidelines
5. **Fair enforcement** - Apply policies consistently

### Financial Management

1. **Process payouts timely** - Within 3-5 business days
2. **Monitor fraud** - Flag unusual transaction patterns
3. **Transparent reporting** - Clear financial statements
4. **Regular audits** - Monthly financial reviews
5. **Secure data** - Protect payment information

---

## Security & Access Control

### Admin Roles

**Super Admin:**
- Full system access
- Manage other admins
- Configure all settings

**Content Moderator:**
- Approve/reject content
- Edit listings
- Manage reviews

**Finance Admin:**
- View financial reports
- Process payouts
- Manage transactions

**Support Admin:**
- Manage user support tickets
- Respond to enquiries
- Help users

### Security Best Practices

1. **Strong passwords** - Require complex passwords
2. **Two-factor authentication** - Enable for all admins
3. **Regular backups** - Daily automated backups
4. **Audit logs** - Track all admin actions
5. **Session timeouts** - Auto-logout after inactivity
6. **IP whitelisting** - Restrict admin access to trusted IPs
7. **Regular updates** - Keep platform updated

---

## Support & Troubleshooting

### Common Issues

**Issue: User can't log in**
- Reset password
- Check email verification status
- Check if account is suspended

**Issue: Listing not appearing**
- Check approval status
- Verify subscription is active
- Check if listing is published

**Issue: Payment failed**
- Check payment gateway status
- Verify API keys
- Check transaction logs

**Issue: Email not sending**
- Check SMTP settings
- Verify queue is running
- Test email functionality

### System Maintenance

**Daily Tasks:**
- Review pending approvals
- Monitor system health
- Check error logs

**Weekly Tasks:**
- Process payouts
- Review user reports
- Update featured content

**Monthly Tasks:**
- Generate financial reports
- Audit user accounts
- Review and update policies

---

## Contact Technical Support

**For Platform Issues:**
- Email: tech@ohmygoa.com
- Phone: +91-XXX-XXX-XXXX
- Emergency: Available 24/7

---

*Last Updated: November 30, 2025*
