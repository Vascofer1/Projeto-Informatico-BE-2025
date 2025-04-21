<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use Inertia\Inertia;
use App\Models\Event;
use App\Models\EventQuestion;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Event $event)
    {
        if ($event->questions()->exists()) {
            return redirect()->route('events.show', $event->id)
                ->with('error', 'There is already a form created for this event.');
        }

        return Inertia::render('Events/StatisticsForm', [
            'event' => [
                'id' => $event->id,
                'name' => $event->name,
                'type' => $event->type,
                'category' => $event->category,
                'location' => $event->location,
                'start_date' => $event->start_date,
                'start_time' => $event->start_time,
                'end_date' => $event->end_date,
                'end_time' => $event->end_time,
                'description' => $event->description,
                'status' => $event->status,
            ],
            'questions' => Question::whereNull('category')
                ->orWhere('category', $event->category)
                ->get()
                ->map(fn ($q) => [
                    'id' => $q->id,
                    'name' => $q->name,
                    'description' => $q->description,
                    'options' => $q->options ?? [],
                    'category' => $q->category,
                ]),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'options' => 'nullable|array',
            'category' => 'nullable|in:sports,health,tech',
        ]);

        $question = Question::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'options' => $validated['options'] ?? [],
            'category' => $validated['category'] ?? null,
        ]);

        return response()->json($question, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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
