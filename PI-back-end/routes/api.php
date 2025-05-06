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
    // Validação básica
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Busca o usuário
    $user = User::where('email', $request->email)->first();

    // Verifica a senha (usando Hash do Laravel)
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['success' => false, 'message' => 'Credenciais inválidas'], 401);
    }

    // Se chegou aqui, login é válido!
    return response()->json([
        'success' => true,
        'user' => $user->only(['id', 'name', 'email']), // Retorna dados não sensíveis
    ]);
});


//ver um participante 
Route::get('/participantes/{id}', [InscricaoController::class, 'verParticipante']);

