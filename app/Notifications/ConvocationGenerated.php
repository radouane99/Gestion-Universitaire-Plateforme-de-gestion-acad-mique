<?php

namespace App\Notifications;

use App\Models\ExamSession;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ConvocationGenerated extends Notification
{
    use Queueable;

    public function __construct(
        public readonly ExamSession $session,
        public readonly int $count
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'convocation_generated',
            'title'      => 'Convocation disponible',
            'message'    => "Votre convocation pour la session \"{$this->session->name}\" est maintenant disponible ({$this->count} examen(s)). Téléchargez-la depuis votre espace.",
            'url'        => route('student.convocations.index'),
            'session_id' => $this->session->id,
            'count'      => $this->count,
        ];
    }
}
