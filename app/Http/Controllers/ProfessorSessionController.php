<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfirmedSession;
use App\Models\Professor;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProfessorSessionController extends Controller
{
    /**
     * View for professors to track their worked hours.
     */
    public function professorIndex()
    {
        $professor = Auth::user()->professor;
        if (!$professor) {
            abort(403, 'Unauthorized.');
        }

        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek()->toDateString();
        $endOfWeek = $now->copy()->endOfWeek()->toDateString();
        $startOfMonth = $now->copy()->startOfMonth()->toDateString();
        $endOfMonth = $now->copy()->endOfMonth()->toDateString();

        // 1. Calculate Statistics
        $hoursWeek = ConfirmedSession::where('professor_id', $professor->id)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->sum('duration');

        $hoursMonth = ConfirmedSession::where('professor_id', $professor->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('duration');

        $hoursTotal = ConfirmedSession::where('professor_id', $professor->id)
            ->sum('duration');

        // 2. Fetch Taught/Confirmed Sessions
        $sessions = ConfirmedSession::where('professor_id', $professor->id)
            ->with(['group', 'module'])
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        // 3. Weekly Breakdown
        $weeklyBreakdown = $sessions->groupBy(function ($session) {
            $carbonDate = Carbon::parse($session->date);
            $start = $carbonDate->copy()->startOfWeek()->format('d/m/Y');
            $end = $carbonDate->copy()->endOfWeek()->format('d/m/Y');
            return "Semaine du {$start} au {$end}";
        })->map(function ($group) {
            return [
                'hours' => $group->sum('duration'),
                'sessions' => $group
            ];
        });

        return view('professor.hours.index', compact(
            'hoursWeek',
            'hoursMonth',
            'hoursTotal',
            'sessions',
            'weeklyBreakdown'
        ));
    }

    /**
     * View for administrators to control worked hours of all professors.
     */
    public function adminIndex(Request $request)
    {
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek()->toDateString();
        $endOfWeek = $now->copy()->endOfWeek()->toDateString();
        $startOfMonth = $now->copy()->startOfMonth()->toDateString();
        $endOfMonth = $now->copy()->endOfMonth()->toDateString();

        $query = Professor::with('user');

        // Optional filtering by contract status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $professors = $query->get()->map(function ($prof) use ($startOfWeek, $endOfWeek, $startOfMonth, $endOfMonth) {
            $prof->hours_week = ConfirmedSession::where('professor_id', $prof->id)
                ->whereBetween('date', [$startOfWeek, $endOfWeek])
                ->sum('duration');

            $prof->hours_month = ConfirmedSession::where('professor_id', $prof->id)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum('duration');

            $prof->hours_total = ConfirmedSession::where('professor_id', $prof->id)
                ->sum('duration');

            return $prof;
        });

        return view('admin.hours.index', compact('professors'));
    }

    /**
     * View for administrators to review a specific professor's worked sessions.
     */
    public function adminShow(Professor $professor)
    {
        $professor->load('user');

        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek()->toDateString();
        $endOfWeek = $now->copy()->endOfWeek()->toDateString();
        $startOfMonth = $now->copy()->startOfMonth()->toDateString();
        $endOfMonth = $now->copy()->endOfMonth()->toDateString();

        // 1. Calculate Statistics
        $hoursWeek = ConfirmedSession::where('professor_id', $professor->id)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->sum('duration');

        $hoursMonth = ConfirmedSession::where('professor_id', $professor->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('duration');

        $hoursTotal = ConfirmedSession::where('professor_id', $professor->id)
            ->sum('duration');

        // 2. Fetch Confirmed Sessions
        $sessions = ConfirmedSession::where('professor_id', $professor->id)
            ->with(['group', 'module'])
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        // 3. Weekly Breakdown
        $weeklyBreakdown = $sessions->groupBy(function ($session) {
            $carbonDate = Carbon::parse($session->date);
            $start = $carbonDate->copy()->startOfWeek()->format('d/m/Y');
            $end = $carbonDate->copy()->endOfWeek()->format('d/m/Y');
            return "Semaine du {$start} au {$end}";
        })->map(function ($group) {
            return [
                'hours' => $group->sum('duration'),
                'sessions' => $group
            ];
        });

        return view('admin.hours.show', compact(
            'professor',
            'hoursWeek',
            'hoursMonth',
            'hoursTotal',
            'sessions',
            'weeklyBreakdown'
        ));
    }
}
