# Instructor Calling Email Feature

## Overview
When an instructor initiates a video call for an approved consultation, the student will now receive an email notification automatically, in addition to the real-time in-app notification.

## Files Created/Modified

### 1. New Email Class
**File:** `app/Mail/InstructorCallingMail.php`
- Mailable class for instructor calling emails
- Parameters: instructor name, consultation date/time, consultation type, call attempt number
- Sends to student's email address

### 2. New Email Template
**File:** `resources/views/emails/instructor_calling.blade.php`
- Professional HTML email template
- Includes:
  - Visual alert that instructor is calling
  - Consultation details (date, time, type)
  - Call attempt number tracking
  - Instructions on how to join the call
  - Link to join consultation directly
  - Security note for unexpected calls

### 3. Updated Routes
**File:** `routes/web.php`
- Added `InstructorCallingMail` import
- Updated consultation start route to send email when instructor initiates call
- Added test route: `GET /email-test/instructor-calling`

### 4. Updated Email Test Controller
**File:** `app/Http/Controllers/EmailTestController.php`
- Added `InstructorCallingMail` import
- Added `testInstructorCalling()` method for testing the instructor calling email
- Creates test consultation and sends email to student

### 5. Updated Documentation
**File:** `EMAIL_SETUP_GUIDE.md`
- Added Instructor Calling Email to templates section
- Updated Email Flow diagram to include instructor calling step
- Added instructor calling to test routes documentation with examples

## How It Works

### Automatic Email Trigger
When an instructor clicks "Start Call" (or initiates a video session):

1. Consultation status updates to `in_progress`
2. In-app notification is created for student (real-time)
3. **NEW:** Email is automatically sent to student with:
   - Instructor's name
   - Consultation date and time
   - Consultation type (Video Call, Face-to-Face, etc.)
   - Call attempt number (useful for retry tracking)
   - Button/link to join the consultation directly

### Email Features
- ✅ Professional, branded HTML email
- ✅ Mobile-responsive design
- ✅ Call attempt tracking (Attempt #1, #2, etc.)
- ✅ Direct action button to join consultation
- ✅ Security warning for unexpected calls
- ✅ Follows platform's email style consistency

## Testing

### Test Email Without Gmail Configuration
If you haven't configured Gmail credentials yet, test with:

```bash
# Test the instructor calling email
curl http://localhost:8000/email-test/instructor-calling

# Or visit in browser (when logged in)
http://localhost:8000/email-test/instructor-calling
```

### Test Email Response
```json
{
    "status": "success",
    "message": "Instructor calling email sent to student@example.com",
    "template": "instructor_calling",
    "recipient": "student@example.com",
    "instructor": "Prof. John Smith",
    "consultation_date": "2026-03-12"
}
```

## Production Notes

- Emails are automatically sent when instructor starts video call
- Student receives both in-app notification AND email
- Email includes security note to report unexpected calls
- Call attempt number helps track retries if student doesn't respond
- Works seamlessly with existing email system (Gmail, SendGrid, Mailtrap, etc.)

## Student Experience

When instructor calls:
1. **In-app:** Toast notification appears immediately (real-time)
2. **Email:** Student receives email within seconds
3. **Email Content:** All details to quickly understand and join the call
4. **Direct Link:** One-click access to join consultation from email

## Configuration Required

- `.env` must have email settings configured (see EMAIL_SETUP_GUIDE.md)
- No additional configuration needed once emails are set up
- Test routes automatically available in local/testing environment
