@component('mail::message')
# Confirm this login

Hello {{ $userName }},

We received a login attempt for your Consultation Platform account. Confirm it by clicking the button below.

@component('mail::button', ['url' => $verificationUrl, 'color' => 'primary'])
Yes, continue login
@endcomponent

This link expires at {{ $expiresAt->format('M d, Y h:i A') }}.

Login details:
- Device: {{ $deviceLabel ?: 'Unknown device' }}
- IP address: {{ $ipAddress ?: 'Unavailable' }}

If this was not you, you can ignore this email. Your account will stay locked out of the dashboard until the link is confirmed.

If the button does not work, copy and paste this URL into your browser:
{{ $verificationUrl }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
