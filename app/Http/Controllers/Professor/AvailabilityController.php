<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfessorAvailability;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AvailabilityController extends Controller
{
    public function index()
    {
        $professor = Auth::user()->professor;
        
        // Get all upcoming availabilities
        $availabilities = ProfessorAvailability::where('professor_id', $professor->id)
            ->where('available_date', '>=', now()->subDays(7))
            ->orderBy('available_date')
            ->get();

        // Group by exam_week label
        $byWeek = $availabilities->groupBy('exam_week');

        return view('professor.availability.index', compact('availabilities', 'byWeek'));
    }

    public function store(Request $request)
    {
        $professor = Auth::user()->professor;

        $validated = $request->validate([
            'dates'     => 'required|array|min:3',
            'dates.*'   => 'date|after_or_equal:today',
            'exam_week' => 'required|string|max:100',
            'notes'     => 'nullable|string|max:500',
        ], [
            'dates.min' => 'Vous devez sélectionner au moins 3 jours de disponibilité.',
            'dates.*.after_or_equal' => 'Les dates doivent être dans le futur.',
        ]);

        $added = 0;
        foreach ($validated['dates'] as $date) {
            ProfessorAvailability::firstOrCreate(
                [
                    'professor_id'   => $professor->id,
                    'available_date' => $date,
                ],
                [
                    'exam_week' => $validated['exam_week'],
                    'notes'     => $validated['notes'] ?? null,
                ]
            );
            $added++;
        }

        return back()->with('success', "{$added} jour(s) de disponibilité soumis avec succès.");
    }

    public function destroy(ProfessorAvailability $availability)
    {
        $professor = Auth::user()->professor;

        if ($availability->professor_id !== $professor->id) {
            abort(403);
        }

        // Can only delete future dates
        if ($availability->available_date->isPast()) {
            return back()->with('error', 'Impossible de supprimer une date passée.');
        }

        $availability->delete();
        return back()->with('success', 'Disponibilité supprimée.');
    }
}
