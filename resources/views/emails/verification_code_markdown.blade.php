@component('mail::message')
# Hello {{ $user->name }}

Use the following verification code to confirm your email address:

@component('mail::panel')
{{ $code }}
@endcomponent

This code will expire in 15 minutes.

If you didn't request this, just ignore this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
