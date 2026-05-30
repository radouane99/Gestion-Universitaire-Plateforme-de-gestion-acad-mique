<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use App\Traits\SendsEmailNotification;

class AbsenceRecorded extends Notification
{
    use SendsEmailNotification;

    public string $moduleName;
    public string $date;

    public function __construct(string $moduleName, string $date)
    {
        $this->moduleName = $moduleName;
        $this->date = $date;
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'  => 'absence',
            'icon'  => '⚠️',
            'title' => 'Absence enregistrée — ' . $this->moduleName,
            'body'  => 'Une absence a été enregistrée le ' . $this->date . '. Vous pouvez la justifier.',
            'color' => 'amber',
            'url'   => '/student/absences',
        ];
    }
}
