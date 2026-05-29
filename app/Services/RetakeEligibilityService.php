<?php

namespace App\Services;

use App\Models\ExamAttendance;
use App\Models\ExamJustification;
use App\Models\RetakeEligibility;
use App\Models\Grade;
use App\Models\Setting;
use App\Models\Student;
use App\Models\ExamSession;
use App\Models\Exam;
use App\Notifications\RetakeStatusChanged;

class RetakeEligibilityService
{
    /**
     * Évaluer l'éligibilité au rattrapage après enregistrement d'une présence examen.
     * Règles:
     * - absent + justification approuvée → eligible
     * - absent + pas de justification    → not_eligible
     * - présent + note < seuil min       → eligible (calculé après saisie note)
     */
    public function evaluateAfterExamAttendance(ExamAttendance $attendance): void
    {
        $student = $attendance->student;
        $exam    = $attendance->exam;

        if (!$student || !$exam) return;

        if ($attendance->isAbsent()) {
            // Absent → éligibilité en attente de justification
            RetakeEligibility::updateOrCreate(
                ['student_id' => $student->id, 'exam_id' => $exam->id],
                [
                    'exam_session_id' => $exam->exam_session_id ?? null,
                    'reason'          => 'exam_absence_justified',
                    'status'          => 'not_eligible', // Par défaut, pas de droit
                ]
            );
        } elseif ($attendance->status === 'fraud') {
            // Fraude → jamais éligible
            RetakeEligibility::updateOrCreate(
                ['student_id' => $student->id, 'exam_id' => $exam->id],
                [
                    'exam_session_id' => $exam->exam_session_id ?? null,
                    'reason'          => 'admin_decision',
                    'status'          => 'not_eligible',
                    'admin_decision'  => 'rejected',
                    'admin_comment'   => 'Fraude détectée lors de l\'examen.',
                ]
            );
        } else {
            // Présent → vérifier la note
            $this->evaluateByGrade($student, $exam);
        }
    }

    /**
     * Évaluer l'éligibilité basée sur la note finale.
     * Si note < seuil min → éligible automatiquement.
     */
    public function evaluateByGrade(Student $student, Exam $exam): void
    {
        $minGrade = (float) Setting::get('retake_min_grade', 10.0);

        $grade = Grade::where('student_id', $student->id)
            ->where('module_id', $exam->module_id)
            ->first();

        if (!$grade || $grade->final_grade === null) return;

        if ($grade->final_grade < $minGrade) {
            $eligibility = RetakeEligibility::updateOrCreate(
                ['student_id' => $student->id, 'exam_id' => $exam->id],
                [
                    'exam_session_id' => $exam->exam_session_id ?? null,
                    'reason'          => 'low_grade',
                    'status'          => 'eligible',
                    'admin_decision'  => 'approved', // Automatique pour note faible
                ]
            );

            // Notifier l'étudiant
            if ($student->user) {
                $student->user->notify(new RetakeStatusChanged($eligibility, 'eligible'));
            }
        } else {
            // Note suffisante → pas de rattrapage
            RetakeEligibility::updateOrCreate(
                ['student_id' => $student->id, 'exam_id' => $exam->id],
                [
                    'exam_session_id' => $exam->exam_session_id ?? null,
                    'reason'          => 'low_grade',
                    'status'          => 'not_eligible',
                    'admin_decision'  => 'rejected',
                ]
            );
        }
    }

    /**
     * Traiter la justification approuvée → rendre l'étudiant éligible au rattrapage.
     */
    public function grantEligibilityFromJustification(ExamJustification $justification): void
    {
        $attendance = $justification->examAttendance;
        if (!$attendance) return;

        $eligibility = RetakeEligibility::updateOrCreate(
            ['student_id' => $justification->student_id, 'exam_id' => $attendance->exam_id],
            [
                'exam_session_id' => $attendance->exam?->exam_session_id ?? null,
                'reason'          => 'exam_absence_justified',
                'status'          => 'eligible',
                'admin_decision'  => 'approved',
                'admin_comment'   => 'Justification d\'absence approuvée.',
                'decided_by'      => auth()->id(),
                'decided_at'      => now(),
            ]
        );

        // Notifier l'étudiant
        $student = $justification->student;
        if ($student?->user) {
            $student->user->notify(new RetakeStatusChanged($eligibility, 'approved'));
        }
    }

    /**
     * Refuser l'éligibilité au rattrapage (justification refusée).
     */
    public function denyEligibilityFromJustification(ExamJustification $justification): void
    {
        $attendance = $justification->examAttendance;
        if (!$attendance) return;

        $eligibility = RetakeEligibility::updateOrCreate(
            ['student_id' => $justification->student_id, 'exam_id' => $attendance->exam_id],
            [
                'exam_session_id' => $attendance->exam?->exam_session_id ?? null,
                'reason'          => 'exam_absence_justified',
                'status'          => 'not_eligible',
                'admin_decision'  => 'rejected',
                'admin_comment'   => 'Justification refusée par l\'administration.',
                'decided_by'      => auth()->id(),
                'decided_at'      => now(),
            ]
        );

        // Notifier l'étudiant
        $student = $justification->student;
        if ($student?->user) {
            $student->user->notify(new RetakeStatusChanged($eligibility, 'rejected'));
        }
    }

    /**
     * Générer la liste des étudiants autorisés au rattrapage pour une session.
     */
    public function generateRetakeList(ExamSession $session): \Illuminate\Database\Eloquent\Collection
    {
        return RetakeEligibility::with([
            'student.user',
            'student.group.filiere',
            'exam.module',
        ])
        ->where('exam_session_id', $session->id)
        ->where(function ($q) {
            $q->where('status', 'eligible')
              ->orWhere('admin_decision', 'approved');
        })
        ->orderBy('student_id')
        ->get();
    }
}
