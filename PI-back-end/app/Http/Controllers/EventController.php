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
            'limit_participants' => 'nullable|integer',
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
