<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class RequestUpdated extends Notification
{
    public string $requestType;
    public string $status;

    public function __construct(string $requestType, string $status)
    {
        $this->requestType = $requestType;
        $this->status = $status;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $icon  = match($this->status) { 'approved' => '✅', 'rejected' => '❌', default => '🔄' };
        $label = match($this->status) { 'approved' => 'approuvée', 'rejected' => 'refusée', default => 'mise à jour' };
        $color = match($this->status) { 'approved' => 'green', 'rejected' => 'red', default => 'blue' };

        return [
            'type'  => 'request',
            'icon'  => $icon,
            'title' => 'Demande ' . $label . ' — ' . $this->requestType,
            'body'  => 'Votre demande de ' . strtolower($this->requestType) . ' a été ' . $label . ' par l\'administration.',
            'color' => $color,
            'url'   => '/student/requests',
        ];
    }
}
