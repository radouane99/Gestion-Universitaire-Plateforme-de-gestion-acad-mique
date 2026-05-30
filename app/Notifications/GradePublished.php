<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use App\Traits\SendsEmailNotification;

class GradePublished extends Notification
{
    use SendsEmailNotification;

    public string $moduleName;
    public float $finalGrade;

    public function __construct(string $moduleName, float $finalGrade)
    {
        $this->moduleName = $moduleName;
        $this->finalGrade = $finalGrade;
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
