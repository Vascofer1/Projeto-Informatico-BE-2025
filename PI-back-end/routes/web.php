<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ParticipantController;

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

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
