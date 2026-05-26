<!DOCTYPE html>
<html>
<head>
    <title>{{ __('passwords.mail_subject') }}</title>
</head>
<body>
    <h1>{{ __('passwords.mail_heading') }}</h1>
    <p>{{ __('passwords.mail_intro') }}</p>

    @if (!empty($emailAddress))
        <p>{{ __('passwords.mail_email') }}: {{ $emailAddress }}</p>
    @endif

    @if (!empty($resetUrl))
        <p><a href="{{ $resetUrl }}">{{ __('passwords.mail_button') }}</a></p>
    @endif

    <p>{{ __('passwords.mail_expire') }}</p>
</body>
</html>