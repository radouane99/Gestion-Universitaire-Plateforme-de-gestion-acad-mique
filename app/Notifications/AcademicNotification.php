<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AcademicNotification extends Notification
{
    use Queueable;

    protected $message;
    protected $type;
    protected $link;

    public function __construct($message, $type = 'info', $link = '#')
    {
        $this->message = $message;
        $this->type = $type;
        $this->link = $link;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('UPF Academic Update')
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line($this->message)
                    ->action('View Details', url($this->link))
                    ->line('Thank you for being part of UPF Academic Community!');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
            'type' => $this->type,
            'link' => $this->link,
        ];
    }
}
