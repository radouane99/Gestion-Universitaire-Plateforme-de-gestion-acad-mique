<?php

namespace App\Notifications;

use App\Models\ProfessorConvocation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProfessorConvocationGenerated extends Notification
{
    use Queueable;

    public function __construct(
        public readonly ProfessorConvocation $convocation
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $exam    = $this->convocation->exam;
        $session = $exam->examSession;

        return [
            'type'            => 'professor_convocation_generated',
            'title'           => 'Convocation de surveillance',
            'message'         => "Vous avez été affecté(e) à la surveillance de l'examen \"{$exam->module->name}\" le " . \Carbon\Carbon::parse($exam->date)->format('d/m/Y') . ". Votre convocation est disponible.",
            'url'             => route('professor.proctor_convocations.index'),
            'convocation_ref' => $this->convocation->reference,
            'exam_id'         => $exam->id,
            'session_id'      => $session?->id,
        ];
    }
}
