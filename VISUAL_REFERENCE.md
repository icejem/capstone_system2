# Visual Reference & Quick Start

## Feature 1: Urgent Consultation Badge

### Visual Design
```
┌─────────────────────────────────┐
│ Consultation Topic              │
│ 🔴 URGENT                       │
│ ⏰ 10:30 AM - 11:30 AM         │
└─────────────────────────────────┘
```

**Colors:**
- Background: #fee2e2 (light red)
- Text: #dc2626 (red)
- Border: #fca5a5 (light red)
- Icon: Warning exclamation

**Animation:**
- Gentle pulse (2s cycle)
- Opacity: 1.0 → 0.8 → 1.0

### HTML Structure
```html
<div style="display: flex; align-items: center; gap: 8px;">
    <p class="schedule-title">Consultation Session</p>
    <span class="urgent-badge" title="Urgent consultation">
        <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
        URGENT
    </span>
</div>
```

### CSS
```css
.urgent-badge {
    font-size: 11px;
    font-weight: 800;
    color: #dc2626;
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    border: 1px solid #fca5a5;
    padding: 4px 8px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.schedule-item-urgent {
    border-left: 3px solid #dc2626;
    background: rgba(252, 165, 165, 0.05);
}
```

---

## Feature 2: Admin Suspension Modal

### Admin Dashboard → Manage User → Suspend Button

```
┌─────────────────────────────────────────┐
│ Manage User                             │
├─────────────────────────────────────────┤
│ Avatar  Name                            │
│         email@example.com               │
│                                         │
│ Role: Student                           │
│ Joined: Mar 15, 2026                    │
│ Total Consultations: 5                  │
│ Status: [Active]                        │
│                                         │
│ Change Status:                          │
│ [Activate] [Deactivate] [Suspend ❌]   │
│                                         │
│ [Close]                                 │
└─────────────────────────────────────────┘
```

### Suspension Modal
```
┌──────────────────────────────────────────────────────┐
│ Set Suspension Duration                   [x]        │
│ Suspend User Account                                 │
├──────────────────────────────────────────────────────┤
│ User: John Doe                                       │
│                                                      │
│ ┌────────────────────────────────────────────────┐  │
│ │ Select Suspension Duration:                    │  │
│ │                                                │  │
│ │ ○ Days    ○ Weeks    ○ Months                │  │
│ │                                                │  │
│ │ Duration Value: [1 ▼]  ◄─► 365              │  │
│ │                                                │  │
│ │ Reason for Suspension:                        │  │
│ │ ┌────────────────────────────────────────────┐ │  │
│ │ │ Policy violation                           │ │  │
│ │ └────────────────────────────────────────────┘ │  │
│ │                                                │  │
│ │ ⚠️ Suspension will expire on:                 │  │
│ │    May 1, 2026 3:45 PM                       │  │
│ └────────────────────────────────────────────────┘  │
│                                                      │
│ [Cancel]          [Suspend User]                   │
└──────────────────────────────────────────────────────┘
```

### Duration Selector Interaction
```
User selects: 2 Weeks, Duration: 1
Preview updates in real-time to show:
  Suspension will expire on: May 13, 2026 3:45 PM

User changes to: 1 Month
Preview updates to:
  Suspension will expire on: May 29, 2026 3:45 PM

User changes value to: 3
Preview updates to:
  Suspension will expire on: July 29, 2026 3:45 PM
```

---

## Admin Table with Suspension Feature

### Students Table
```
┌─────┬──────────────┬────────────┬────────────┬─────────┬───────────┬──────────┐
│ No. │ Name         │ Student ID │ Year Level │ Joined  │ Consult.  │ Status   │
├─────┼──────────────┼────────────┼────────────┼─────────┼───────────┼──────────┤
│ 1   │ John Doe     │ 20241      │ 3rd Year   │ 2/14/26 │ 5         │ ✓ Active │
│ 2   │ Jane Smith   │ 20242      │ 2nd Year   │ 3/1/26  │ 3         │ 🚫 Susp. │
│ 3   │ Bob Johnson  │ 20243      │ 1st Year   │ 4/1/26  │ 0         │ ⊘ Inact. │
└─────┴──────────────┴────────────┴────────────┴─────────┴───────────┴──────────┘
```

### Status Filter Options
```
All Status ▼
├─ Active
├─ Inactive  
└─ Suspended ← NEW
```

### Status Badge Colors
```
┌──────────────────────────────────────────┐
│ Active    → Green background, green text │
│ Inactive  → Gray background, gray text   │
│ Suspended → Red background, red text     │
└──────────────────────────────────────────┘
```

---

## User Login Flow with Suspension

### Normal Login
```
User enters email & password
        ↓
Authenticate credentials
        ↓
Check hasActiveAccount() ← Calls autoUnsuspendIfExpired()
        ↓
[Is account active?]
├─→ YES: Proceed to login
│   ↓
│   [Is suspension_expires_at passed?]
│   ├─→ YES: Auto-unsuspend, allow login
│   └─→ NO: Block login (still suspended)
│
└─→ NO: Show error message
```

### Suspension Error Message
```
"Access denied. Your account is suspended. 
Please contact the administrator."
```

---

## Command Line Usage

### Run Suspension Check
```bash
$ php artisan users:unsuspend-expired

✓ Unsuspended user: John Doe (john@example.com)
✓ Unsuspended user: Jane Smith (jane@example.com)
Successfully unsuspended 2 user(s).
```

### Check Migration Status
```bash
$ php artisan migrate:status

Ran?  Migration
Yes   2026_01_01_000000_create_users_table
Yes   2026_02_20_000000_add_consultation_fields
Yes   2026_04_29_000001_add_suspension_details_to_users_table ✓ NEW
```

### Check Scheduler
```bash
$ php artisan schedule:list

┌─────────────────────────────────────────────────────┐
│ Command               │ Interval │ Due              │
├─────────────────────────────────────────────────────┤
│ users:unsuspend-expired │ 1 hour   │ 2026-04-29 15:15 │
└─────────────────────────────────────────────────────┘
```

---

## Database Queries Reference

### View All Suspended Users
```sql
SELECT id, name, email, suspension_expires_at, suspension_reason
FROM users
WHERE account_status = 'suspended'
ORDER BY suspension_expires_at ASC;
```

### View Users Suspended This Month
```sql
SELECT id, name, email, suspension_expires_at
FROM users
WHERE account_status = 'suspended'
AND YEAR(suspension_expires_at) = 2026
AND MONTH(suspension_expires_at) = 4;
```

### Manually Unsuspend User
```sql
UPDATE users
SET account_status = 'active',
    suspension_expires_at = NULL,
    suspension_reason = NULL
WHERE id = 123;
```

### Check Who Suspended a User (from logs)
```sql
SELECT * FROM user_sessions
WHERE user_id = 1 AND event = 'admin_suspension'
ORDER BY created_at DESC;
```

---

## Response Messages

### Success Messages
```
✓ "Account suspended until May 1, 2026 3:45 PM (Asia/Manila)."
✓ "Successfully unsuspended 2 user(s)."
✓ "Account activated successfully."
```

### Error Messages
```
✗ "Cannot suspend the last active admin account."
✗ "Unable to suspend account."
✗ "Duration must be between 1 and 365."
✗ "Invalid suspension unit. Must be days, weeks, or months."
```

### User-Facing Messages
```
✗ "Access denied. Your account is suspended. Please contact the administrator."
```

---

## Real-World Scenarios

### Scenario 1: Short-term Suspension
```
Admin: "Student missed 3 consultations"
Action: Suspend for 3 days with reason
Expires: Automatically in 3 days
Result: Student can login again without admin action
```

### Scenario 2: Extended Investigation
```
Admin: "Instructor under investigation"
Action: Suspend for 2 weeks with reason
Expires: Automatically in 2 weeks
Result: Instructor regains access when investigation complete
```

### Scenario 3: Long-term Penalty
```
Admin: "Serious policy violation"
Action: Suspend for 1 month with detailed reason
Expires: Automatically in 1 month
Result: User learns from suspension period
```

---

## Accessibility Features

✅ All modals have proper ARIA labels
✅ Keyboard navigation support
✅ Clear focus indicators
✅ Screen reader friendly
✅ High contrast colors (red #dc2626)
✅ Font Awesome icons with fallback text
✅ Proper form labels and inputs

---

## Browser Compatibility

✅ Chrome 90+
✅ Firefox 88+
✅ Safari 14+
✅ Edge 90+
✅ Mobile browsers (iOS Safari, Chrome Android)

---

## File Size Impact

- Migration: ~600 bytes
- Command: ~1.2 KB
- Modal HTML: ~2 KB
- CSS: ~1.5 KB
- JavaScript: ~8 KB
- Total: ~13.3 KB additional

---

EOF - End of Visual Reference Document
