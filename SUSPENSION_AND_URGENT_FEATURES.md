# Urgent Consultation Badge & Admin Suspension Feature Implementation

## Overview
This document outlines the implementation of two key features added to the consultation system:

1. **Urgent Consultation Badge (Red)** - Visual indicator for urgent consultations
2. **Admin User Suspension with Configurable Duration** - Admin can suspend users for specified periods

---

## Feature 1: Urgent Consultation Badge

### Description
Students and instructors can now see a red "URGENT" badge on upcoming consultation sessions to quickly identify urgent consultations.

### Implementation Details

#### Database
- Uses existing `consultation_priority` field in the `consultations` table
- Values: 'urgent' (case-insensitive)

#### Frontend Changes

**File:** `resources/views/student/dashboard.blade.php`
- Added `$isUrgent` variable to check if `consultation_priority` is 'urgent'
- Shows urgent badge on upcoming consultations

**File:** `resources/views/student/dashboard/partials/content.blade.php`
- Modified upcoming consultations display to include urgent badge
- Badge displays only when priority is 'urgent'

**File:** `resources/views/student/dashboard/partials/styles.blade.php`
- Added `.urgent-badge` CSS class with:
  - Red background color (#dc2626)
  - Red text color
  - Pulse animation for visual emphasis
  - Exclamation icon
- Added `.schedule-item-urgent` class for schedule item styling

### Usage
1. When creating a consultation, set `consultation_priority` to 'urgent'
2. The red badge automatically appears on the student/instructor dashboard
3. Badge includes:
   - Warning icon
   - "URGENT" text
   - Subtle pulse animation for attention

---

## Feature 2: Admin User Suspension with Duration

### Description
Admins can now suspend user accounts (students/instructors) for a configurable duration measured in days, weeks, or months. When the suspension period expires, users are automatically unsuspended.

### Database Changes

**Migration:** `database/migrations/2026_04_29_000001_add_suspension_details_to_users_table.php`

Added columns to `users` table:
- `suspension_expires_at` (datetime, nullable) - When the suspension expires
- `suspension_reason` (text, nullable) - Reason for suspension

### Model Changes

**File:** `app/Models/User.php`

Added methods:
- `autoUnsuspendIfExpired()` - Automatically unsuspend if expiry date passed
- `isSuspended()` - Check if user is currently suspended
- `getSuspensionExpiryAttribute()` - Get formatted expiry date in Manila timezone
- Modified `hasActiveAccount()` to call `autoUnsuspendIfExpired()`

### Backend Implementation

**Route:** `POST /admin/users/{user}/suspend`
- Location: `routes/web.php` (lines 1371-1425)
- Validates suspension duration (1-365)
- Validates suspension unit (days/weeks/months)
- Calculates expiry date based on current time + duration
- Prevents suspending last active admin
- Updates user status to 'suspended'

**Command:** `app/Console/Commands/UnsuspendExpiredUsers.php`
- Finds all suspended users with expired suspension dates
- Automatically resets their status to 'active'
- Clears suspension fields
- Can be run manually or scheduled

**Scheduler:** `app/Console/Kernel.php`
- Schedules `users:unsuspend-expired` command hourly
- Ensures users are automatically unsuspended when period expires

### Frontend Implementation

**Modal:** `resources/views/admin/dashboard/partials/modals.blade.php`
- New `#suspensionModal` for suspension duration selection
- Input fields:
  - Duration value (1-365)
  - Duration unit (days/weeks/months)
  - Suspension reason (optional)
- Real-time expiry date preview

**Styling:** `resources/views/admin/dashboard/partials/styles.blade.php`
- `.suspension-label` - Label styling
- `.suspension-duration-options` - Radio button group
- `.suspension-input` - Number input
- `.suspension-textarea` - Reason text area

**JavaScript:** `resources/views/admin/dashboard/partials/scripts.blade.php`
- `openSuspensionModal()` - Opens suspension modal
- `closeSuspensionModalFn()` - Closes suspension modal
- `updateSuspensionExpiryPreview()` - Updates expiry date preview in real-time
- `submitSuspension()` - Submits suspension request to backend
- Event listeners for all modal interactions
- Replaces default "Suspend" button to use duration modal

### Admin UI Workflow

1. **Open Admin Dashboard**
   - Navigate to admin dashboard
   - Go to Students or Instructors tab

2. **Find User**
   - Search for user by name, email, or ID
   - Apply filters as needed

3. **Manage User**
   - Click "Manage" or "View" button on user row
   - Manage modal opens

4. **Suspend User**
   - Click "Suspend" button in manage modal
   - Suspension modal opens

5. **Configure Suspension**
   - Select duration unit (days, weeks, months)
   - Enter duration value (1-365)
   - Optionally enter suspension reason
   - Preview shows when suspension will expire

6. **Confirm Suspension**
   - Click "Suspend User" button
   - User status changes to "Suspended" in table
   - Toast notification confirms suspension

7. **Auto-Unsuspend**
   - When suspension expiry time passes, user can login normally
   - System automatically clears suspension fields
   - Next hourly task run updates status

### Security Features

- Only admins can suspend users
- Cannot suspend the last active admin account
- Suspension duration limited to 365 days max
- Prevents logging in while suspended
- All suspension actions logged

### Usage Examples

**Suspend a student for 3 days:**
- Duration: 3
- Unit: Days
- Reason: Violate conduct policy

**Suspend an instructor for 2 weeks:**
- Duration: 2
- Unit: Weeks
- Reason: Unauthorized absence from scheduled consultations

**Suspend for 1 month:**
- Duration: 1
- Unit: Months
- Reason: Pending investigation

---

## Implementation Checklist

- [x] Create database migration for suspension fields
- [x] Add suspension methods to User model
- [x] Create suspension route endpoint
- [x] Create UnsuspendExpiredUsers command
- [x] Create Kernel scheduler
- [x] Create suspension modal UI
- [x] Add suspension CSS styling
- [x] Add suspension JavaScript handling
- [x] Add urgent badge to consultation dashboard
- [x] Add urgent badge styling and animation
- [x] Login protection checks (existing)

---

## Testing Steps

### Test 1: Urgent Badge Display
1. Create a consultation with `consultation_priority = 'urgent'`
2. View student/instructor dashboard
3. Verify red "URGENT" badge appears on consultation
4. Verify badge has pulse animation

### Test 2: Basic Suspension
1. Go to admin dashboard
2. Find a student/instructor
3. Click "Manage"
4. Click "Suspend"
5. Set duration: 1 day
6. Click "Suspend User"
7. Verify user status shows "Suspended"
8. Try to login with that user (should be denied)

### Test 3: Suspension with Reason
1. Repeat Test 2
2. Add suspension reason
3. Verify database stores reason

### Test 4: Auto-Unsuspend
1. Suspend a user for 1 day
2. Check database: `suspension_expires_at` should be tomorrow
3. Manually run: `php artisan users:unsuspend-expired`
4. Verify user status changes back to "active"
5. User can now login again

### Test 5: Duration Units
1. Test suspending for different units:
   - 7 days
   - 2 weeks
   - 1 month
2. Verify expiry date calculations are correct

---

## Files Modified/Created

### Created Files:
- `database/migrations/2026_04_29_000001_add_suspension_details_to_users_table.php`
- `app/Console/Commands/UnsuspendExpiredUsers.php`
- `app/Console/Kernel.php`
- `app/Http/Controllers/SuspendUserController.php`

### Modified Files:
- `app/Models/User.php` - Added suspension methods
- `routes/web.php` - Added suspension route
- `resources/views/student/dashboard.blade.php` - Added urgent logic
- `resources/views/student/dashboard/partials/content.blade.php` - Added urgent badge display
- `resources/views/student/dashboard/partials/styles.blade.php` - Added urgent and schedule-item-urgent styles
- `resources/views/admin/dashboard/partials/modals.blade.php` - Added suspension modal
- `resources/views/admin/dashboard/partials/styles.blade.php` - Added suspension CSS
- `resources/views/admin/dashboard/partials/scripts.blade.php` - Added suspension JavaScript

---

## Database Schema

### Users Table - New Columns
```sql
ALTER TABLE users ADD COLUMN suspension_expires_at DATETIME NULL;
ALTER TABLE users ADD COLUMN suspension_reason TEXT NULL;
```

### Consultations Table - Existing Field
```
consultation_priority: enum ('low', 'normal', 'urgent') 
```

---

## API Endpoints

### Suspend User
```
POST /admin/users/{user}/suspend
Content-Type: application/json
X-CSRF-TOKEN: {token}

{
  "suspension_duration": 1,
  "suspension_unit": "days",
  "suspension_reason": "Policy violation"
}

Response:
{
  "message": "Account suspended until April 30, 2026 3:45 PM (Asia/Manila).",
  "user": {
    "id": 123,
    "account_status": "suspended",
    "suspension_expires_at": "2026-04-30T15:45:00+08:00"
  }
}
```

### Auto-Unsuspend Command
```
php artisan users:unsuspend-expired
```

---

## Notes

- All times use Asia/Manila timezone
- Suspension expiry is checked on every login attempt
- The scheduler runs hourly; alternatively, run the command manually
- Urgent badge uses Font Awesome icons
- No additional dependencies required
