<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\PVExamen;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PVExamenController extends Controller
{
    public function create(Exam $exam)
    {
        $professor = Auth::user()->professor;
        if (!$professor && !Auth::user()->isAdmin()) {
            abort(403, 'Accès non autorisé.');
        }

        // Verify if professor is proctor for this exam
        if (!Auth::user()->isAdmin() && !$exam->proctors->contains($professor)) {
            abort(403, "Vous n'êtes pas affecté comme surveillant pour cet examen.");
        }

        $pv = $exam->pvExamen;
        return view('professor.pv-examen.create', compact('exam', 'pv'));
    }

    public function store(Request $request, Exam $exam)
    {
        $professor = Auth::user()->professor;
        if (!$professor && !Auth::user()->isAdmin()) {
            abort(403, 'Accès non autorisé.');
        }

        if (!Auth::user()->isAdmin() && !$exam->proctors->contains($professor)) {
            abort(403, "Vous n'êtes pas affecté comme surveillant pour cet examen.");
        }

        $request->validate([
            'presents_count' => 'required|integer|min:0',
            'absents_count' => 'required|integer|min:0',
            'retards_count' => 'required|integer|min:0',
            'incidents' => 'nullable|string',
            'fraude_detected' => 'boolean',
            'fraude_details' => 'required_if:fraude_detected,1|nullable|string',
            'remarques' => 'nullable|string',
        ]);

        $pv = PVExamen::updateOrCreate(
            ['exam_id' => $exam->id],
            [
                'room_id' => $exam->room_id,
                'presents_count' => $request->presents_count,
                'absents_count' => $request->absents_count,
                'retards_count' => $request->retards_count,
                'incidents' => $request->incidents,
                'fraude_detected' => $request->has('fraude_detected'),
                'fraude_details' => $request->fraude_details,
                'remarques' => $request->remarques,
                'submitted_by' => Auth::id(),
                'submitted_at' => now(),
            ]
        );

        return redirect()->route('professor.proctor_convocations.index')
            ->with('success', "Le Procès-Verbal (PV) d'examen a été enregistré avec succès.");
    }

    public function exportPdf(Exam $exam)
    {
        $settings = Setting::first();
        $pv = $exam->pvExamen;

        if (!$pv) {
            return back()->with('error', "Le PV d'examen n'a pas encore été rédigé.");
        }

        $pdf = Pdf::loadView('pdf.pv_examen', compact('exam', 'pv', 'settings'));
        return $pdf->download('pv_examen_' . $exam->module?->code . '_' . now()->format('Ymd_His') . '.pdf');
    }
}
