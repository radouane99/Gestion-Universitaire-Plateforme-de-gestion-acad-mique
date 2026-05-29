<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use App\Models\ProfessorConvocation;

class ProfessorConvocationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $convocation;
    public string $pdfContent;

    public function __construct(ProfessorConvocation $convocation, string $pdfContent)
    {
        $this->convocation = $convocation;
        $this->pdfContent  = $pdfContent;
    }

    public function envelope(): Envelope
    {
        $professor = $this->convocation->professor->user;
        $exam      = $this->convocation->exam;

        return new Envelope(
            subject: '🎓 Convocation de Surveillance — ' . $exam->module->name . ' (' . \Carbon\Carbon::parse($exam->date)->format('d/m/Y') . ')',
            to: [$professor->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.professor_convocation',
        );
    }

    public function attachments(): array
    {
        $filename = 'surveillance_' . $this->convocation->reference . '.pdf';

        return [
            Attachment::fromData(fn () => $this->pdfContent, $filename)
                ->withMime('application/pdf'),
        ];
    }
}
