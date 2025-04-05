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

class MessageController extends Controller
{
    public function create($eventId)
    {
        $event = Event::find($eventId);
        
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
        
        if ($sendTime->isPast()) {
            return response()->json(['error' => 'O horário agendado já passou.'], 422);
        }
        
        dispatch(new SendEventEmail($event, $messageContent))->delay($sendTime);
        \Log::info('Email job dispatched', ['event_id' => $event->id, 'send_time' => $sendTime]);

        return response()->json(['message' => 'E-mail agendado com sucesso!']);
    }


}
