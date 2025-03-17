<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\EventController;

/*Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');*/

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/events', function () {
    return Inertia::render('Events');
})->name('events');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
