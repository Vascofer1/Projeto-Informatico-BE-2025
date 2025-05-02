<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Event;

class ScheduledEmailController extends Controller
{
    public function index()
{
    $eventFilter = request('event'); // este vai conter o nome do evento

    // Vai buscar todos os jobs agendados
    $allJobs = DB::table('jobs')
        ->where('available_at', '>', Carbon::now()->timestamp)
        ->orderBy('available_at')
        ->get()
        ->map(function ($job) {
            $payload = json_decode($job->payload, true);

            $commandSerialized = $payload['data']['command'];

            try {
                $command = unserialize($commandSerialized);
            } catch (\Throwable $e) {
                return null;
            }

            $eventName = null;
            if (isset($command->event->id)) {
                $event = Event::find($command->event->id);
                $eventName = $event?->name ?? '(Evento não encontrado)';
            }

            return [
                'id' => $job->id,
                'event_id' => $command->event->id ?? null,
                'event_name' => $eventName,
                'message' => $command->messageContent ?? '(sem conteúdo)',
                'send_qr' => $command->sendQr ? 'Yes' : 'No',
                'send_at' => Carbon::createFromTimestamp($job->available_at)->addHour()->format('d/m/Y H:i'),
            ];
        })
        ->filter(); // remove nulls

    // Aplica o filtro por nome se existir
    if ($eventFilter) {
        $allJobs = $allJobs->filter(function ($job) use ($eventFilter) {
            return str_contains(strtolower($job['event_name']), strtolower($eventFilter));
        });
    }

    return Inertia::render('ScheduledEmails', [
        'jobs' => $allJobs->values(), // limpa as keys do collection
        'filter' => $eventFilter,
    ]);
}



    public function destroy($id)
    {
        DB::table('jobs')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Mensagem agendada cancelada com sucesso!');
    }
}

