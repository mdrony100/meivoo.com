@component('mail::message')
# Password Reset OTP

Use this code to continue resetting your password:

@component('mail::panel')
{{ $otp }}
@endcomponent

This code expires in {{ $expiresInMinutes }} minutes.

If you did not request this, you can safely ignore this email.

Thanks,
{{ config('app.name') }}
@endcomponent
