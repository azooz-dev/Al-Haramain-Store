<x-mail::message>
# Introduction

The body of your message.

<x-mail::button :url="''">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
@component('mail::message')
{{-- Header with logo --}}
<p style="text-align:center">
  <img src="{{ url('/images/logo.png') }}" alt="Site Logo" style="max-width:150px;">
</p>

# استعادة كلمة المرور

مرحبًا {{ $name }},

لقد طلبت إعادة تعيين كلمة المرور. اضغط على الزر التالي لتعيين كلمة مرور جديدة:

@component('mail::button', ['url' => $resetUrl])
تعيين كلمة المرور
@endcomponent

إذا لم تطلب ذلك، يمكنك تجاهل هذا الإيميل.

تحياتنا,<br>
{{ config('app.name') }}
@endcomponent