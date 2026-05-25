<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use App\Models\Convocation;

class ConvocationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $convocation;
    public string $pdfContent;

    public function __construct(Convocation $convocation, string $pdfContent)
    {
        $this->convocation = $convocation;
        $this->pdfContent = $pdfContent;
    }

    public function envelope(): Envelope
    {
        $student = $this->convocation->student->user;
        $exam    = $this->convocation->exam;

        return new Envelope(
            subject: '📋 Convocation d\'examen — ' . $exam->module->name . ' (' . $exam->type . ')',
            to: [$student->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.convocation',
        );
    }

    public function attachments(): array
    {
        $exam = $this->convocation->exam;
        $filename = 'convocation_' . $this->convocation->reference . '.pdf';

        return [
            Attachment::fromData(fn () => $this->pdfContent, $filename)
                ->withMime('application/pdf'),
        ];
    }
}
