<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Filiere;
use App\Models\Module;
use App\Models\Group;
use App\Models\Semester;
use App\Models\AcademicYear;
use App\Models\User;
use App\Models\Student;
use App\Models\Professor;
use App\Models\Room;
use App\Models\Schedule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class RealisticDataSeeder extends Seeder
{
    public function run()
    {
        $year = AcademicYear::firstOrCreate(['name' => '2025/2026'], ['is_current' => true]);
        $s1 = Semester::firstOrCreate(['name' => 'S1'], ['level' => 1]);
        $s2 = Semester::firstOrCreate(['name' => 'S2'], ['level' => 1]);
        
        // Ensure some rooms exist
        $rooms = [];
        for ($i = 1; $i <= 5; $i++) {
            $rooms[] = Room::firstOrCreate(['name' => "Salle {$i}", 'type' => 'Cours', 'capacity' => 40]);
        }

        $filieresData = [
            'GI' => ['name' => 'Génie Informatique', 'prefix' => 'INF'],
            'MATH' => ['name' => 'Mathématiques Appliquées', 'prefix' => 'MAT'],
            'GP' => ['name' => 'Génie des Procédés', 'prefix' => 'PRC'],
            'GE' => ['name' => 'Génie Électrique', 'prefix' => 'ELEC'],
            'MGT' => ['name' => 'Management & Commerce', 'prefix' => 'MGT'],
            'GC' => ['name' => 'Génie Civil', 'prefix' => 'CIV'],
            'IA' => ['name' => 'Intelligence Artificielle', 'prefix' => 'AI']
        ];

        $password = Hash::make(env('SEED_USER_PASSWORD', 'ChangeMe123!'));
        
        $profRole = \App\Models\Role::firstOrCreate(['name' => 'professor']);
        $studentRole = \App\Models\Role::firstOrCreate(['name' => 'student']);

        foreach ($filieresData as $code => $data) {
            $filiere = Filiere::firstOrCreate(
                ['code' => $code],
                ['name' => $data['name'], 'description' => 'Département de ' . $data['name']]
            );

            // Create Professors for this filiere
            $profs = [];
            for ($p = 1; $p <= 2; $p++) {
                $user = User::firstOrCreate(
                    ['email' => strtolower($code) . ".prof{$p}@upf.ac.ma"],
                    ['name' => "Professeur {$p} {$code}", 'password' => $password, 'role_id' => $profRole->id]
                );
                $profs[] = Professor::firstOrCreate(
                    ['user_id' => $user->id],
                    ['department' => $data['name']]
                );
            }

            // Create Groups and Students
            $groups = [];
            $groups[] = Group::firstOrCreate(['name' => $code . '-G1'], ['level' => 1, 'filiere_id' => $filiere->id]);
            $groups[] = Group::firstOrCreate(['name' => $code . '-G2'], ['level' => 1, 'filiere_id' => $filiere->id]);

            foreach ($groups as $gIndex => $group) {
                // Create 15 students per group
                for ($s = 1; $s <= 15; $s++) {
                    $studentEmail = strtolower($code) . ".g" . ($gIndex+1) . ".s{$s}@upf.ac.ma";
                    $studentUser = User::firstOrCreate(
                        ['email' => $studentEmail],
                        ['name' => "Étudiant {$s} {$group->name}", 'password' => $password, 'role_id' => $studentRole->id]
                    );
                    Student::firstOrCreate(
                        ['user_id' => $studentUser->id],
                        [
                            'group_id' => $group->id, 
                            'student_number' => "STU-" . $year->id . "-" . str_pad($studentUser->id, 4, '0', STR_PAD_LEFT),
                            'academic_year_id' => $year->id
                        ]
                    );
                }
            }

            // Create Modules and Schedules
            $modules = [];
            for ($i = 1; $i <= 14; $i++) {
                $sem = ($i <= 7) ? $s1 : $s2;
                $modNum = str_pad($i, 2, '0', STR_PAD_LEFT);
                $module = Module::firstOrCreate(
                    [
                        'code' => $data['prefix'] . '-' . $sem->name . '-' . $modNum,
                        'filiere_id' => $filiere->id
                    ],
                    [
                        'name' => "Module {$i} de " . $data['name'],
                        'coefficient' => rand(2, 5),
                        'semester_id' => $sem->id
                    ]
                );
                $modules[] = $module;

                // Create a schedule for each group for this module
                foreach ($groups as $group) {
                    $prof = $profs[array_rand($profs)];
                    $room = $rooms[array_rand($rooms)];
                    
                    Schedule::firstOrCreate([
                        'group_id' => $group->id,
                        'module_id' => $module->id,
                        'day_of_week' => rand(1, 6), // 1: Monday, 6: Saturday
                    ], [
                        'professor_id' => $prof->id,
                        'room_id' => $room->id,
                        'start_time' => '08:30:00',
                        'end_time' => '10:30:00',
                    ]);
                }
            }
        }
    }
}
