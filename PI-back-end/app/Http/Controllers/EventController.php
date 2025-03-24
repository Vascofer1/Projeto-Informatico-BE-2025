<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::all()->map(function ($event) {
            return [
                'id' => $event->id,
                'name' => $event->name,
                'location' => $event->location,
                'startdate' => $event->start_date,
                'status' => $event->status,
            ];
        });

        return Inertia::render('Events/Index', ['events' => $events]);
    }


    public function filter()
    {
        $today = now()->toDateString(); // Obtém a data de hoje no formato 'YYYY-MM-DD'
        $pastWeek = now()->subDays(7)->toDateString(); // Últimos 7 dias
        $nextWeek = now()->addDays(7)->toDateString(); // Próximos 7 dias
    
        $recentEvents = Event::whereBetween('end_date', [$pastWeek, $today])->get();
    
        $ongoingEvents = Event::where('start_date', '<=', $today)
                              ->where('end_date', '>=', $today)
                              ->get();
    
        $upcomingEvents = Event::whereBetween('start_date', [$today, $nextWeek])->get();
    
        return Inertia::render('Dashboard', [
            'recentEvents' => $recentEvents ?? [],
            'ongoingEvents' => $ongoingEvents ?? [],
            'upcomingEvents' => $upcomingEvents ?? []
        ]);
    }


    public function create()
    {
        return Inertia::render('Events/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'category' => 'required|string',
            'location' => 'required|string',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_date' => 'required|date',
            'end_time' => 'required',
            'image' => 'nullable|image|max:2048',
            'limit_participants' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
        ]);

        // Se houver imagem, processa o upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        Event::create($validated);

        return redirect()->route('events.index')->with('success', 'Evento criado com sucesso!');
    }

    public function showRegistrationPage($id)
    {
        $event = Event::findOrFail($id);
        return Inertia::render('EventRegistration', ['event' => $event]);
    }


    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $event->loadCount('participants'); // Adiciona participants_count ao evento

        return Inertia::render('Events/Show', [
            'event' => $event
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
