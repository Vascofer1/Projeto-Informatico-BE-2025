<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Question;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\EventQuestion;

class EventQuestionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /*$category = $request->query('category');

        $questions = Question::whereNull('category')
            ->orWhere('category', $category)
            ->get();

        return response()->json($questions);*/
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Event $event)
    {
        if ($event->questions()->exists()) {
            return redirect()->route('events.show', $event->id)
                ->with('error', 'A form already exists for this event.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Event $event)
{
    
    $validated = $request->validate([
        'questions' => 'required|array',
        'questions.*.question_id' => 'required|exists:questions,id',
        'questions.*.mandatory' => 'required|boolean',
    ]);

    foreach ($validated['questions'] as $q) {
        $event->questions()->attach($q['question_id'], [
            'mandatory' => $q['mandatory'],
        ]);
    }

    return redirect()->route('events.form.show', $event->id)
        ->with('success', 'Form created successfully.');
}

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
{    

    $questions = $event->questions()->withPivot('mandatory')->get();

    // Eliminar duplicados mantendo a versão obrigatória se existir
    $questions = $questions->groupBy('id')->map(function ($group) {
        return $group->sortByDesc(fn ($q) => $q->pivot->mandatory)->first();
    })->values();

    // Passar apenas os dados relevantes para o front-end
    $questions = $questions->map(function ($q) {
        return [
            'id' => $q->id,
            'name' => $q->name,
            'description' => $q->description,
            'options' => $q->options ?? '[]',
            'mandatory' => $q->pivot->mandatory,
        ];
    });

    return Inertia::render('Events/Form', [
        'event' => $event,
        'questions' => $questions,
        
    ]);
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
