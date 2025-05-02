<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envio email</title>
</head>
<body>
<p><strong>Event Reminder:</strong> {{ $event->name ?? 'Unspecified Event' }}</p>

<p>{{ $messageContent ?? 'Conteúdo da mensagem não disponível.' }}</p>


@if($sendQr)
    <p>Here is your QR Code for the event:</p>
    <img src="{{ $message->embed(public_path('storage/qrcodes/' . $participant->id . '.png')) }}">
@endif



<p>Best regards,</p>  
<p>Eventify</p>
<p>Note: This is an automated message, please do not reply.</p>
    
</body>
</html>


