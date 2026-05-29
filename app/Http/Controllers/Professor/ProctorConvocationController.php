<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfessorConvocation;
use Illuminate\Support\Facades\Auth;

class ProctorConvocationController extends Controller
{
    /**
     * Show all professor surveillance convocations.
     */
    public function index()
    {
        $professor = Auth::user()->professor;

        if (!$professor) {
            abort(403, 'Profil professeur introuvable.');
        }

        $convocations = ProfessorConvocation::where('professor_id', $professor->id)
            ->with(['exam.module', 'exam.room', 'exam.group.filiere', 'exam.examSession'])
            ->orderBy('created_at', 'desc')
            ->get();

        $upcoming = $convocations->filter(fn ($c) => \Carbon\Carbon::parse($c->exam->date)->isFuture() || \Carbon\Carbon::parse($c->exam->date)->isToday())->values();
        $past     = $convocations->filter(fn ($c) => \Carbon\Carbon::parse($c->exam->date)->isPast() && !\Carbon\Carbon::parse($c->exam->date)->isToday())->values();

        // Stats
        $totalAssigned = $convocations->count();
        $confirmed     = $convocations->where('status', 'confirmed')->count();
        $pending       = $convocations->whereIn('status', ['pending', 'generated', 'sent'])->count();

        return view('professor.proctor_convocations.index', compact(
            'upcoming',
            'past',
            'totalAssigned',
            'confirmed',
            'pending'
        ));
    }

    /**
     * Download a professor surveillance convocation as PDF.
     */
    public function download(ProfessorConvocation $convocation)
    {
        $professor = Auth::user()->professor;

        // Security: only the assigned professor can download
        if ($convocation->professor_id !== $professor->id) {
            abort(403, 'Accès non autorisé à cette convocation.');
        }

        $convocation->load([
            'professor.user',
            'exam.module',
            'exam.room',
            'exam.group.filiere',
            'exam.examSession.academicYear',
            'exam.proctors',
        ]);

        // Mark as downloaded (only if not already confirmed)
        if (in_array($convocation->status, ['pending', 'generated', 'sent'])) {
            $convocation->update(['status' => 'downloaded']);
        }

        // Get all surveillance convocations for this professor in this session (for the PDF table)
        $allConvocations = ProfessorConvocation::where('professor_id', $professor->id)
            ->whereHas('exam', fn ($q) =>
                $q->where('exam_session_id', $convocation->exam->exam_session_id)
            )
            ->with(['exam.module', 'exam.room', 'exam.group'])
            ->get()
            ->sortBy(fn ($c) => $c->exam->date . ' ' . $c->exam->start_time);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('convocations.proctor_pdf', compact('convocation', 'allConvocations'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('surveillance_' . $convocation->reference . '.pdf');
    }

    /**
     * Professor confirms receipt of their surveillance convocation.
     */
    public function confirm(ProfessorConvocation $convocation)
    {
        $professor = Auth::user()->professor;

        // Security: only the assigned professor can confirm
        if ($convocation->professor_id !== $professor->id) {
            abort(403, 'Accès non autorisé.');
        }

        if ($convocation->status === 'confirmed') {
            return back()->with('info', 'Vous avez déjà confirmé réception de cette convocation.');
        }

        $convocation->markAsConfirmed();

        return back()->with('success', 'Réception confirmée. Merci !');
    }
}
