<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamJustification;
use App\Models\ActivityLog;
use App\Notifications\ExamAbsenceJustificationReviewed;
use App\Services\RetakeEligibilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamJustificationController extends Controller
{
    public function __construct(private RetakeEligibilityService $retakeService) {}

    /**
     * Liste des justifications examens — admin.
     */
    public function index(Request $request)
    {
        $query = ExamJustification::with([
            'student.user',
            'student.group',
            'examAttendance.exam.module',
            'reviewedBy',
        ])->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $justifications = $query->paginate(20);

        $stats = [
            'pending'  => ExamJustification::where('status', 'pending')->count(),
            'approved' => ExamJustification::where('status', 'approved')->count(),
            'rejected' => ExamJustification::where('status', 'rejected')->count(),
        ];

        return view('admin.exam-justifications.index', compact('justifications', 'stats'));
    }

    /**
     * Approuver une justification → rendre éligible au rattrapage.
     */
    public function approve(Request $request, ExamJustification $justification)
    {
        $request->validate([
            'admin_comment' => 'nullable|string|max:500',
        ]);

        $justification->update([
            'status'       => 'approved',
            'admin_comment' => $request->admin_comment,
            'reviewed_by'  => Auth::id(),
            'reviewed_at'  => now(),
        ]);

        // Déclencher éligibilité rattrapage
        $this->retakeService->grantEligibilityFromJustification($justification);

        // Notifier l'étudiant
        $moduleName = $justification->examAttendance?->exam?->module?->name ?? 'Module';
        $justification->student?->user?->notify(
            new ExamAbsenceJustificationReviewed($moduleName, 'approved', $request->admin_comment ?? '')
        );

        ActivityLog::log(
            'approved',
            'ExamJustification',
            "Justification examen #{$justification->id} approuvée. Rattrapage accordé."
        );

        return back()->with('success', 'Justification approuvée. L\'étudiant est éligible au rattrapage.');
    }

    /**
     * Refuser une justification → pas de droit au rattrapage.
     */
    public function reject(Request $request, ExamJustification $justification)
    {
        $request->validate([
            'admin_comment' => 'required|string|min:5|max:500',
        ]);

        $justification->update([
            'status'        => 'rejected',
            'admin_comment' => $request->admin_comment,
            'reviewed_by'   => Auth::id(),
            'reviewed_at'   => now(),
        ]);

        // Refuser le rattrapage
        $this->retakeService->denyEligibilityFromJustification($justification);

        // Notifier l'étudiant
        $moduleName = $justification->examAttendance?->exam?->module?->name ?? 'Module';
        $justification->student?->user?->notify(
            new ExamAbsenceJustificationReviewed($moduleName, 'rejected', $request->admin_comment)
        );

        ActivityLog::log(
            'rejected',
            'ExamJustification',
            "Justification examen #{$justification->id} refusée."
        );

        return back()->with('success', 'Justification refusée. L\'étudiant n\'a pas le droit au rattrapage.');
    }

    /**
     * Télécharger le fichier de justification.
     */
    public function downloadFile(ExamJustification $justification)
    {
        if (!$justification->justification_path) {
            abort(404, 'Aucun fichier déposé.');
        }

        $path = storage_path('app/' . $justification->justification_path);
        if (!file_exists($path)) {
            abort(404, 'Fichier introuvable.');
        }

        return response()->download($path);
    }
}
