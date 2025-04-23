<?php
namespace App\Jobs;

use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Twilio\Rest\Client;

class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $participant;
    protected $message;

    public function __construct(Participant $participant, string $message)
    {
        $this->participant = $participant;
        $this->message = $message;
    }

    public function handle(): void
    {
        $twilioSid = config('services.twilio.sid');
        $twilioToken = config('services.twilio.token');
        $twilioFrom = 'whatsapp:' . config('services.twilio.whatsapp_from');

        $client = new Client($twilioSid, $twilioToken);

        // Aqui garantimos que o número tem o prefixo +351 ou outro
        $number = $this->participant->phone;
        if (!str_starts_with($number, '+')) {
            $number = '+351' . ltrim($number, '0');
        }

        $client->messages->create(
            'whatsapp:' . $number,
            [
                'from' => $twilioFrom,
                'body' => $this->message
            ]
        );
    }
}
