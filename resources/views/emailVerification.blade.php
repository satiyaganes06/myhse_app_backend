<html>

<head>
    <!-- resources/views/emails/verification.blade.php -->

    <p>Click the following link to verify your email:</p>
    <a href="{{ url('/verify/' . $verificationCode) }}">Verify Email</a>

</html>