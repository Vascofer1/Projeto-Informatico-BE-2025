<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;


class EventController extends Controller
{
    public function index()
    {
        $now = now();
        $twoHoursLater = $now->copy()->addHours(2);

        $eventosAtivos = Event::where('status', 'Upcoming')
            ->whereBetween('start_time', [$now, $twoHoursLater])
            ->get();

        return response()->json($eventosAtivos);
    }

    public function show($id)
    {
        $evento = Event::findOrFail($id);
        $evento->loadCount('participants');
        $evento->loadCount([
            'participants as confirmed_count' => fn($q) => $q->where('status', 'Confirmed'),
            'participants as waiting_count' => fn($q) => $q->where('status', 'Unconfirmed'),
        ]);
        return response()->json($evento);
    }

}
