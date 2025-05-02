<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Event;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Storage;
use App\Models\Participant;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;


use Illuminate\Support\Facades\Mail;
use App\Jobs\SendEventEmail;

class EventNotification extends Mailable
{
    use Queueable, SerializesModels;
    public $event;
    public $messageContent;
    public $participant;
    public $sendQr;
    public $participant_id;


    /**
     * Create a new message instance.
     */
    public function __construct(Event $event, $messageContent, Participant $participant, $sendQr)
    {
        $this->event = $event;
        $this->messageContent = $messageContent;
        $this->participant = $participant;
        $this->sendQr = $sendQr;
        $this->participant_id = $participant->id;
        
       
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Event Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {   
        
        return new Content(
            view: 'emails.event_notification',
            with: [
                'event' => $this->event,
                'messageContent' => $this->messageContent,
                'sendQr' => $this->sendQr,
                'participant_id' => $this->participant_id,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [

        ];
    }
}
