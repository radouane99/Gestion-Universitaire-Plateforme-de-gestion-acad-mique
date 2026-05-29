<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RetakeEligibility;
use App\Models\ExamSession;
use App\Models\ActivityLog;
use App\Notifications\RetakeStatusChanged;
use App\Services\RetakeEligibilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class RetakeEligibilityController extends Controller
{
    public function __construct(private RetakeEligibilityService $service) {}

    /**
     * Liste des étudiants éligibles au rattrapage pour une session.
     */
    public function index(Request $request, ExamSession $session = null)
    {
        $sessions = ExamSession::orderByDesc('start_date')->get();

        $query = RetakeEligibility::with([
            'student.user',
            'student.group.filiere',
            'exam.module',
            'examSession',
            'decidedBy',
        ]);

        if ($session) {
            $query->where('exam_session_id', $session->id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        $eligibilities = $query->orderBy('student_id')->paginate(30);

        $stats = [
            'eligible'     => RetakeEligibility::where('status', 'eligible')->count(),
            'not_eligible' => RetakeEligibility::where('status', 'not_eligible')->count(),
            'pending'      => RetakeEligibility::where('status', 'pending')->count(),
            'approved'     => RetakeEligibility::where('admin_decision', 'approved')->count(),
        ];

        return view('admin.retake.index', compact('eligibilities', 'stats', 'sessions', 'session'));
    }

    /**
     * Approuver le rattrapage pour un étudiant.
     */
    public function approve(Request $request, RetakeEligibility $eligibility)
    {
        $request->validate(['admin_comment' => 'nullable|string|max:500']);

        $eligibility->update([
            'admin_decision' => 'approved',
            'admin_comment'  => $request->admin_comment,
            'decided_by'     => Auth::id(),
            'decided_at'     => now(),
        ]);

        $eligibility->student?->user?->notify(new RetakeStatusChanged($eligibility, 'approved'));

        ActivityLog::log('approved', 'RetakeEligibility', "Rattrapage accordé — étudiant #{$eligibility->student_id}, examen #{$eligibility->exam_id}.");

        return back()->with('success', 'Rattrapage accordé avec succès.');
    }

    /**
     * Refuser le rattrapage.
     */
    public function reject(Request $request, RetakeEligibility $eligibility)
    {
        $request->validate(['admin_comment' => 'required|string|min:5|max:500']);

        $eligibility->update([
            'admin_decision' => 'rejected',
            'status'         => 'not_eligible',
            'admin_comment'  => $request->admin_comment,
            'decided_by'     => Auth::id(),
            'decided_at'     => now(),
        ]);

        $eligibility->student?->user?->notify(new RetakeStatusChanged($eligibility, 'rejected'));

        ActivityLog::log('rejected', 'RetakeEligibility', "Rattrapage refusé — étudiant #{$eligibility->student_id}, examen #{$eligibility->exam_id}.");

        return back()->with('success', 'Rattrapage refusé.');
    }

    /**
     * Export PDF de la liste de rattrapage.
     */
    public function exportPdf(ExamSession $session)
    {
        $eligibilities = $this->service->generateRetakeList($session);

        $pdf = Pdf::loadView('pdf.retake_list', compact('eligibilities', 'session'));

        return $pdf->download("liste_rattrapage_{$session->type}.pdf");
    }

    /**
     * Export Excel de la liste de rattrapage.
     */
    public function exportExcel(ExamSession $session)
    {
        $eligibilities = $this->service->generateRetakeList($session);

        $filename = "liste_rattrapage_{$session->type}_" . now()->format('Ymd') . '.csv';
        $headers  = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($eligibilities) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['N°', 'Étudiant', 'N° Étudiant', 'Groupe', 'Filière', 'Module', 'Raison', 'Décision']);

            foreach ($eligibilities as $i => $e) {
                fputcsv($handle, [
                    $i + 1,
                    $e->student?->user?->name ?? 'N/A',
                    $e->student?->student_number ?? 'N/A',
                    $e->student?->group?->name ?? 'N/A',
                    $e->student?->group?->filiere?->name ?? 'N/A',
                    $e->exam?->module?->name ?? 'N/A',
                    $e->reason_label,
                    $e->admin_decision_label,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
