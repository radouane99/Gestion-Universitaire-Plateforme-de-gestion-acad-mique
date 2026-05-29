<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Exam;
use App\Models\ExamAttendance;
use App\Models\ExamJustification;
use App\Models\DisciplineCase;
use App\Models\RetakeEligibility;
use App\Models\Student;
use App\Models\Setting;
use App\Models\Convocation;
use App\Models\Grade;
use App\Models\ExamSession;
use Illuminate\Support\Facades\DB;

class AcademicPilotingController extends Controller
{
    public function index()
    {
        $settings   = Setting::current();
        $warning    = $settings?->absence_warning_threshold ?? 80;
        $discipline = $settings?->absence_discipline_threshold ?? 120;

        // ── Statistiques absences ──────────────────────────────────────────
        $allStudents = Student::with(['user', 'group.filiere'])->get();

        $studentsAtRisk = $allStudents->filter(fn($s) => $s->absence_score >= $warning);
        $studentsDiscipline = $allStudents->filter(fn($s) => $s->absence_score >= $discipline);

        $totalUnjustifiedHours = Absence::where('is_justified', false)->sum('duration');
        $pendingJustifications = Absence::where('justification_status', 'pending')->count();

        // ── Conseil de discipline ──────────────────────────────────────────
        $disciplineCases = DisciplineCase::with(['student.user', 'student.group'])
            ->whereIn('status', ['open', 'notified'])
            ->orderByDesc('total_unjustified_hours')
            ->get();

        // ── Absences examens ──────────────────────────────────────────────
        $examAbsences = ExamAttendance::with(['exam.module', 'student.user'])
            ->where('status', 'absent')
            ->count();

        $fraudCases = ExamAttendance::where('status', 'fraud')->count();

        // ── Justifications examens en attente ─────────────────────────────
        $pendingExamJustifications = ExamJustification::with([
            'student.user',
            'examAttendance.exam.module',
        ])
        ->where('status', 'pending')
        ->orderByDesc('created_at')
        ->get();

        // ── Rattrapages ───────────────────────────────────────────────────
        $retakesApproved = RetakeEligibility::where('admin_decision', 'approved')->count();
        $retakesPending  = RetakeEligibility::where('status', 'pending')->count();

        // ── Convocations non téléchargées ─────────────────────────────────
        $unconvocated = Convocation::whereIn('status', ['pending', 'generated', 'sent'])->count();

        // ── Examens sans notes ────────────────────────────────────────────
        $examsWithoutGrades = Exam::whereDoesntHave('module', function ($q) {
            $q->whereHas('grades');
        })->count();

        // ── Modules sans notes saisies ────────────────────────────────────
        $modulesWithoutGrades = \App\Models\Module::whereDoesntHave('grades')->count();

        // ── Étudiants à risque détaillés (top 10) ────────────────────────
        $topRiskStudents = $studentsAtRisk
            ->sortByDesc(fn($s) => $s->absence_score)
            ->take(10)
            ->values();

        return view('admin.pilotage.index', compact(
            'settings', 'warning', 'discipline',
            'studentsAtRisk', 'studentsDiscipline',
            'totalUnjustifiedHours', 'pendingJustifications',
            'disciplineCases',
            'examAbsences', 'fraudCases',
            'pendingExamJustifications',
            'retakesApproved', 'retakesPending',
            'unconvocated', 'examsWithoutGrades', 'modulesWithoutGrades',
            'topRiskStudents'
        ));
    }
}
