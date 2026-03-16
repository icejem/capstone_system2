# Email Configuration Setup Guide

## Overview
The Consultation Platform is configured to send emails using:
1. **Student submits consultation** → Email to Instructor
2. **Instructor approves/declines** → Email to Student
3. **Forgot password** → Password reset email
4. **Status updates** → Real-time notifications

## Email Configuration (Gmail)

### Step 1: Enable 2-Factor Authentication on Gmail
1. Go to [Google Account Security](https://myaccount.google.com/security)
2. Enable 2-Step Verification
3. Once enabled, create an **App Password**

### Step 2: Generate Gmail App Password
1. Go to [Google Account App Passwords](https://myaccount.google.com/apppasswords)
2. Select "Mail" and "Windows Computer"
3. Copy the 16-character app password

### Step 3: Update .env File
```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Consultation Platform"
```

**Example:**
```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=consultation.platform@gmail.com
MAIL_PASSWORD=abcd efgh ijkl mnop
MAIL_FROM_ADDRESS=consultation.platform@gmail.com
MAIL_FROM_NAME="Consultation Platform"
```

### Step 4: Clear Configuration Cache
```bash
php artisan config:clear
php artisan cache:clear
```

## Alternative Email Services

### Using Mailtrap (for testing)
```dotenv
MAIL_MAILER=smtp
MAIL_HOST=live.smtp.mailtrap.io
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="Consultation Platform"
```

### Using SendGrid
```dotenv
MAIL_MAILER=sendgrid
SENDGRID_API_KEY=your-sendgrid-api-key
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="Consultation Platform"
```

## Email Templates

### 1. Consultation Request Email
- **Triggered:** When student submits a consultation request
- **Recipient:** Instructor
- **Template:** `resources/views/emails/consultation_request.blade.php`
- **Contains:** Student details, consultation date/time, consultation type

### 2. Consultation Status Update Email
- **Triggered:** When instructor approves/declines a consultation
- **Recipient:** Student
- **Template:** `resources/views/emails/consultation_status_update.blade.php`
- **Contains:** Status, date/time, instructor name, action details

### 3. Instructor Calling Email
- **Triggered:** When instructor initiates a video call for an approved consultation
- **Recipient:** Student
- **Template:** `resources/views/emails/instructor_calling.blade.php`
- **Contains:** Instructor name, consultation date/time, call attempt number, instructions to join

### 4. Password Reset Email
- **Triggered:** When user requests password reset
- **Recipient:** User's email
- **Template:** `resources/views/emails/password_reset.blade.php`
- **Contains:** Password reset link (expires in 1 hour)

### 5. Student Cancellation Email
- **Triggered:** When student cancels a pending consultation request
- **Recipient:** Instructor
- **Template:** `resources/views/emails/student_cancellation.blade.php`
- **Contains:** Student name, cancelled consultation details, date/time

### 6. Admin Action Email
- **Triggered:** For every consultation action (submit, cancel, approve, decline, call started)
- **Recipient:** All admin users
- **Template:** `resources/views/emails/admin_action.blade.php`
- **Contains:** Action type, who performed it, who it affects, consultation details, timestamp

## Testing Email Configuration

### Option 1: Use MailHog (Local Testing)
```bash
# Download MailHog from: https://github.com/mailhog/MailHog
# Run MailHog
./MailHog

# Update .env:
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
```

Then access MailHog UI at `http://127.0.0.1:8025`

### Option 2: Use Built-in Test Routes
The platform includes test routes (only available in local/testing environment):

```bash
# Test email configuration status
GET /email-test/status

# Test password reset email
GET /email-test/password-reset

# Test consultation request email (student → instructor)
GET /email-test/consultation-request

# Test consultation approved email (instructor → student)
GET /email-test/consultation-approved

# Test consultation declined email (instructor → student)
GET /email-test/consultation-declined

# Test instructor calling email (instructor initiates call)
GET /email-test/instructor-calling

# Test student cancellation email (student → instructor)
GET /email-test/student-cancellation

# Test admin action email (all actions → admin)
GET /email-test/admin-action
```

**Example with cURL:**
```bash
curl http://localhost:8000/email-test/status
curl http://localhost:8000/email-test/instructor-calling
```

**Expected Response:**
```json
{
    "status": "success",
    "message": "Instructor calling email sent to student@example.com",
    "template": "instructor_calling",
    "recipient": "student@example.com",
    "instructor": "Prof. Smith",
    "consultation_date": "2026-03-12"
}
```

## Troubleshooting

### "SMTP authentication failed"
- Check MAIL_USERNAME and MAIL_PASSWORD in .env
- Ensure Gmail App Password is correct (16 characters with spaces)
- Verify 2-Factor Authentication is enabled on Gmail

### "Connection refused"
- Check MAIL_HOST and MAIL_PORT are correct
- For Gmail: use `smtp.gmail.com:587` with TLS
- Ensure no firewall is blocking outbound port 587

### "Unable to send reset email"
- Clear cache: `php artisan config:clear && php artisan cache:clear`
- Verify database has user records
- Check logs: `storage/logs/laravel.log`

### Emails going to Spam
- Gmail/Office365: Add your domain to SPF and DKIM records
- Consider using a dedicated email service like SendGrid or AWS SES

## Email Flow

```
1. Student Submits Consultation
   ├─ Creates consultation record
   ├─ Creates notification for instructor
   ├─ Sends email to instructor (ConsultationRequest)
   └─ Sends email to all admins (AdminActionMail - submitted)

2. Student Cancels Consultation (Pending Status)
   ├─ Updates status to 'cancelled'
   ├─ Creates notification for instructor
   ├─ Sends email to instructor (StudentCancellationMail)
   └─ Sends email to all admins (AdminActionMail - cancelled)

3. Instructor Reviews Request
   ├─ [Approve]
   │  ├─ Updates status to 'approved'
   │  ├─ Creates notification for student
   │  ├─ Sends email to student (ConsultationStatusUpdate - approved)
   │  └─ Sends email to all admins (AdminActionMail - approved)
   │
   └─ [Decline]
      ├─ Updates status to 'declined'
      ├─ Creates notification for student
      ├─ Sends email to student (ConsultationStatusUpdate - declined)
      └─ Sends email to all admins (AdminActionMail - declined)

4. Instructor Initiates Video Call
   ├─ Consultation status set to 'in_progress'
   ├─ Creates notification for student (real-time)
   ├─ Sends email to student (InstructorCallingMail) with:
   │  ├─ Instructor name
   │  ├─ Consultation date/time
   │  ├─ Call attempt number
   │  └─ Instructions to join
   └─ Sends email to all admins (AdminActionMail - call_started)

5. Student Requests Password Reset
   ├─ Enters email in forgot password form
   ├─ System generates reset token
   └─ Sends reset email with link (expires in 1 hour)

6. Student Clicks Reset Link
   └─ Can set new password
```

## Admin Notifications

Admins receive emails for all major consultation activities:
- ✅ New consultation submission (student → instructor)
- ✅ Consultation cancellation (student cancel)
- ✅ Approval decision (instructor approve)
- ✅ Decline decision (instructor decline)
- ✅ Call initiated (instructor start call)

This allows admins to monitor platform activity and respond quickly if escalation is needed.

## Production Considerations

1. **Use HTTPS URLs** in .env:
   ```dotenv
   APP_URL=https://your-domain.com
   APP_FORCE_HTTPS=true
   ```

2. **Use Production Email Service:**
   - SendGrid for high volume
   - AWS SES for cost efficiency
   - Mailgun for reliability

3. **Configure Queue** for better performance:
   ```dotenv
   QUEUE_CONNECTION=database
   ```

4. **Add SPF/DKIM Records** for deliverability:
   - Contact your email service for DNS records
   - Add records to your domain's DNS settings

## Security Notes

- Never commit `.env` file with real credentials
- Use environment variables for sensitive data
- Emails containing reset links expire after 1 hour
- Gmail App Passwords are different from account passwords
