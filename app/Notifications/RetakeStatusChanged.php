<?php

namespace App\Notifications;

use App\Models\RetakeEligibility;
use Illuminate\Notifications\Notification;

class RetakeStatusChanged extends Notification
{
    public function __construct(
        private RetakeEligibility $eligibility,
        private string            $newStatus, // 'eligible', 'approved', 'rejected'
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $moduleName = $this->eligibility->exam?->module?->name ?? 'Module';
        $isApproved = in_array($this->newStatus, ['eligible', 'approved']);

        return [
            'type'  => 'retake_status',
            'icon'  => $isApproved ? '🎓' : '❌',
            'title' => $isApproved
                ? "Droit au Rattrapage — {$moduleName}"
                : "Rattrapage Refusé — {$moduleName}",
            'body'  => $isApproved
                ? "Vous êtes autorisé à passer l'examen de rattrapage pour le module {$moduleName}. Consultez votre espace étudiant pour plus de détails."
                : "Votre demande de rattrapage pour le module {$moduleName} a été refusée. Contactez l'administration pour plus d'informations.",
            'color' => $isApproved ? 'emerald' : 'red',
            'url'   => route('student.retake.index'),
        ];
    }
}
