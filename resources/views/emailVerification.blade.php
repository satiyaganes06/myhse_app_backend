<html>

<head>
    <!-- resources/views/emails/verification.blade.php -->

    <p>Click the following link to verify your email:</p>
    <a href="{{ url('/emailVerification/' . $userId) }}">Verify Email</a>

</html>
