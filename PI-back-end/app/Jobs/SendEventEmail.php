<?php

namespace App\Jobs;

use App\Models\Event;
use App\Mail\EventNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;
//use log
use Illuminate\Support\Facades\Log;

class SendEventEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $event;
    public $messageContent;
    public $sendQr;


    public function __construct($event, $messageContent, $sendQr)
    {
        $this->event = $event;
        $this->messageContent = $messageContent;
        $this->sendQr = $sendQr;

    }

    private function parseMessage($message, $event, $participant)
    {
        $placeholders = [
            '{{ nome }}' => $participant->name,
            '{{ email }}' => $participant->email,
            '{{ evento }}' => $event->name,
            '{{ data }}' => \Carbon\Carbon::parse($event->start_date)->format('d-m-Y'),
            '{{ hora }}' => \Carbon\Carbon::parse($event->start_time)->format('H:i'),
            '{{ local }}' => $event->location,
            '{{ link_formulario }}' => url('events/' . $event->id . '/form'),
        ];

        return strtr($message, $placeholders);
    }

    public function handle()
    {
        \Log::info("Job SendEventEmail está a ser processado!", [
            'event' => $this->event->id,
            'message' => $this->messageContent,
            'send_qr' => $this->sendQr,
        ]);
    
        foreach ($this->event->participants as $participant) {
            \Log::info("A enviar email para: " . $participant->email);
            $finalMessage = $this->parseMessage($this->messageContent, $this->event, $participant);

            Mail::to($participant->email)->send(
                new EventNotification($this->event, $finalMessage, $participant, $this->sendQr)
            );
        }
    }

    

}

