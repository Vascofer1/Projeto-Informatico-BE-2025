<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $this->updateEventStatus();

    $query = Event::query();

    if ($request->has('search')) {
        $search = $request->input('search');
        $query->whereRaw('LOWER(name) LIKE ?', ["%".strtolower($search)."%"]);
    }

    $events = $query->paginate(12)->withQueryString();

    return Inertia::render('Events/Index', ['events' => $events]);
}


    public function filter()
{
    $this->updateEventStatus();

    $now = now(); // Obtém a data e hora atuais (YYYY-MM-DD HH:MM:SS)
    $today = $now->toDateString(); // Apenas a data (YYYY-MM-DD)
    $pastWeek = $now->copy()->subDays(7)->toDateString(); // Últimos 7 dias
    $nextWeek = $now->copy()->addDays(7)->toDateString(); // Próximos 7 dias

    // Eventos Recentes: Terminaram nos últimos 7 dias (considerando data e hora)
    $recentEvents = Event::where(function ($query) use ($pastWeek, $now) {
        $query->where('end_date', '>=', $pastWeek)
              ->whereRaw("CONCAT(end_date, ' ', end_time) < ?", [$now]); // Só eventos que já terminaram
    })->get();

    // Eventos a Decorrer: Já começaram, mas ainda não terminaram
    $ongoingEvents = Event::where(function ($query) use ($now) {
        $query->whereRaw("CONCAT(start_date, ' ', start_time) <= ?", [$now])
              ->whereRaw("CONCAT(end_date, ' ', end_time) >= ?", [$now]);
    })->get();

    // Eventos Futuros: Ainda não começaram, mas vão começar nos próximos 7 dias
    $upcomingEvents = Event::where(function ($query) use ($today, $nextWeek, $now) {
        $query->where('start_date', '>=', $today)
              ->where('start_date', '<=', $nextWeek)
              ->whereRaw("CONCAT(start_date, ' ', start_time) > ?", [$now]); // Só eventos que ainda não começaram
    })->get();

    return Inertia::render('Dashboard', [
        'user' => Auth::user(),
        'recentEvents' => $recentEvents ?? [],
        'ongoingEvents' => $ongoingEvents ?? [],
        'upcomingEvents' => $upcomingEvents ?? []
    ]);
}


protected function updateEventStatus()
{
    $now = now(); // Data e hora atuais

    Event::all()->each(function ($event) use ($now) {
        $startDateTime = \Carbon\Carbon::parse("{$event->start_date} {$event->start_time}");
        $endDateTime = \Carbon\Carbon::parse("{$event->end_date} {$event->end_time}");

        if ($endDateTime->lessThan($now)) {
            $event->status = 'Finished'; // Evento terminou
        } elseif ($startDateTime->greaterThan($now)) {
            $event->status = 'Upcoming'; // Evento no futuro
        } else {
            $event->status = 'On going'; // Evento está em andamento
        }

        $event->save();
    });
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
    $now = now(); // Obtém a data e hora atuais

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'type' => 'required|string',
        'category' => 'required|string',
        'location' => 'required|string',
        'start_date' => 'required|date|after_or_equal:' . $now->toDateString(),
        'start_time' => 'required|date_format:H:i',
        'end_date' => 'required|date|after_or_equal:start_date',
        'end_time' => 'required|date_format:H:i',
        'image' => 'nullable|image|max:2048',
        'limit_participants' => 'nullable|integer|min:1',
        'description' => 'nullable|string',
    ]);

    // Criar um objeto de data/hora para comparação mais precisa
    $startDateTime = \Carbon\Carbon::parse("{$request->start_date} {$request->start_time}");
    $endDateTime = \Carbon\Carbon::parse("{$request->end_date} {$request->end_time}");

    // Se a data/hora de início for no passado
    if ($startDateTime->lt($now)) {
        return back()->withErrors(['start_date' => 'Start date and time must be in the future.'])->withInput();
    }

    // Se a data/hora de término for antes da data/hora de início
    if ($endDateTime->lte($startDateTime)) {
        return back()->withErrors(['end_time' => 'The end date field must be a date after or equal to start date.'])->withInput();
    }

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('event_images', 'public'); // Salva na pasta storage/app/public/event_images
        $validated['image'] = $path;
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
        $this->updateEventStatus();

        $event->loadCount('participants'); // Adiciona participants_count ao evento

        return Inertia::render('Events/Show', [
            'event' => $event
        ]);
    }


    public function edit(Event $event)
{
    if($event->status == 'Finished') {
        return redirect()->route('events.show', $event->id)->withErrors(['event' => 'Cannot edit a finished event.']);
    }

    if($event->status == 'On going') {
        return redirect()->route('events.show', $event->id)->withErrors(['event' => 'Cannot edit an ongoing event.']);
    }

    return Inertia::render('Events/Edit', ['event' => $event]);
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    $event = Event::findOrFail($id);
    $now = now();

    $validated = $request->validate([
        'name' => 'nullable|string|max:255',
        'type' => 'nullable|string',
        'category' => 'nullable|string',
        'location' => 'nullable|string',
        'start_date' => 'nullable|date|after_or_equal:' . $now->toDateString(),
        'start_time' => 'nullable|date_format:H:i',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'end_time' => 'nullable|date_format:H:i',
        'limit_participants' => 'nullable|integer|min:1',
        'description' => 'nullable|string',
        'image' => 'nullable|image|max:2048',
    ]);

    $startDateTime = \Carbon\Carbon::parse("{$request->start_date} {$request->start_time}");
    $endDateTime = \Carbon\Carbon::parse("{$request->end_date} {$request->end_time}");

    if ($startDateTime->lt($now)) {
        return back()->withErrors(['start_date' => 'Start date and time must be in the future.'])->withInput();
    }

    if ($endDateTime->lte($startDateTime)) {
        return back()->withErrors(['end_time' => 'The end date field must be a date after or equal to start date.'])->withInput();
    }

    // Atualizar imagem se for enviada
    if ($request->hasFile('image')) {
        // Guarda a imagem e obtém o caminho
        $path = $request->file('image')->store('event_images', 'public');
        $validated['image'] = $path;
    }

    foreach ($validated as $key => $value) {
        if (!is_null($value)) {
            $event->$key = $value;
        }
    }

    // Debugging: Ver os dados antes de salvar
    // dd($event);

    $event->save();

    return Inertia::render('Events/Show', [
        'event' => $event,
        'message' => 'Evento atualizado com sucesso!',
    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}