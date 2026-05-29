<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Convocation;
use App\Models\ExamSession;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Professor;
use App\Models\ProfessorConvocation;
use App\Models\AcademicYear;
use App\Models\Filiere;
use App\Mail\ConvocationMail;
use App\Mail\ProfessorConvocationMail;
use App\Notifications\ConvocationGenerated;
use App\Notifications\ProfessorConvocationGenerated;
use App\Services\ProctorAssignmentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ConvocationController extends Controller
{
    // ─── Admin Dashboard ──────────────────────────────────────────────────────

    /**
     * Main admin convocations dashboard.
     */
    public function index(Request $request)
    {
        $currentYear  = AcademicYear::where('is_current', true)->first();
        $examSessions = $currentYear ? $currentYear->examSessions : collect();
        $filieres     = Filiere::orderBy('name')->get();

        $selectedSessionId = $request->input('session_id', $examSessions->first()?->id);
        $selectedSession   = $selectedSessionId ? ExamSession::find($selectedSessionId) : null;

        // ── Student convocations stats ────────────────────────────────────────
        $studentStats = [
            'total'      => 0,
            'pending'    => 0,
            'generated'  => 0,
            'sent'       => 0,
            'downloaded' => 0,
        ];

        // ── Professor convocations stats ──────────────────────────────────────
        $professorStats = [
            'total'     => 0,
            'pending'   => 0,
            'generated' => 0,
            'sent'      => 0,
            'confirmed' => 0,
        ];

        $studentConvocations    = collect();
        $professorConvocations  = collect();
        $professorAvailabilities = collect();
        $examsWithoutProctors   = collect();

        if ($selectedSession) {
            // Student convocations
            $studentConvocations = Convocation::whereHas('exam', fn ($q) =>
                    $q->where('exam_session_id', $selectedSession->id)
                )
                ->with(['student.user', 'student.group.filiere', 'exam.module', 'exam.room'])
                ->when($request->filled('filiere_id'), fn ($q) =>
                    $q->whereHas('exam.group', fn ($qq) =>
                        $qq->where('filiere_id', $request->filiere_id)
                    )
                )
                ->when($request->filled('status'), fn ($q) =>
                    $q->where('status', $request->status)
                )
                ->orderBy('created_at', 'desc')
                ->paginate(30)
                ->withQueryString();

            // Student stats
            $rawStats = Convocation::whereHas('exam', fn ($q) =>
                    $q->where('exam_session_id', $selectedSession->id)
                )
                ->selectRaw('status, count(*) as cnt')
                ->groupBy('status')
                ->pluck('cnt', 'status');

            $studentStats = [
                'total'      => $rawStats->sum(),
                'pending'    => $rawStats->get('pending', 0),
                'generated'  => $rawStats->get('generated', 0),
                'sent'       => $rawStats->get('sent', 0),
                'downloaded' => $rawStats->get('downloaded', 0),
            ];

            // Professor convocations
            $professorConvocations = ProfessorConvocation::whereHas('exam', fn ($q) =>
                    $q->where('exam_session_id', $selectedSession->id)
                )
                ->with(['professor.user', 'exam.module', 'exam.room', 'exam.group'])
                ->orderBy('created_at', 'desc')
                ->get();

            $professorStats = [
                'total'     => $professorConvocations->count(),
                'pending'   => $professorConvocations->where('status', 'pending')->count(),
                'generated' => $professorConvocations->where('status', 'generated')->count(),
                'sent'      => $professorConvocations->where('status', 'sent')->count(),
                'confirmed' => $professorConvocations->where('status', 'confirmed')->count(),
            ];

            // Professor availabilities for this session window
            $professorAvailabilities = Professor::with([
                'user',
                'availabilities' => fn ($q) =>
                    $q->whereBetween('available_date', [
                        $selectedSession->start_date ?? now()->toDateString(),
                        $selectedSession->end_date   ?? now()->addMonths(3)->toDateString(),
                    ])->orderBy('available_date')
            ])
            ->get()
            ->filter(fn ($p) => $p->availabilities->isNotEmpty())
            ->values();

            // Exams without enough proctors
            $examsWithoutProctors = $selectedSession->exams()
                ->with(['module', 'group', 'room', 'proctors'])
                ->get()
                ->filter(fn ($exam) => $exam->proctors->count() === 0)
                ->values();
        }

        return view('admin.convocations.index', compact(
            'examSessions',
            'filieres',
            'selectedSession',
            'selectedSessionId',
            'studentConvocations',
            'professorConvocations',
            'studentStats',
            'professorStats',
            'professorAvailabilities',
            'examsWithoutProctors'
        ));
    }

    // ─── Student Convocations ────────────────────────────────────────────────

    /**
     * Generate convocations for ALL students of ALL exams in a session.
     */
    public function generateForSession(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:exam_sessions,id',
        ]);

        $session = ExamSession::with('exams.group.students.user')->findOrFail($request->session_id);
        $totalGenerated = 0;
        $totalSkipped   = 0;

        DB::transaction(function () use ($session, &$totalGenerated, &$totalSkipped) {
            foreach ($session->exams as $exam) {
                $students = Student::where('group_id', $exam->group_id)
                    ->with(['user', 'group.filiere'])
                    ->get();

                foreach ($students as $student) {
                    $exists = Convocation::where('exam_id', $exam->id)
                        ->where('student_id', $student->id)
                        ->exists();

                    if ($exists) {
                        $totalSkipped++;
                        continue;
                    }

                    Convocation::create([
                        'exam_id'    => $exam->id,
                        'student_id' => $student->id,
                        'reference'  => Convocation::generateReference(),
                        'status'     => 'generated',
                    ]);

                    $totalGenerated++;
                }
            }
        });

        // Notify students (group by student user, once per session)
        $this->notifyStudentsForSession($session);

        $msg = "{$totalGenerated} convocation(s) générée(s)";
        if ($totalSkipped) {
            $msg .= " ({$totalSkipped} déjà existantes ignorées)";
        }

        return back()->with('success', $msg . '.');
    }

    /**
     * Send emails for all student convocations that haven't been sent yet for a session.
     */
    public function sendForSession(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:exam_sessions,id',
        ]);

        $session = ExamSession::findOrFail($request->session_id);

        $convocations = Convocation::whereHas('exam', fn ($q) =>
                $q->where('exam_session_id', $session->id)
            )
            ->whereNull('sent_at')
            ->with(['exam.module', 'exam.room', 'exam.proctors.user', 'student.user', 'student.group.filiere'])
            ->get();

        $count = 0;
        $errors = 0;

        foreach ($convocations as $convocation) {
            try {
                $pdfContent = \Barryvdh\DomPDF\Facade\Pdf::loadView('convocations.pdf', compact('convocation'))
                    ->output();

                Mail::to($convocation->student->user->email)
                    ->send(new ConvocationMail($convocation, $pdfContent));

                $convocation->update(['status' => 'sent', 'sent_at' => now()]);
                $count++;
            } catch (\Exception $e) {
                Log::error("Erreur envoi convocation {$convocation->reference}: " . $e->getMessage());
                $errors++;
            }
        }

        $msg = "{$count} email(s) envoyé(s) avec succès.";
        if ($errors) {
            $msg .= " {$errors} erreur(s) — voir les logs.";
        }

        return back()->with('success', $msg);
    }

    // ─── Professor Convocations ───────────────────────────────────────────────

    /**
     * Auto-assign proctors to all exams in a session.
     */
    public function autoAssignProctors(Request $request, ProctorAssignmentService $service)
    {
        $request->validate([
            'session_id' => 'required|exists:exam_sessions,id',
        ]);

        $result = $service->assignForSession($request->session_id);

        $msg = "Affectation automatique : {$result['assigned']} surveillant(s) affecté(s), {$result['skipped']} examen(s) déjà couverts.";
        if ($result['failed'] > 0) {
            $msg .= " ⚠️ {$result['failed']} examen(s) sans surveillant disponible.";
            return back()
                ->with('warning', $msg)
                ->with('assignment_errors', $result['errors']);
        }

        return back()->with('success', $msg);
    }

    /**
     * Generate professor convocation records for all proctors in a session.
     */
    public function generateProfessors(Request $request, ProctorAssignmentService $service)
    {
        $request->validate([
            'session_id' => 'required|exists:exam_sessions,id',
        ]);

        $result = $service->generateConvocationsForSession($request->session_id);

        // Notify each professor
        $this->notifyProfessorsForSession($request->session_id);

        return back()->with('success', "{$result['generated']} convocation(s) professeur générée(s). {$result['skipped']} ignorée(s) (déjà existantes).");
    }

    /**
     * Send emails to professors with pending surveillance convocations.
     */
    public function sendProfessors(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:exam_sessions,id',
        ]);

        $convocations = ProfessorConvocation::whereHas('exam', fn ($q) =>
                $q->where('exam_session_id', $request->session_id)
            )
            ->whereNull('sent_at')
            ->with(['professor.user', 'exam.module', 'exam.room', 'exam.group.filiere', 'exam.examSession.academicYear'])
            ->get();

        $count  = 0;
        $errors = 0;

        foreach ($convocations as $convocation) {
            try {
                $pdfContent = \Barryvdh\DomPDF\Facade\Pdf::loadView('convocations.proctor_pdf', compact('convocation'))
                    ->setPaper('a4', 'portrait')
                    ->output();

                Mail::to($convocation->professor->user->email)
                    ->send(new ProfessorConvocationMail($convocation, $pdfContent));

                $convocation->update(['status' => 'sent', 'sent_at' => now()]);
                $count++;
            } catch (\Exception $e) {
                Log::error("Erreur envoi conv. prof {$convocation->reference}: " . $e->getMessage());
                $errors++;
            }
        }

        $msg = "{$count} email(s) professeur envoyé(s).";
        if ($errors) {
            $msg .= " {$errors} erreur(s).";
        }

        return back()->with('success', $msg);
    }

    /**
     * View professor availabilities for a session.
     */
    public function professorAvailabilities(Request $request)
    {
        $currentYear  = AcademicYear::where('is_current', true)->first();
        $examSessions = $currentYear ? $currentYear->examSessions : collect();
        $selectedSession = $request->filled('session_id')
            ? ExamSession::find($request->session_id)
            : $examSessions->first();

        $professors = Professor::with([
            'user',
            'availabilities' => fn ($q) =>
                $q->when($selectedSession, fn ($q2) =>
                    $q2->whereBetween('available_date', [
                        $selectedSession->start_date ?? now()->toDateString(),
                        $selectedSession->end_date   ?? now()->addMonths(3)->toDateString(),
                    ])
                )->orderBy('available_date')
        ])->get()->sortBy('user.name')->values();

        return view('admin.convocations.availabilities', compact(
            'professors',
            'examSessions',
            'selectedSession'
        ));
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private function notifyStudentsForSession(ExamSession $session): void
    {
        $studentIds = Convocation::whereHas('exam', fn ($q) =>
            $q->where('exam_session_id', $session->id)
        )->distinct()->pluck('student_id');

        $students = Student::whereIn('id', $studentIds)->with('user')->get();
        $count = $session->exams->count();

        foreach ($students as $student) {
            try {
                $student->user->notify(new ConvocationGenerated($session, $count));
            } catch (\Exception $e) {
                Log::error("Notification student {$student->id}: " . $e->getMessage());
            }
        }
    }

    private function notifyProfessorsForSession(int $sessionId): void
    {
        $convocations = ProfessorConvocation::whereHas('exam', fn ($q) =>
            $q->where('exam_session_id', $sessionId)
        )->with('professor.user')->get();

        foreach ($convocations as $conv) {
            try {
                $conv->professor->user->notify(new ProfessorConvocationGenerated($conv));
            } catch (\Exception $e) {
                Log::error("Notification prof {$conv->professor_id}: " . $e->getMessage());
            }
        }
    }
}
