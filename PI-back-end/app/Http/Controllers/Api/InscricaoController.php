<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\Event;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Response;


class InscricaoController extends Controller
{
    public function store(Request $request, $id)
{
    $validated = $request->validate([
        'name' => 'required|string',
        'email' => [
            'required', 'email',
            Rule::unique('participants')->where(function ($query) use ($id) {
                return $query->where('event_id', $id);
            }),
        ],
        'phone' => [
            'required',
            Rule::unique('participants')->where(function ($query) use ($id) {
                return $query->where('event_id', $id);
            }),
        ],
    ],
    [
        'name.required' => 'O nome é obrigatório.',
        'email.required' => 'O email é obrigatório.',
        'email.email' => 'O email deve ser um endereço de email válido.',
        'email.unique' => 'Este email já está inscrito para este evento.',
        'phone.required' => 'O telefone é obrigatório.',
        'phone.unique' => 'Este telefone já está inscrito para este evento.',
    ]);

    Participant::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'phone' => $validated['phone'],
        'presence' => 1,
        'event_id' => $id,
        'status' => 'Confirmed',
        'qrcode' => null,
    ]);

    return response()->json(['message' => 'Participante inscrito com sucesso!'], 201);
}
    

    //validar qrcode
    public function validateQrCode(Request $request)
    {
        $code = $request->input('code');

        $participant = Participant::where('code', $request->code)
            ->where('event_id', $request->event_id)
            ->first();


        if (!$participant) {
            return response()->json(['error' => 'Participante não encontrado no evento'], 404);
        }

        $participant->status = 'Confirmed';
        $participant->save();

        return response()->json([
            'nome' => $participant->name,
            'status' => $participant->status,
        ]);
    }


    public function listarPorEvento($eventoId, Request $request)
    {
        $participants = Participant::where('event_id', $eventoId)->get();

        return response()->json($participants);
    }

    public function verParticipante($id)
    {
        $participant = Participant::findOrFail($id);
        return response()->json($participant);
    }
}
