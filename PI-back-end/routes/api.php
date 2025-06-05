<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\InscricaoController;
use App\Http\Controllers\Api\AuthController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

Route::get('/eventos', [EventController::class, 'index']);
Route::get('/eventos/{id}', [EventController::class, 'show']);

Route::post('/eventos/{id}/inscrever', [InscricaoController::class, 'store']);

Route::post('/validar-qrcode', [InscricaoController::class, 'validateQrCode']);

Route::get('/eventos/{eventoId}/participantes', [InscricaoController::class, 'listarPorEvento']);

Route::post('/mobile/check-login', function (Request $request) {
    
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    
    $user = User::where('email', $request->email)->first();

    
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['success' => false, 'message' => 'Credenciais inválidas'], 401);
    }

    
    return response()->json([
        'success' => true,
        'user' => $user->only(['id', 'name', 'email']), 
    ]);
});


//ver um participante 
Route::get('/participantes/{id}', [InscricaoController::class, 'verParticipante']);

