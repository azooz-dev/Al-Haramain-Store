@component('mail::message')
{{-- Logo Header --}}
<div style="text-align: center; margin-bottom: 24px;">
  <div style="display: inline-flex; align-items: center; gap: 12px;">
    <div style="
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: 600;
        ">ح</div>
    <span style="font-size: 24px; font-weight: 600; color: #1f2937;">Al-Haramain</span>
  </div>
</div>

# {{ __('mail.verification.greeting', ['name' => $user->name]) }}

{{ __('mail.verification.intro') }}

@component('mail::panel')
<div style="
    text-align: center;
    font-size: 36px;
    font-weight: 700;
    letter-spacing: 8px;
    color: #1f2937;
    padding: 12px 0;
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(234, 88, 12, 0.1) 100%);
    border-radius: 12px;
">{{ $code }}</div>
@endcomponent

<div style="
    text-align: center;
    padding: 16px;
    background: #f9fafb;
    border-radius: 8px;
    margin: 24px 0;
    border-left: 4px solid #f59e0b;
">
  <p style="margin: 0; color: #d97706; font-weight: 500;">
    ⏱️ {{ __('mail.verification.expiry') }}
  </p>
</div>

{{ __('mail.verification.ignore') }}

{{-- Footer --}}
<div style="text-align: center; margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
  <p style="color: #6b7280; font-size: 14px; margin: 0;">
    {{ __('mail.verification.thanks') }}
  </p>
  <p style="
        color: #d97706;
        font-size: 16px;
        font-weight: 600;
        margin: 8px 0 0 0;
    ">{{ config('app.name') }}</p>
</div>
@endcomponent
