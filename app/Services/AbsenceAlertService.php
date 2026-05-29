<?php

namespace App\Services;

use App\Models\Student;
use App\Models\DisciplineCase;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\DisciplineThresholdReached;
use App\Notifications\AbsenceWarningReached;
use Illuminate\Support\Facades\Notification;

class AbsenceAlertService
{
    /**
     * Vérifier et déclencher les alertes après enregistrement d'une absence.
     * Appelé depuis AbsenceController::store() après chaque enregistrement.
     */
    public function checkAndTriggerAlerts(Student $student): void
    {
        $settings   = Setting::current();
        $warning    = $settings?->absence_warning_threshold ?? 80;
        $discipline = $settings?->absence_discipline_threshold ?? 120;

        $score = $student->absence_score; // heures non justifiées

        if ($score >= $discipline) {
            $this->handleDisciplineThreshold($student, $score, $settings);
        } elseif ($score >= $warning) {
            $this->handleWarningThreshold($student, $score, $warning);
        }
    }

    /**
     * Gérer le dépassement du seuil de discipline.
     */
    private function handleDisciplineThreshold(Student $student, float $score, ?Setting $settings): void
    {
        // Créer ou mettre à jour le dossier de discipline
        $case = DisciplineCase::updateOrCreate(
            [
                'student_id' => $student->id,
                'status'     => ['open', 'notified'], // Ne pas recréer si déjà traité
            ],
            [
                'total_unjustified_hours' => $score,
                'status'                  => 'notified',
            ]
        );

        // S'il n'y a pas de dossier existant, créer un nouveau
        if (!$case->wasRecentlyCreated && !in_array($case->status, ['open', 'notified'])) {
            DisciplineCase::create([
                'student_id'              => $student->id,
                'total_unjustified_hours' => $score,
                'status'                  => 'notified',
            ]);
        }

        // Notifier l'étudiant
        $customText = $settings?->discipline_notification_text
            ?? 'Vous avez dépassé le seuil d\'absences non justifiées. Veuillez vous présenter à l\'administration.';

        if ($student->user) {
            $student->user->notify(new DisciplineThresholdReached(
                $score,
                $settings?->absence_discipline_threshold ?? 120,
                $customText
            ));
        }

        // Notifier tous les administrateurs
        $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->get();
        Notification::send($admins, new DisciplineThresholdReached(
            $score,
            $settings?->absence_discipline_threshold ?? 120,
            'L\'étudiant ' . ($student->user?->name ?? '#' . $student->id) . ' a dépassé le seuil d\'absences non justifiées.',
            $student
        ));
    }

    /**
     * Gérer le dépassement du seuil d'avertissement.
     */
    private function handleWarningThreshold(Student $student, float $score, int $warningThreshold): void
    {
        if ($student->user) {
            $student->user->notify(new AbsenceWarningReached($score, $warningThreshold));
        }
    }

    /**
     * Recalculer et mettre à jour tous les dossiers de discipline actifs.
     * Utile après validation d'un justificatif (score peut baisser).
     */
    public function recalculateAfterJustification(Student $student): void
    {
        $settings   = Setting::current();
        $discipline = $settings?->absence_discipline_threshold ?? 120;
        $score      = $student->absence_score;

        // Si le score est redescendu sous le seuil, marquer le dossier comme traité automatiquement
        if ($score < $discipline) {
            DisciplineCase::where('student_id', $student->id)
                ->whereIn('status', ['open', 'notified'])
                ->update([
                    'status'       => 'treated',
                    'treated_at'   => now(),
                    'admin_comment' => 'Résolu automatiquement — score en dessous du seuil après justification.',
                ]);
        }
    }

    /**
     * Obtenir les étudiants à risque (triés par score desc).
     */
    public static function getStudentsAtRisk(): \Illuminate\Support\Collection
    {
        $settings   = Setting::current();
        $warning    = $settings?->absence_warning_threshold ?? 80;
        $discipline = $settings?->absence_discipline_threshold ?? 120;

        return Student::with(['user', 'group.filiere'])->get()
            ->filter(fn($s) => $s->absence_score >= $warning)
            ->sortByDesc(fn($s) => $s->absence_score)
            ->values();
    }

    /**
     * Obtenir les étudiants conseil de discipline.
     */
    public static function getStudentsDiscipline(): \Illuminate\Support\Collection
    {
        $discipline = Setting::get('absence_discipline_threshold', 120);

        return Student::with(['user', 'group.filiere', 'disciplineCases'])->get()
            ->filter(fn($s) => $s->absence_score >= $discipline)
            ->sortByDesc(fn($s) => $s->absence_score)
            ->values();
    }
}
