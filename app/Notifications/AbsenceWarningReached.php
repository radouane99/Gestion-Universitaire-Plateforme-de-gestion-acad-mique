<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use App\Traits\SendsEmailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class AbsenceWarningReached extends Notification implements ShouldQueue
{
    use Queueable, SendsEmailNotification;

    public function __construct(
        private float $currentHours,
        private int   $threshold,
    ) {}

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
