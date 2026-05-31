<?php

namespace App\Notifications;

use App\Models\Student;
use Illuminate\Notifications\Notification;
use App\Traits\SendsEmailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class DisciplineThresholdReached extends Notification implements ShouldQueue
{
    use Queueable, SendsEmailNotification;

    public function __construct(
        private float   $currentHours,
        private int     $threshold,
        private string  $message,
        private ?Student $targetStudent = null, // Fourni quand notif envoyée aux admins
    ) {}

    public function toDatabase($notifiable): array
    {
        $isAdmin = $this->targetStudent !== null;

        return [
            'type'    => 'discipline_alert',
            'icon'    => '🚨',
            'title'   => $isAdmin
                ? 'Alerte Conseil de Discipline — ' . ($this->targetStudent?->user?->name ?? 'Étudiant')
                : 'Alerte — Conseil de Discipline',
            'body'    => $this->message,
            'hours'   => $this->currentHours,
            'threshold' => $this->threshold,
            'color'   => 'red',
            'url'     => $isAdmin
                ? route('admin.discipline.index')
                : route('student.absences'),
        ];
    }
}
