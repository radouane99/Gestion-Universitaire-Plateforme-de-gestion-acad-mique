<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('convocations:send-reminders')]
#[Description('Envoie les convocations par email pour les examens prévus dans 7 jours')]
class SendConvocationsCron extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetDate = \Carbon\Carbon::now()->addDays(7)->format('Y-m-d');

        // Trouver les examens prévus dans exactement 7 jours
        $exams = \App\Models\Exam::where('date', $targetDate)
            ->with(['convocations.student.user'])
            ->get();

        $count = 0;

        foreach ($exams as $exam) {
            foreach ($exam->convocations as $convocation) {
                // On pourrait ajouter une colonne 'email_sent' pour éviter les doublons, 
                // mais on suppose ici que c'est géré par le fait que la tâche cron tourne une seule fois par jour.
                if ($convocation->student && $convocation->student->user && $convocation->student->user->email) {
                    \Illuminate\Support\Facades\Mail::to($convocation->student->user->email)
                        ->queue(new \App\Mail\ConvocationMail($convocation));
                    $count++;
                }
            }
        }

        $this->info("{$count} convocations envoyées pour les examens du {$targetDate}.");
    }
}
