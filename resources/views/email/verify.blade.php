@component('mail::message')
Verify your account.

Thank You ;D

@component('mail::button', ['url' => "http://localhost:8080/user/verify/{$token}"]);
Follow the link for verify your acccount.
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
