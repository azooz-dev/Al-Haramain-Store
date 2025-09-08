@component('mail::message')
# Reset your password

Click the button below to reset your password.

@component('mail::button', ['url' => $resultUrl])
Reset Password
@endcomponent

If you didn’t request this, no action is required.

Token: {{ $token }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent