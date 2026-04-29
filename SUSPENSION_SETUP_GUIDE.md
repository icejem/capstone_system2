# Setup Guide: Suspension & Urgent Features

## Quick Start

### Step 1: Run Database Migration

Run the migration to add suspension columns to the users table:

```bash
php artisan migrate
```

This creates:
- `suspension_expires_at` column (datetime, nullable)
- `suspension_reason` column (text, nullable)

### Step 2: Test the Features

#### Test Urgent Badge:
1. Create a consultation via the student dashboard
2. Set `consultation_priority` to 'urgent'
3. View upcoming consultations - you should see the red URGENT badge with pulse animation

#### Test User Suspension:
1. Login as admin
2. Go to Admin Dashboard → Students tab
3. Find a test student
4. Click "Manage"
5. Click "Suspend" button
6. Set suspension duration (e.g., 1 day)
7. Click "Suspend User"
8. Try logging in with that student account (access should be denied)

#### Test Auto-Unsuspend (Manual):
1. After suspension duration expires, run:
   ```bash
   php artisan users:unsuspend-expired
   ```
2. User can now login again

### Step 3: Enable Scheduler (Production)

For production, ensure the Laravel scheduler is running:

```bash
* * * * * cd /path/to/capstone_system2L && php artisan schedule:run >> /dev/null 2>&1
```

This runs the `users:unsuspend-expired` command hourly automatically.

---

## Admin UI Guide

### Suspending a User

1. Open Admin Dashboard
2. Go to **Students** or **Instructors** tab
3. Click **Manage** on the user row
4. Click the red **Suspend** button
5. A dialog appears with options:
   - Select suspension duration unit (Days/Weeks/Months)
   - Enter duration value (1-365)
   - Optionally add suspension reason
   - Preview shows expiry date/time
6. Click **Suspend User** to confirm

### Checking Suspension Status

- Suspended users show **"Suspended"** status in the table
- The status filters include a "Suspended" option
- You can filter to view only suspended accounts

---

## Student Dashboard: Urgent Consultations

- Upcoming consultations with `consultation_priority = 'urgent'` show:
  - Red badge with warning icon
  - Text "URGENT" in red
  - Subtle pulse animation
  - Left border highlight

---

## Database Queries

### Find Suspended Users:
```sql
SELECT id, name, email, account_status, suspension_expires_at, suspension_reason 
FROM users 
WHERE account_status = 'suspended';
```

### Find Users Whose Suspension Expires Today:
```sql
SELECT id, name, email, suspension_expires_at 
FROM users 
WHERE account_status = 'suspended' 
AND DATE(suspension_expires_at) = CURDATE();
```

### Manually Unsuspend a User:
```sql
UPDATE users 
SET account_status = 'active', 
    suspension_expires_at = NULL, 
    suspension_reason = NULL 
WHERE id = 123;
```

---

## Troubleshooting

### Migration Fails:
- Ensure you have valid database connection
- Check if migration file exists: `database/migrations/2026_04_29_000001_add_suspension_details_to_users_table.php`
- Try: `php artisan migrate:status` to check migration status

### Suspension Modal Not Appearing:
- Clear browser cache (Ctrl+Shift+Delete)
- Verify JavaScript in admin scripts is loaded
- Check browser console for errors

### Suspended User Can Still Login:
- Verify `suspension_expires_at` is set correctly
- Check database for user record
- Run: `php artisan users:unsuspend-expired` to force unsuspend if expired
- Check `hasActiveAccount()` is being called in login flow

### Urgent Badge Not Showing:
- Verify consultation has `consultation_priority = 'urgent'`
- Check CSS is loaded (verify `.urgent-badge` class exists)
- Clear browser cache
- Check Font Awesome icon library is loaded

---

## Files Reference

- Migration: `database/migrations/2026_04_29_000001_add_suspension_details_to_users_table.php`
- User Model: `app/Models/User.php`
- Route: `routes/web.php` (line 1371+)
- Command: `app/Console/Commands/UnsuspendExpiredUsers.php`
- Admin Modal: `resources/views/admin/dashboard/partials/modals.blade.php`
- Admin JS: `resources/views/admin/dashboard/partials/scripts.blade.php`
- Student Dashboard: `resources/views/student/dashboard.blade.php`

---

## Support

For issues, check:
1. Migration status: `php artisan migrate:status`
2. Scheduler registration: `php artisan schedule:list`
3. Browser console for JS errors
4. Laravel logs in `storage/logs/`
