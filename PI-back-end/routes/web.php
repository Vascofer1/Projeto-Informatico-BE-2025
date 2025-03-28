<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ParticipantController;
use App\Exports\ParticipantsExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Mail\ParticipantRegistered;
use Illuminate\Support\Facades\Mail;
use App\Models\Participant;
use App\Models\Event;

/*Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');*/

Route::get('/', function () {
    return Inertia::render('auth/Login');
})->middleware(['guest'])->name('home');

Route::get('dashboard', [EventController::class, 'filter'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
Route::post('/events', [EventController::class, 'store'])->name('events.store');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

Route::get('/inscricao/{id}', [EventController::class, 'showRegistrationPage'])->name('events.registration');
Route::post('/inscricao/{id}', [ParticipantController::class, 'store'])->name('participants.store');

Route::get('/event/{id}/participants', [ParticipantController::class, 'index']);

Route::get('/eventos/{event}/participantes/export-csv', [ParticipantController::class, 'export']);

Route::get('/eventos/{event}/participantes/export-pdf', [ParticipantController::class, 'exportpdf']);

Route::get('/test-email', function () {
    // Simulação do evento
    $event = new Event([
        'name' => 'Laravel Workshop',
        'location' => 'Lisboa',
        'date' => '2025-04-15',
    ]);

    // Simulação do participante e associação manual ao evento
    $participant = new Participant([
        'name' => 'John Doe',
        'email' => 'johndoe@example.com',
    ]);
    $participant->event = $event; // adicionamos o evento manualmente

    // Enviar o email
    Mail::to($participant->email)->send(new ParticipantRegistered($participant));

    return 'Email enviado com sucesso!';
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
