<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;
use App\Models\Group;
use App\Models\Module;
use App\Models\Student;
use App\Models\Professor;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Grade;
use App\Models\Absence;
use App\Models\Request as AcademicRequest;
use App\Models\ContactMessage;
use App\Models\ActivityLog;
use App\Models\ClassroomPost;
use App\Models\Comment;
use App\Models\Reservation;
use App\Models\Filiere;
use App\Models\AcademicYear;
use App\Models\Semester;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        ActivityLog::truncate();
        ContactMessage::truncate();
        Absence::truncate();
        Grade::truncate();
        AcademicRequest::truncate();
        Schedule::truncate();
        Comment::truncate();
        ClassroomPost::truncate();
        Reservation::truncate();
        Student::truncate();
        Professor::truncate();
        User::truncate();
        Room::truncate();
        Module::truncate();
        Group::truncate();
        Filiere::truncate();
        Role::truncate();
        AcademicYear::truncate();
        Semester::truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $seedPassword = Hash::make('password');

        // Roles
        $adminRole = Role::create(['name' => 'admin']);
        $professorRole = Role::create(['name' => 'professor']);
        $studentRole = Role::create(['name' => 'student']);

        // Admin User
        $adminUser = User::create([
            'name' => 'Radouane El Asri',
            'email' => 'radouane.elasri@usmba.ac.ma',
            'password' => $seedPassword,
            'role_id' => $adminRole->id,
        ]);

        // Academic Year & Semesters
        $year = AcademicYear::create(['name' => '2025/2026', 'is_current' => true]);
        $s1 = Semester::create(['name' => 'S1', 'level' => 1]);
        $s2 = Semester::create(['name' => 'S2', 'level' => 1]);

        // Filieres
        $filieresData = [
            'INF' => ['name' => 'Génie Informatique', 'desc' => 'Ingénierie du logiciel et intelligence artificielle'],
            'GC'  => ['name' => 'Génie Civil', 'desc' => 'BTP, infrastructures et travaux publics'],
            'GE'  => ['name' => 'Génie Électrique', 'desc' => 'Électrotechnique, électronique et automatismes'],
            'ECO' => ['name' => 'Économie & Gestion', 'desc' => 'Finance, comptabilité et gestion d\'entreprise'],
            'MKT' => ['name' => 'Marketing & Commerce', 'desc' => 'Marketing digital, commerce international'],
        ];

        $filieres = [];
        $groups = [];
        $modules = [];

        foreach ($filieresData as $code => $data) {
            $filiere = Filiere::create(['code' => $code, 'name' => $data['name'], 'description' => $data['desc']]);
            $filieres[] = $filiere;

            // 2 Groups per filiere
            $groups[] = Group::create(['name' => $data['name'] . ' - Groupe 1', 'level' => 'L1', 'filiere_id' => $filiere->id]);
            $groups[] = Group::create(['name' => $data['name'] . ' - Groupe 2', 'level' => 'L1', 'filiere_id' => $filiere->id]);

            // 2 Modules per filiere
            $modules[] = Module::create(['code' => $code.'-101', 'name' => 'Introduction - ' . $data['name'], 'coefficient' => 2.0, 'filiere_id' => $filiere->id, 'semester_id' => $s1->id]);
            $modules[] = Module::create(['code' => $code.'-102', 'name' => 'Avancé - ' . $data['name'], 'coefficient' => 3.0, 'filiere_id' => $filiere->id, 'semester_id' => $s2->id]);
        }

        // Rooms
        $rooms = [
            Room::create(['name' => 'Amphi Ibn Khaldoun', 'capacity' => 200, 'type' => 'course']),
            Room::create(['name' => 'Amphi Al Khwarizmi', 'capacity' => 150, 'type' => 'course']),
            Room::create(['name' => 'Salle TD 01', 'capacity' => 40, 'type' => 'TD']),
            Room::create(['name' => 'Salle TD 02', 'capacity' => 40, 'type' => 'TD']),
            Room::create(['name' => 'Labo Info 1', 'capacity' => 30, 'type' => 'TP']),
        ];

        // Professors
        $professorsData = [
            ['name' => 'Prof. Khalid Alami', 'email' => 'professor@university.com', 'dept' => 'Génie Informatique'],
            ['name' => 'Prof. Fatima Zahra Bennani', 'email' => 'fatima.bennani@usmba.ac.ma', 'dept' => 'Génie Civil'],
            ['name' => 'Prof. Youssef El Mansouri', 'email' => 'youssef.mansouri@usmba.ac.ma', 'dept' => 'Génie Électrique'],
            ['name' => 'Prof. Salma Tazi', 'email' => 'salma.tazi@usmba.ac.ma', 'dept' => 'Économie'],
            ['name' => 'Prof. Hicham Alaoui', 'email' => 'hicham.alaoui@usmba.ac.ma', 'dept' => 'Marketing'],
        ];

        $professors = [];
        foreach ($professorsData as $p) {
            $user = User::create(['name' => $p['name'], 'email' => $p['email'], 'password' => $seedPassword, 'role_id' => $professorRole->id]);
            $professors[] = Professor::create(['user_id' => $user->id, 'department' => $p['dept']]);
        }

        // Students Generation
        $firstNames = ['Mohammed', 'Ahmed', 'Youssef', 'Hamza', 'Omar', 'Ali', 'Hassan', 'Othmane', 'Ilyas', 'Ayoub', 'Yassine', 'Amine', 'Mehdi', 'Tariq', 'Fatima', 'Khadija', 'Meryem', 'Salma', 'Sara', 'Hajar', 'Imane', 'Nada', 'Zineb', 'Aya', 'Chaimae', 'Soukaina'];
        $lastNames = ['Alaoui', 'Bennani', 'Tazi', 'Idrissi', 'El Fassi', 'Bennis', 'Guessous', 'Benjelloun', 'Chraibi', 'Lazraq', 'Lahlou', 'Benani', 'El Amrani', 'Mansouri', 'Filali', 'El Othmani', 'Boujida', 'Naciri', 'Sekkat', 'Tahiri'];

        $students = [];
        $studentCounter = 1;

        foreach ($groups as $group) {
            // 25 students per group
            for ($i = 0; $i < 25; $i++) {
                $fn = $firstNames[array_rand($firstNames)];
                $ln = $lastNames[array_rand($lastNames)];
                
                // Add default student email for testing
                $email = ($studentCounter === 1) ? 'student@university.com' : strtolower($fn) . '.' . strtolower($ln) . $studentCounter . '@usmba.ac.ma';
                
                $user = User::create([
                    'name' => $fn . ' ' . $ln,
                    'email' => $email,
                    'password' => $seedPassword,
                    'role_id' => $studentRole->id,
                ]);

                $students[] = Student::create([
                    'user_id' => $user->id,
                    'group_id' => $group->id,
                    'student_number' => 'S2026' . str_pad($studentCounter, 4, '0', STR_PAD_LEFT),
                    'academic_year_id' => $year->id,
                ]);
                $studentCounter++;
            }
        }

        // Grades & Absences
        foreach ($students as $student) {
            // Assign grades for modules in the student's filiere
            $studentModules = Module::where('filiere_id', $student->group->filiere_id)->get();
            foreach ($studentModules as $mod) {
                $profile = rand(1, 100);
                if ($profile > 80) {
                    $cc1 = rand(14, 18) + (rand(0, 4) * 0.25);
                    $cc2 = rand(15, 19) + (rand(0, 4) * 0.25);
                    $exam = rand(14, 18) + (rand(0, 4) * 0.25);
                } elseif ($profile > 30) {
                    $cc1 = rand(10, 14) + (rand(0, 4) * 0.25);
                    $cc2 = rand(9, 13) + (rand(0, 4) * 0.25);
                    $exam = rand(10, 14) + (rand(0, 4) * 0.25);
                } else {
                    $cc1 = rand(4, 9) + (rand(0, 4) * 0.25);
                    $cc2 = rand(5, 9) + (rand(0, 4) * 0.25);
                    $exam = rand(3, 8) + (rand(0, 4) * 0.25);
                }
                
                $final = ($cc1 * 0.2) + ($cc2 * 0.2) + ($exam * 0.6);
                
                Grade::create([
                    'student_id' => $student->id,
                    'module_id' => $mod->id,
                    'cc1' => min(20, $cc1),
                    'cc2' => min(20, $cc2),
                    'exam' => min(20, $exam),
                    'final_grade' => min(20, round($final, 2))
                ]);
            }

            // Occasional absences
            if (rand(1, 10) > 8) {
                Absence::create([
                    'student_id' => $student->id, 
                    'date' => now()->subDays(rand(1, 30))->format('Y-m-d'), 
                    'session_type' => ['course', 'TD', 'TP'][rand(0, 2)], 
                    'is_justified' => rand(0, 1) == 1, 
                    'justification_status' => ['none', 'pending', 'approved'][rand(0, 2)]
                ]);
            }
        }
    }
}
