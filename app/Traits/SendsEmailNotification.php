<?php

namespace App\Traits;

use Illuminate\Notifications\Messages\MailMessage;

trait SendsEmailNotification
{
    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $dbData = method_exists($this, 'toDatabase') ? $this->toDatabase($notifiable) : $this->toArray($notifiable);
        $title = $dbData['title'] ?? 'Nouvelle notification académique';
        $body = $dbData['body'] ?? ($dbData['message'] ?? ($dbData['body_content'] ?? ''));
        $url = $dbData['url'] ?? '/dashboard';
        
        return (new MailMessage)
            ->subject('UPF Portail — ' . $title)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line($body)
            ->action('Accéder à mon Espace', url($url))
            ->line('Merci d\'utiliser la plateforme académique intelligente de l\'Université Privée de Fès.');
    }
}
