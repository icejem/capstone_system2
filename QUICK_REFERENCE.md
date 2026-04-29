# Quick Reference Card

## 🚀 How to Activate Features

### Immediate (First 2 minutes)
```bash
# 1. Run migration
php artisan migrate

# 2. Clear cache
php artisan cache:clear
php artisan config:clear
```

### That's it! ✅

---

## 🎯 Feature 1: Urgent Badge on Dashboards

**What:** Red badge with exclamation icon appears on urgent consultations  
**Where:** Student/Instructor "Upcoming Schedule" section  
**How:** Set `consultation_priority = 'urgent'` when creating consultation  
**Visual:** Red badge with pulse animation  
**No config needed** - Works automatically

---

## 🛡️ Feature 2: Admin User Suspension

**Access Point:**
```
Admin Dashboard → Students/Instructors Tab → Click "Manage" → Click "Suspend"
```

**What to Select:**
- Duration value: 1-365
- Duration unit: Days, Weeks, or Months
- Reason: Optional text field

**Result:**
- User status shows "Suspended"
- User cannot login
- Automatically unsuspends after duration expires

---

## 📊 Common Admin Tasks

### Suspend a Student
```
1. Admin Dashboard → Students
2. Find student → Click "Manage"
3. Click "Suspend" button
4. Set: 3 Days, Reason: "Missing consultations"
5. Confirm
```

### Suspend an Instructor
```
Same as above but in Instructors tab
```

### Check Suspended Users
```
Admin Dashboard → Students/Instructors
Use Status Filter: "Suspended"
```

### Manually Unsuspend (if needed)
```bash
php artisan users:unsuspend-expired
```

---

## 💾 Database Queries (SQL)

### Find all suspended users
```sql
SELECT name, email, suspension_expires_at, suspension_reason
FROM users WHERE account_status = 'suspended';
```

### Find users suspended on specific date
```sql
SELECT name, email, suspension_expires_at
FROM users 
WHERE DATE(suspension_expires_at) = '2026-05-01';
```

### Emergency: Unsuspend everyone
```sql
UPDATE users 
SET account_status = 'active', 
    suspension_expires_at = NULL, 
    suspension_reason = NULL 
WHERE account_status = 'suspended';
```

---

## 🐛 Troubleshooting (Quick Fixes)

### Urgent badge not showing
- Clear browser cache: Ctrl+Shift+Delete
- Check consultation has `priority='urgent'`
- Check Font Awesome icons are loaded

### Suspension modal doesn't appear
- Refresh page (F5)
- Clear browser cache
- Check browser console (F12) for errors

### Suspended user can still login
- Run: `php artisan users:unsuspend-expired`
- Check database: `SELECT * FROM users WHERE id=123;`
- Verify `suspension_expires_at` is set

### Can't suspend user
- Verify you're admin user
- Try suspending different user first
- Check browser console for error messages

---

## 📱 Status Badge Colors

| Status | Color | Meaning |
|--------|-------|---------|
| Active | Green | ✅ Can login |
| Inactive | Gray | ⊘ Blocked |
| Suspended | Red | 🚫 Temporarily blocked |

---

## ⏰ Duration Examples

| Selection | Result |
|-----------|--------|
| 1 Day | Expires tomorrow |
| 7 Days | Expires in 1 week |
| 2 Weeks | Expires in 14 days |
| 1 Month | Expires in ~30 days |
| 3 Months | Expires in ~90 days |

---

## 🔄 Automatic Processes

**Hourly Unsuspend Check:**
- Runs automatically at the top of each hour
- Finds users whose `suspension_expires_at <= NOW()`
- Changes status from "suspended" to "active"
- Clears suspension fields

**On Next Login:**
- Login check calls `hasActiveAccount()`
- This triggers `autoUnsuspendIfExpired()`
- If suspension expired, user is unsuspended immediately
- User can login normally

---

## 📞 When to Use Each Feature

### Urgent Badge
- Important consultations
- Limited availability
- High-priority topics
- Emergency sessions

### Suspension
- Policy violations
- Missing consultations
- Under investigation
- Temporary restrictions
- Need for account freeze

---

## ✨ Special Cases

**Case: Suspend Last Active Admin**
```
NOT ALLOWED ❌
Error: "Cannot suspend the last active admin account."
System prevents this to avoid lockout
```

**Case: Suspend for 30 Days, User Logs In Day 15**
```
User can login ✓
Still suspended until Day 30
Suspension doesn't change on login
```

**Case: Suspend for 3 Days, Forget to Unsuspend**
```
No problem - automatic! ✓
After 3 days pass, system auto-unsuspends
User can login normally
```

---

## 🔐 Permission Requirements

- **Suspend users**: Admin only
- **View suspensions**: Admin only
- **Create urgent consultations**: Any user
- **View urgent badge**: All users
- **Change own status**: Cannot (admin-only)

---

## 📈 Monitoring

### Check for suspended users
```bash
php artisan tinker
# Then:
User::where('account_status', 'suspended')->count()
```

### Check scheduler
```bash
php artisan schedule:list
```

### View last unsuspend results
```bash
tail -f storage/logs/laravel.log
# Look for: "user:unsuspend-expired"
```

---

## 🎓 Example Scenarios

**Scenario A: Short-term Suspension**
```
Reason: Missed 3 consultations
Duration: 3 days
Result: Student learns and returns after penalty
```

**Scenario B: Investigation Suspension**
```
Reason: Pending conduct review
Duration: 2 weeks
Result: Review completed, user unsuspends automatically
```

**Scenario C: Long-term Penalty**
```
Reason: Serious violation
Duration: 1 month
Result: Strong deterrent with automatic reset
```

---

## 📋 Maintenance Checklist (Monthly)

- [ ] Check suspended user count: `SELECT COUNT(*) FROM users WHERE account_status='suspended'`
- [ ] Review suspension reasons for patterns
- [ ] Verify scheduler is running: `php artisan schedule:list`
- [ ] Test unsuspend command: `php artisan users:unsuspend-expired`
- [ ] Check logs for errors: `tail storage/logs/laravel.log`

---

## 🆘 Emergency Contacts

- **Check logs**: `storage/logs/laravel.log`
- **Database issues**: `php artisan tinker`
- **Migration problems**: `php artisan migrate:status`
- **Scheduler issues**: `php artisan schedule:list`
- **Command issues**: `php artisan users:unsuspend-expired --help`

---

## 📚 Full Documentation

For detailed information, see:
1. `SUSPENSION_AND_URGENT_FEATURES.md` - Technical details
2. `SUSPENSION_SETUP_GUIDE.md` - Setup & troubleshooting
3. `IMPLEMENTATION_SUMMARY.md` - What changed
4. `VISUAL_REFERENCE.md` - UI/UX guide
5. `IMPLEMENTATION_CHECKLIST.md` - What's done

---

## ✅ Final Checklist

- [ ] Run `php artisan migrate`
- [ ] Test urgent badge (create urgent consultation)
- [ ] Test suspension (suspend test user)
- [ ] Test auto-unsuspend (wait or run command)
- [ ] Read documentation files
- [ ] Mark tasks complete

---

**Version:** 1.0  
**Date:** April 29, 2026  
**Status:** READY TO USE ✅

---

**Print this page or bookmark it for quick reference!**
