<?php
namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\ParticipantRegistered;
use Illuminate\Validation\Rule;
use PDF;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;


class ParticipantController extends Controller
{
    public function index(Request $request,$id)
{
    $event = Event::findOrFail($id);
    $participants = Participant::query()
        ->where('event_id', $id)
        ->when($request->status === 'Unconfirmed', fn ($q) => $q->where('status', 'Unconfirmed'))
        ->when($request->status === 'Confirmed', fn ($q) => $q->where('status', 'Confirmed'))
        ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
        ->paginate(7)
        ->withQueryString();

    return inertia('EventParticipants', [
        'event' => $event,
        'participants' => $participants,
        'filters' => $request->only(['status', 'search']),
    ]);
}
   
    


    public function store(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('participants')->where(function ($query) use ($eventId) {
                return $query->where('event_id', $eventId);
            }),
            ],
            'phone' => [
            'required',
            'string',
            'regex:/^\d{9}$/',
            Rule::unique('participants')->where(function ($query) use ($eventId) {
                return $query->where('event_id', $eventId);
            }),
            ],
        ], [
            'email.unique' => 'Já existe uma inscrição com este email para este evento.',
            'phone.unique' => 'Já existe uma inscrição com este telemóvel para este evento.',
        ]);

        

        $participant = Participant::create([
            'event_id' => $event->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone
        ]);

        $codigoUnico = 'PART-' . $participant->id . '-' . Str::random(8);
        
        // Gerar QR Code como PNG
        $qrCodePath = 'qrcodes/' . $participant->id . '.png';
        QrCode::format('png')
          ->size(300)
          ->margin(1)
          ->backgroundColor(255, 255, 255)
          ->generate($codigoUnico, storage_path('app/public/' . $qrCodePath));

          $participant->update([
            'qrcode' => $qrCodePath,
            'code' => $codigoUnico,
        ]);

        $participant->event = $event;

        Mail::to($participant->email)->send(new ParticipantRegistered($participant));

        return redirect()->route('events.registration', ['id' => $event->id])->with('success', 'Inscrição feita com sucesso!');
        
    }

    public function export($eventId)
    {

        $status = request('status');
        $search = request('search');

        $participants = Participant::where('event_id', $eventId)
        ->when($status, fn($query) => $query->where('status', $status))
        ->when($search, fn($query) => $query->where('name', 'like', "%{$search}%"))
        ->get();

        $csvData = "Nome,Email,Telemóvel,Status\n";

        foreach ($participants as $participant) {
            $csvData .= "{$participant->name},{$participant->email},{$participant->phone},{$participant->status}\n";
        }

        $filename = "participantes_evento_{$eventId}.csv";

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    public function exportPDF($eventId)
{
    $status = request('status');
    $search = request('search');

    $participants = Participant::where('event_id', $eventId)
        ->when($status === 'Unconfirmed', fn($query) => $query->where('status', 'Unconfirmed'))
        ->when($status === 'Confirmed', fn($query) => $query->where('status', 'Confirmed'))
        //->when($search ?? '' !== '', fn($query) => $query->where('name', 'like', "%{$search}%"))
        ->get();

    $pdf = Pdf::loadView('pdf.participants', [
        'participants' => $participants,
        'eventName' => Event::find($eventId)?->name ?? '',
    ]);

    return $pdf->download("participantes_evento_{$eventId}.pdf");
}

    

}