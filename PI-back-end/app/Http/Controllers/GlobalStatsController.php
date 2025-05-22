<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GlobalStatsController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'year'); // 'year' ou '6months'
        $monthsBack = $filter === '6months' ? 5 : 11;

        $months = collect(range(0, $monthsBack))->map(function ($i) {
            return now()->subMonths($i)->format('m-Y');
        })->reverse();

        $startDate = now()->subMonths($monthsBack);

        $eventsPerMonth = Event::select(DB::raw("DATE_FORMAT(start_date, '%m-%Y') as month"), DB::raw('count(*) as total'))
            ->where('start_date', '>=', $startDate)
            ->groupBy('month')
            ->pluck('total', 'month');

        $participantsPerMonth = DB::table('participants')
            ->select(DB::raw("DATE_FORMAT(created_at, '%m-%Y') as month"), DB::raw('count(*) as total'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('month')
            ->pluck('total', 'month');

        $events = $months->mapWithKeys(fn($month) => [$month => $eventsPerMonth[$month] ?? 0]);
        $participants = $months->mapWithKeys(fn($month) => [$month => $participantsPerMonth[$month] ?? 0]);

        // Novos dados circulares
        $categoryDistribution = Event::where('start_date', '>=', $startDate)
            ->select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category');

        $typeDistribution = Event::where('start_date', '>=', $startDate)
            ->select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

// ADD: Top 5 eventos com mais participantes confirmados
$topEvents = DB::table('events')
    ->leftJoin('participants', 'events.id', '=', 'participants.event_id')
    ->select('events.id', 'events.name', DB::raw("SUM(CASE WHEN participants.presence = 1 THEN 1 ELSE 0 END) as confirmed"))
    ->where('events.start_date', '>=', $startDate)
    ->groupBy('events.id', 'events.name')
    ->orderByDesc('confirmed')
    ->take(5)
    ->get()
    ->map(function ($event) {
        // Total de participantes (inscritos), independentemente da presença
        $totalParticipants = DB::table('participants')
            ->where('event_id', $event->id)
            ->count();

        $confirmed = $event->confirmed;

        return [
            'name' => $event->name,
            'confirmed' => $confirmed,
            'percentage' => $totalParticipants > 0 ? round(($confirmed / $totalParticipants) * 100, 1) : 0
        ];
    });

return Inertia::render('Statistics', [
    'eventsPerMonth' => $events,
    'participantsPerMonth' => $participants,
    'categoryDistribution' => $categoryDistribution,
    'typeDistribution' => $typeDistribution,
    'selectedFilter' => $filter,
    'averageParticipants' => round($avgParticipants ?? 0, 1),
    'topEvents' => $topEvents
]);
    }
}
