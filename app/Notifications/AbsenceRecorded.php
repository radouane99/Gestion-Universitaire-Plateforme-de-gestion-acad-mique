<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class AbsenceRecorded extends Notification
{
    public string $moduleName;
    public string $date;

    public function __construct(string $moduleName, string $date)
    {
        $this->moduleName = $moduleName;
        $this->date = $date;
    }

    public function via($notifiable): array
    {
        return ['database'];
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
