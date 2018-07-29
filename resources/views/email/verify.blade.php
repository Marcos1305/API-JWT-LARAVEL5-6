<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Complete registration</title>
</head>
<body>
    Hi {{ $name }},
    <br>
    Thank you for creating an account with us. Don't forget to complete you registration!
    <br>
    Please click on the link below or copy it into the address bar of your browser to confirm your email address:
    <br>
    <a href="{{ url('user/verify', $verification_code )}}">Confirm my email address</a>
    </br>
</body>
</html>
