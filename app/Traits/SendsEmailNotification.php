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

        $title      = $dbData['title']       ?? 'Nouvelle notification académique';
        $body       = $dbData['body']        ?? ($dbData['message'] ?? ($dbData['body_content'] ?? ''));
        $url        = $dbData['url']         ?? url('/dashboard');
        $icon       = $dbData['icon']        ?? '🔔';
        $color      = $dbData['color']       ?? 'blue';

        // Map color to a friendly action label
        $actionText = match ($color) {
            'green'  => 'Voir le détail',
            'red'    => 'Vérifier maintenant',
            'amber'  => 'Régulariser ma situation',
            'blue'   => 'Accéder à mon espace',
            default  => 'Accéder à mon espace',
        };

        return (new MailMessage)
            ->subject('UPF — ' . strip_tags($title))
            ->view('emails.notification', [
                'recipientName' => $notifiable->name ?? 'Étudiant(e)',
                'title'         => $title,
                'body'          => $body,
                'icon'          => $icon,
                'actionUrl'     => $url,
                'actionText'    => $actionText,
            ]);
    }
}
