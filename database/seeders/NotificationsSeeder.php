<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Notifications\GradePublished;
use App\Notifications\AbsenceRecorded;
use App\Notifications\RequestUpdated;

class NotificationsSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'Algorithmique & Structures de Données',
            'Programmation Web',
            'Bases de Données Avancées',
            'Systèmes d\'Exploitation',
            'Mathématiques Discrètes',
        ];

        $students = User::whereHas('student')->take(20)->get();

        foreach ($students as $user) {
            // Note notification
            $user->notify(new GradePublished(
                $modules[array_rand($modules)],
                round(rand(8, 18) + rand(0, 9) / 10, 2)
            ));

            // Absence notification (50% chance)
            if (rand(0, 1)) {
                $user->notify(new AbsenceRecorded(
                    $modules[array_rand($modules)],
                    now()->subDays(rand(1, 7))->format('d/m/Y')
                ));
            }
        }

        // Request notifications
        $allUsers = User::all()->take(15);
        foreach ($allUsers as $user) {
            if (rand(0, 1)) {
                $status = ['approved', 'rejected', 'pending'][rand(0, 2)];
                $user->notify(new RequestUpdated('Attestation de Scolarité', $status));
            }
        }

        $this->command->info('Notifications seeded successfully!');
    }
}
