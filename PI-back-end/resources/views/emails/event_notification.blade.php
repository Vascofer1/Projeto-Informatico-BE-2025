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

<p>Here is your QR Code for the event:</p>

{!! $qrCodeSvg !!} <!-- Renderiza o SVG diretamente -->

<p>Thanks for your participation!</p>
<p>Best regards,</p>  
{{ config('app.name') }}</p>
    
</body>
</html>


