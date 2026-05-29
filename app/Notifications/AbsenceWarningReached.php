<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class AbsenceWarningReached extends Notification
{
    public function __construct(
        private float $currentHours,
        private int   $threshold,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'    => 'absence_warning',
            'icon'    => '⚠️',
            'title'   => 'Avertissement — Absences Élevées',
            'body'    => "Vous avez accumulé {$this->currentHours}h d'absences non justifiées. "
                       . "Le seuil du conseil de discipline est fixé à {$this->threshold}h. "
                       . "Veuillez régulariser votre situation.",
            'hours'   => $this->currentHours,
            'threshold' => $this->threshold,
            'color'   => 'amber',
            'url'     => route('student.absences'),
        ];
    }
}
