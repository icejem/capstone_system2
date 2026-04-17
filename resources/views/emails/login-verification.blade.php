@component('mail::message')

# 🔐 Confirm Your Login

Hi {{ $user->first_name }},

We received a login request to your Consultation Platform account. To protect your account, please confirm this login by clicking the button below.

@component('mail::button', ['url' => $verificationUrl, 'color' => 'primary'])
✓ Confirm Login
@endcomponent

**Verification Details:**
- **Expires in:** {{ $expiresIn }} minutes
- **Browser:** {{ $browserInfo['browser'] }}
- **Device:** {{ $browserInfo['os'] }}
- **IP Address:** {{ $ipAddress }}

---

**Didn't recognize this login attempt?**

If this wasn't you, please ignore this email and your account will remain secure. Your login will only be confirmed if you click the button above.

---

For security reasons:
- This link is valid for only {{ $expiresIn }} minutes
- Each link can only be used once
- We never send your password in emails

Thanks,
**Consultation Platform Team**

@endcomponent
