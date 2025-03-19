<?php
namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Event;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function index($id)
{
    $event = Event::findOrFail($id);
    $participants = Participant::where('event_id', $id)->get();

    return inertia('EventParticipants', [
        'event' => $event,
        'participants' => $participants
    ]);
}

    public function store(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:participants,email',
            'phone' => 'required|string|max:20',
        ]);

        Participant::create([
            'event_id' => $event->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return redirect()->route('events.registration', ['id' => $event->id])->with('success', 'Inscrição feita com sucesso!');
    }
}
