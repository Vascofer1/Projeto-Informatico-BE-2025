<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Participant;
use App\Models\EventResponse;
use Illuminate\Support\Facades\DB;

class EventResponsesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request, $eventId)
{
    $validated = $request->validate([
        'responses.*.event_question_id' => 'required|integer|exists:event_questions,id',
        'responses.*.answer' => 'nullable|string',
    ]);

    foreach ($validated['responses'] as $resp) {
        EventResponse::create([
            'event_question_id' => $resp['event_question_id'],
            'response' => $resp['answer'],
        ]);
    }

    return back()->with('success', 'Respostas submetidas com sucesso.');
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
