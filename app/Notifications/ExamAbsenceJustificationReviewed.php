<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class ExamAbsenceJustificationReviewed extends Notification
{
    public function __construct(
        private string $moduleName,
        private string $status,       // 'approved' | 'rejected'
        private string $adminComment = '',
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $isApproved = $this->status === 'approved';

        return [
            'type'  => 'exam_justification_review',
            'icon'  => $isApproved ? '✅' : '❌',
            'title' => $isApproved
                ? "Justification Acceptée — {$this->moduleName}"
                : "Justification Refusée — {$this->moduleName}",
            'body'  => $isApproved
                ? "Votre justification d'absence à l'examen de {$this->moduleName} a été acceptée. Vous êtes éligible au rattrapage."
                : "Votre justification d'absence à l'examen de {$this->moduleName} a été refusée."
                  . ($this->adminComment ? " Motif : {$this->adminComment}" : ''),
            'color' => $isApproved ? 'emerald' : 'red',
            'url'   => route('student.retake.index'),
        ];
    }
}
