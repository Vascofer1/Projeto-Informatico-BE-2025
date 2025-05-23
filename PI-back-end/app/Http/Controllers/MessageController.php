<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use App\Models\Event;
use App\Jobs\SendEventEmail;
use Carbon\Carbon;
use App\Mail\EventNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Participant;
use App\Jobs\SendWhatsAppMessage;
use Twilio\Rest\Client;



class MessageController extends Controller
{
    public function create($eventId)
    {
        $event = Event::find($eventId);
        if (!$event) {
            return redirect()->back()->with('error', 'Event not found.');
        }
        if ($event->status !== 'Upcoming') {
            return redirect()->back()->with('error', 'Not allowed to send messages for this event.');
        }
        
        return Inertia::render('Messages', [
            'event' => $event
        ]);
    }


    public function scheduleEmail(Request $request)
    {
        \Log::info('scheduleEmail method called', ['request' => $request->all()]);

        $request->validate([
            'event_id' => 'required|exists:events,id',
            'message' => 'required|string',
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'type' => 'required|in:email',
            'send_qr' => 'boolean',
    ]);

        \Log::info('Request validated successfully');

        $event = Event::findOrFail($request->event_id);
        \Log::info('Event found', ['event_id' => $event->id]);


        $messageContent = $request->message;
        \Log::info('Message content set', ['message' => $messageContent]);

        // Converter data para Carbon e agendar o job
        $sendTime = Carbon::parse($request->start_time);
        \Log::info('Send time parsed', ['send_time' => $sendTime]);
        $sendTime = Carbon::parse($sendTime, 'Europe/Lisbon')->setTimezone('UTC');
        
        if ($sendTime->isPast() || $sendTime->toDateString() < Carbon::now('UTC')->toDateString()) {
            return redirect()->back()->with('error', 'A data/hora de envio não pode ser no passado.');
        }
        
        dispatch(new SendEventEmail($event, $messageContent, $request->send_qr))->delay($sendTime); //aqui
        \Log::info('Email job dispatched', ['event_id' => $event->id, 'send_time' => $sendTime]);

        return redirect()->back()->with('success', 'Mensagem agendada com sucesso!');

    }


    
    public function scheduleWhatsapp(Request $request)
    {
        \Log::info('scheduleWhatsapp method called', ['request' => $request->all()]);

        $request->validate([
            'event_id' => 'required|exists:events,id',
            'message' => 'required|string',
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'type' => 'required|in:whatsapp',
        ]);

        \Log::info('Request validated successfully');

        $event = Event::findOrFail($request->event_id);
        \Log::info('Event found', ['event_id' => $event->id]);

        $messageContent = $request->message;
        \Log::info('Message content set', ['message' => $messageContent]);

        // Converter data para Carbon e agendar o job
        $sendTime = Carbon::parse($request->start_time);
        \Log::info('Send time parsed', ['send_time' => $sendTime]);
        $sendTime = Carbon::parse($sendTime, 'Europe/Lisbon')->setTimezone('UTC');
        
        if ($sendTime->isPast() || $sendTime->toDateString() < Carbon::now('UTC')->toDateString()) {
            return redirect()->back()->with('error', 'A data/hora de envio não pode ser no passado.');
        }

        foreach ($event->participants as $participant) {
            dispatch(new SendWhatsAppMessage($participant, $messageContent))->delay($sendTime);
            \Log::info('WhatsApp message job dispatched', ['participant_id' => $participant->id, 'send_time' => $sendTime]);
        }

        return redirect()->back()->with('success', 'Mensagem agendada com sucesso!');
    }

    public function sendWhatsAppMessage()
    {
        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $twilio = new Client($sid, $token);

        $message = $twilio->messages->create(
            'whatsapp:+19786227980', // Teu número VERIFICADO na sandbox
            [
                'from' => 'whatsapp:+14155238886', // Fixo (sandbox)
                'body' => 'Olá! Esta é uma mensagem enviada via WhatsApp com Twilioooooo 🤖'
            ]
        );

        return response()->json([
            'message_sid' => $message->sid,
            'status' => 'Mensagem enviada com sucesso 🚀'
        ]);
    }


}
