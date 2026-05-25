<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class GradePublished extends Notification
{
    public string $moduleName;
    public float $finalGrade;

    public function __construct(string $moduleName, float $finalGrade)
    {
        $this->moduleName = $moduleName;
        $this->finalGrade = $finalGrade;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'    => 'grade',
            'icon'    => '📝',
            'title'   => 'Note publiée — ' . $this->moduleName,
            'body'    => 'Votre note finale est ' . number_format($this->finalGrade, 2) . '/20.',
            'color'   => 'blue',
            'url'     => '/student/grades',
        ];
    }
}
