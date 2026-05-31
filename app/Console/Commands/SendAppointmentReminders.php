<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Notifications\AcademicNotification;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders to students and hosts 24 hours before their scheduled appointment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Searching for appointments scheduled in ~24 hours...');

        $appointments = Appointment::where('status', 'scheduled')
            ->where('reminder_sent', false)
            ->whereHas('slot', function ($query) {
                $query->whereBetween('start_time', [
                    now()->addHours(23),
                    now()->addHours(25)
                ]);
            })
            ->with(['slot.host', 'student.user'])
            ->get();

        $count = $appointments->count();
        $this->info("Found {$count} appointment(s) needing reminders.");

        foreach ($appointments as $appointment) {
            $slot = $appointment->slot;
            
            // Notify student
            $appointment->student->user?->notify(new AcademicNotification(
                "⏰ Rappel : Votre rendez-vous avec {$slot->host->name} est prévu pour demain à {$slot->start_time->format('H:i')}.",
                'warning',
                route('student.appointments.index')
            ));

            // Notify host
            $hostRoute = $slot->host->isProfessor() ? route('professor.appointments.index') : route('admin.appointments.index');
            $slot->host->notify(new AcademicNotification(
                "⏰ Rappel : Votre rendez-vous avec l'étudiant {$appointment->student->user->name} est prévu pour demain à {$slot->start_time->format('H:i')}.",
                'warning',
                $hostRoute
            ));

            $appointment->update(['reminder_sent' => true]);
            $this->info("Reminder sent for Appointment ID: {$appointment->id}");
        }

        $this->info('Reminders sent successfully.');
    }
}
