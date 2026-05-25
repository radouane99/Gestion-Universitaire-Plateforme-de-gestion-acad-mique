<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\ContactMessage;

class ReplyMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $replyText;
    public $originalMessage;

    public function __construct($replyText, ContactMessage $originalMessage)
    {
        $this->replyText = $replyText;
        $this->originalMessage = $originalMessage;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'RE : ' . $this->originalMessage->subject . ' - UPF',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reply-message',
        );
    }
}
