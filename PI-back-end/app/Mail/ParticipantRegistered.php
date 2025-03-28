<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Participant;
use App\Models\Event;

class ParticipantRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public $participant;

    public function __construct(Participant $participant)
    {
        $this->participant = $participant;
        
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmação de Inscrição no Evento'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.participant_registered',
            with: [
                'participant' => $this->participant,
                'event' => $this->participant->event
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
