<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\EventResponsesController;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\ScheduledEmailController;
use App\Mail\ParticipantRegistered;
use Illuminate\Support\Facades\Mail;
use App\Models\Participant;
use App\Models\Event;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\EventQuestionsController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\GlobalStatsController;

/*Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');*/

Route::get('/', function () {
    return Inertia::render('auth/Login');
})->middleware(['guest'])->name('home');

Route::get('dashboard', [EventController::class, 'filter'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');



Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    Route::get('/events/{event}/questions', [QuestionController::class, 'index'])->name('events.questions.index');
    Route::post('/events/{event}/questions', [QuestionController::class, 'store']);

    Route::get('/events/{id}/questions', [EventQuestionsController::class, 'create'])->name('events.questions.create');

    Route::get('/event/{id}/participants', [ParticipantController::class, 'index']);

    Route::get('/eventos/{event}/participantes/export-csv', [ParticipantController::class, 'export']);

    Route::get('/eventos/{event}/participantes/export-pdf', [ParticipantController::class, 'exportpdf']);

    Route::get('/statistics', [GlobalStatsController::class, 'index'])->name('global.stats');
    
    Route::get('/messages/create/{event_id}', [MessageController::class, 'create'])->name('messages.create');

    Route::post('/schedule-email', [MessageController::class, 'scheduleEmail']);
});



Route::get('/events/{event}/form', [EventQuestionsController::class, 'show'])->name('events.form.show');

Route::post('/events/{event}/form', [EventQuestionsController::class, 'store'])->name('events.form.store');

Route::post('/events/{event}/submit-form', [EventResponsesController::class, 'store']);

Route::get('/inscricao/{id}', [EventController::class, 'showRegistrationPage'])->name('events.registration');
Route::post('/inscricao/{id}', [ParticipantController::class, 'store'])->name('participants.store');




Route::post('/schedule-whatsapp', [MessageController::class, 'scheduleWhatsApp']);
Route::get('/test-whatsapp', [MessageController::class, 'sendWhatsAppMessage']);


Route::get('/scheduled-emails', [ScheduledEmailController::class, 'index'])
->name('scheduled-emails.index');
Route::delete('/scheduled-emails/{id}', [ScheduledEmailController::class, 'destroy'])
->name('scheduled-emails.destroy');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
