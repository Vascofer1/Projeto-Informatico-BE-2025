<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registration Confirmation</title>
</head>
<body>
    <h1>, {{ $particiHellopant->name }}!</h1>

    <p>Thank you for registering for the event <strong>{{ $event->name }}</strong>.</p>

    <p>The event will take place at <strong>{{ $event->location }}</strong> on <strong>{{ \Carbon\Carbon::parse($event->start_date)->format('m/d/Y') }}</strong>.</p>

    <p>You will soon receive more information and your entry QR Code.</p>

    <br>
    <p>Thank you and see you soon!</p>
</body>
</html>
