@component('mail::message')
# Password Reset Request

Hello {{ $userName }},

We received a request to reset the password for your account in the Consultation Platform. If you did not make this request, you can safely ignore this email.

To reset your password, please click the button below:

@component('mail::button', ['url' => $resetUrl, 'color' => 'primary'])
Reset Password
@endcomponent

**This link will expire in 1 hour.**

If you're having trouble clicking the button, copy and paste this URL into your browser:
{{ $resetUrl }}

---

**For security reasons:**
- Never share this link with anyone
- This link is personal and should not be forwarded
- If you did not request this password reset, please ignore this email

If you have any questions, please contact our support team.

Best regards,  
ONLINE FACULTY-STUDENT CONSULTATION FOR CCS Team
@endcomponent
