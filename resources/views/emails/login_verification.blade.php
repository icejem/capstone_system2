@component('mail::message')
# Are you trying to log in?

Hello {{ $userName }},

We received a sign-in attempt for your Consultation Platform account. Review the details below and approve only if it was really you.

@component('mail::button', ['url' => $verificationUrl, 'color' => 'primary'])
YES, it's me
@endcomponent

@component('mail::button', ['url' => $denyUrl, 'color' => 'error'])
NO, secure my account
@endcomponent

This request expires at {{ $expiresAt->format('M d, Y h:i A') }}.

Login details:
- Device: {{ $deviceLabel ?: 'Unknown device' }}
- Network/IP: {{ $ipAddress ?: 'Unavailable' }}
- Attempted at: {{ $attemptedAt?->format('M d, Y h:i A') }}
- Approximate location: Unavailable from server lookup

If you tap YES, only the original browser that requested this login will be allowed to continue.
If you tap NO, this login request will be denied immediately.

If the button does not work, copy and paste this URL into your browser:
{{ $verificationUrl }}

Thanks,<br>
ONLINE FACULTY-STUDENT CONSULTATION FOR CCS
@endcomponent
