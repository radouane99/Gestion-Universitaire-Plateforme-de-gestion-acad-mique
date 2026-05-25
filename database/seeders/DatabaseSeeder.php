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

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Clear existing data in correct FK order to prevent lockups
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
        Role::truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Roles (Admin, Professor, Student)
        $adminRole = Role::create(['name' => 'admin']);
        $professorRole = Role::create(['name' => 'professor']);
        $studentRole = Role::create(['name' => 'student']);

        // 3. Groups (5 Moroccan-style Groups at UPF)
        $groups = [
            Group::create(['name' => 'Génie Informatique 1 (GI-1)', 'level' => 'L1']),
            Group::create(['name' => 'Génie Informatique 2 (GI-2)', 'level' => 'L2']),
            Group::create(['name' => 'Ingénierie des Données & Systèmes 1 (IDS-1)', 'level' => 'L3']),
            Group::create(['name' => 'Ingénierie des Données & Systèmes 2 (IDS-2)', 'level' => 'M1']),
            Group::create(['name' => 'Cybersécurité & Cloud (CSC-1)', 'level' => 'M2']),
        ];

        // 4. Modules (5 Premium Moroccan Engineering Modules)
        $modules = [
            Module::create(['code' => 'INF-201', 'name' => 'Algorithmique & Structures de Données', 'coefficient' => 2.00]),
            Module::create(['code' => 'INF-202', 'name' => 'Programmation Mobile (Flutter)', 'coefficient' => 1.50]),
            Module::create(['code' => 'INF-203', 'name' => 'Bases de Données Avancées (Oracle)', 'coefficient' => 2.00]),
            Module::create(['code' => 'INF-204', 'name' => 'Sécurité Informatique & Cryptographie', 'coefficient' => 1.50]),
            Module::create(['code' => 'INF-205', 'name' => 'Intelligence Artificielle & Deep Learning', 'coefficient' => 3.00]),
        ];

        // 5. Rooms (5 Moroccan Campus Rooms)
        $rooms = [
            Room::create(['name' => 'Amphi Ibn Khaldoun', 'capacity' => 150, 'type' => 'course']),
            Room::create(['name' => 'Salle de TP C-12', 'capacity' => 30, 'type' => 'TP']),
            Room::create(['name' => 'Salle TD A-05', 'capacity' => 40, 'type' => 'TD']),
            Room::create(['name' => 'Labo Informatique 4', 'capacity' => 25, 'type' => 'TP']),
            Room::create(['name' => 'Amphi Al Khwarizmi', 'capacity' => 120, 'type' => 'course']),
        ];

        // 6. Users & Admin (1 Default Admin User + 4 others if needed, total 5 admins/staff)
        $adminUser = User::create([
            'name' => 'Youssef Alami',
            'email' => 'admin@university.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);
        
        $adminStaff = [
            User::create(['name' => 'Amine Bennani', 'email' => 'amine@university.com', 'password' => Hash::make('password'), 'role_id' => $adminRole->id]),
            User::create(['name' => 'Khadija El Idrissi', 'email' => 'khadija@university.com', 'password' => Hash::make('password'), 'role_id' => $adminRole->id]),
            User::create(['name' => 'Rachid Mansouri', 'email' => 'rachid@university.com', 'password' => Hash::make('password'), 'role_id' => $adminRole->id]),
            User::create(['name' => 'Fatima Zahra Tazi', 'email' => 'fatima@university.com', 'password' => Hash::make('password'), 'role_id' => $adminRole->id]),
        ];

        // 7. Users & Professors (5 Professors with Moroccan names)
        $professorsData = [
            ['name' => 'Prof. Khalid Alami', 'email' => 'prof@university.com', 'dept' => 'Génie Logiciel'], // Main prof so existing routes work
            ['name' => 'Prof. Fatima Zahra Bennani', 'email' => 'fatima.bennani@university.com', 'dept' => 'Bases de Données'],
            ['name' => 'Prof. Youssef El Mansouri', 'email' => 'youssef.mansouri@university.com', 'dept' => 'Sécurité & Réseaux'],
            ['name' => 'Prof. Salma Tazi', 'email' => 'salma.tazi@university.com', 'dept' => 'Intelligence Artificielle'],
            ['name' => 'Prof. Hicham Alaoui', 'email' => 'hicham.alaoui@university.com', 'dept' => 'Mathématiques Appliquées'],
        ];

        $professors = [];
        foreach ($professorsData as $p) {
            $user = User::create([
                'name' => $p['name'],
                'email' => $p['email'],
                'password' => Hash::make('password'),
                'role_id' => $professorRole->id,
            ]);
            $professors[] = Professor::create([
                'user_id' => $user->id,
                'department' => $p['dept'],
            ]);
        }

        // 8. Users & Students (5 Students with Moroccan names + linked to groups)
        $studentsData = [
            ['name' => 'Amine El Amrani', 'email' => 'student@university.com', 'group' => $groups[1], 'num' => 'S202601'], // Main student for testing
            ['name' => 'Chaimae Jabri', 'email' => 'chaimae@university.com', 'group' => $groups[1], 'num' => 'S202602'],
            ['name' => 'Tariq Bennis', 'email' => 'tariq@university.com', 'group' => $groups[0], 'num' => 'S202603'],
            ['name' => 'Mehdi Sekkat', 'email' => 'mehdi@university.com', 'group' => $groups[2], 'num' => 'S202604'],
            ['name' => 'Soukaina Naciri', 'email' => 'soukaina@university.com', 'group' => $groups[3], 'num' => 'S202605'],
        ];

        $students = [];
        foreach ($studentsData as $s) {
            $user = User::create([
                'name' => $s['name'],
                'email' => $s['email'],
                'password' => Hash::make('password'),
                'role_id' => $studentRole->id,
            ]);
            $students[] = Student::create([
                'user_id' => $user->id,
                'group_id' => $s['group']->id,
                'student_number' => $s['num'],
            ]);
        }

        // 9. Schedules (5 time schedule slots for the groups)
        Schedule::create(['group_id' => $groups[1]->id, 'module_id' => $modules[0]->id, 'professor_id' => $professors[0]->id, 'room_id' => $rooms[0]->id, 'date' => now()->startOfWeek()->format('Y-m-d'), 'day_of_week' => 1, 'start_time' => '08:30:00', 'end_time' => '10:30:00']);
        Schedule::create(['group_id' => $groups[1]->id, 'module_id' => $modules[1]->id, 'professor_id' => $professors[1]->id, 'room_id' => $rooms[1]->id, 'date' => now()->startOfWeek()->addDays(1)->format('Y-m-d'), 'day_of_week' => 2, 'start_time' => '10:45:00', 'end_time' => '12:45:00']);
        Schedule::create(['group_id' => $groups[0]->id, 'module_id' => $modules[2]->id, 'professor_id' => $professors[2]->id, 'room_id' => $rooms[2]->id, 'date' => now()->startOfWeek()->addDays(2)->format('Y-m-d'), 'day_of_week' => 3, 'start_time' => '14:00:00', 'end_time' => '16:00:00']);
        Schedule::create(['group_id' => $groups[2]->id, 'module_id' => $modules[3]->id, 'professor_id' => $professors[3]->id, 'room_id' => $rooms[3]->id, 'date' => now()->startOfWeek()->addDays(3)->format('Y-m-d'), 'day_of_week' => 4, 'start_time' => '16:15:00', 'end_time' => '18:15:00']);
        Schedule::create(['group_id' => $groups[3]->id, 'module_id' => $modules[4]->id, 'professor_id' => $professors[4]->id, 'room_id' => $rooms[4]->id, 'date' => now()->startOfWeek()->addDays(4)->format('Y-m-d'), 'day_of_week' => 5, 'start_time' => '09:00:00', 'end_time' => '12:00:00']);

        // 10. Grades (6 realistic grades for students)
        Grade::create(['student_id' => $students[0]->id, 'module_id' => $modules[0]->id, 'cc1' => 14.50, 'cc2' => 15.00, 'exam' => 16.00, 'final_grade' => 15.40]);
        Grade::create(['student_id' => $students[0]->id, 'module_id' => $modules[1]->id, 'cc1' => 12.00, 'cc2' => 14.00, 'exam' => 11.50, 'final_grade' => 12.30]);
        Grade::create(['student_id' => $students[1]->id, 'module_id' => $modules[0]->id, 'cc1' => 16.00, 'cc2' => 17.50, 'exam' => 18.00, 'final_grade' => 17.40]);
        Grade::create(['student_id' => $students[2]->id, 'module_id' => $modules[2]->id, 'cc1' => 9.50, 'cc2' => 11.00, 'exam' => 8.00, 'final_grade' => 9.10]);
        Grade::create(['student_id' => $students[3]->id, 'module_id' => $modules[3]->id, 'cc1' => 13.00, 'cc2' => 12.50, 'exam' => 15.00, 'final_grade' => 14.10]);
        Grade::create(['student_id' => $students[4]->id, 'module_id' => $modules[4]->id, 'cc1' => 15.00, 'cc2' => 16.00, 'exam' => 17.00, 'final_grade' => 16.30]);

        // 11. Absences (5 realistic absences)
        Absence::create(['student_id' => $students[0]->id, 'date' => now()->subDays(5)->format('Y-m-d'), 'session_type' => 'course', 'is_justified' => false, 'justification_status' => 'none']);
        Absence::create(['student_id' => $students[0]->id, 'date' => now()->subDays(2)->format('Y-m-d'), 'session_type' => 'TP', 'is_justified' => true, 'justification_status' => 'approved']);
        Absence::create(['student_id' => $students[1]->id, 'date' => now()->subDays(4)->format('Y-m-d'), 'session_type' => 'TD', 'is_justified' => false, 'justification_status' => 'none']);
        Absence::create(['student_id' => $students[2]->id, 'date' => now()->subDays(1)->format('Y-m-d'), 'session_type' => 'course', 'is_justified' => true, 'justification_status' => 'pending']);
        Absence::create(['student_id' => $students[3]->id, 'date' => now()->subDays(3)->format('Y-m-d'), 'session_type' => 'TP', 'is_justified' => false, 'justification_status' => 'none']);

        // 12. Requests (5 administrative document requests)
        AcademicRequest::create(['user_id' => $students[0]->user_id, 'type' => 'Attestation de Scolarité', 'status' => 'approved', 'reason' => null]);
        AcademicRequest::create(['user_id' => $students[0]->user_id, 'type' => 'Relevé de Notes', 'status' => 'pending', 'reason' => null]);
        AcademicRequest::create(['user_id' => $students[1]->user_id, 'type' => 'Attestation de Scolarité', 'status' => 'pending', 'reason' => null]);
        AcademicRequest::create(['user_id' => $students[2]->user_id, 'type' => 'Convention de Stage', 'status' => 'rejected', 'reason' => 'Dossier d\'assurance manquant']);
        AcademicRequest::create(['user_id' => $students[3]->user_id, 'type' => 'Attestation de Scolarité', 'status' => 'approved', 'reason' => null]);

        // 13. Contact Messages (5 public contact forms)
        ContactMessage::create(['name' => 'Tariq Alami', 'email' => 'tariq.alami@gmail.com', 'subject' => 'Demande d\'inscription', 'message' => 'Bonjour, je souhaiterais m\'inscrire en Master IDS pour l\'année prochaine.', 'status' => 'replied']);
        ContactMessage::create(['name' => 'Fatima Jabri', 'email' => 'fatima.jabri@yahoo.fr', 'subject' => 'Frais de scolarité', 'message' => 'Pourriez-vous m\'envoyer la grille tarifaire pour le cycle d\'ingénieur ?', 'status' => 'pending']);
        ContactMessage::create(['name' => 'Yassine Bennis', 'email' => 'yassine.bennis@gmail.com', 'subject' => 'Partenariat', 'message' => 'Nous aimerions proposer des stages d\'été pour vos étudiants en GI.', 'status' => 'pending']);
        ContactMessage::create(['name' => 'Sanaa Mansouri', 'email' => 'sanaa.m@gmail.com', 'subject' => 'Problème d\'accès', 'message' => 'Je ne parviens plus à me connecter à mon portail étudiant depuis ce matin.', 'status' => 'replied']);
        ContactMessage::create(['name' => 'Adnane Tazi', 'email' => 'adnane.tazi@outlook.com', 'subject' => 'Transfert de dossier', 'message' => 'Quelle est la procédure pour transférer mon dossier depuis une autre université ?', 'status' => 'pending']);

        // 14. Activity Logs (5 initial log entries)
        ActivityLog::create([
            'user_id' => $adminUser->id,
            'action' => 'Connexion',
            'model_type' => 'Auth',
            'description' => 'Administrateur connecté au système depuis Fès, Maroc.',
            'ip_address' => '127.0.0.1'
        ]);
        ActivityLog::create([
            'user_id' => $adminUser->id,
            'action' => 'Importation',
            'model_type' => 'Base de Données',
            'description' => 'Importation initiale de la base de données académique UPF.',
            'ip_address' => '127.0.0.1'
        ]);
        ActivityLog::create([
            'user_id' => $adminUser->id,
            'action' => 'Système',
            'model_type' => 'Configuration',
            'description' => 'Initialisation des 5 modules d\'ingénierie et des salles de cours.',
            'ip_address' => '127.0.0.1'
        ]);
        ActivityLog::create([
            'user_id' => $adminUser->id,
            'action' => 'Sécurité',
            'model_type' => 'Utilisateurs',
            'description' => 'Génération des clés d\'accès de test pour les professeurs et étudiants.',
            'ip_address' => '127.0.0.1'
        ]);
        ActivityLog::create([
            'user_id' => $adminUser->id,
            'action' => 'Configuration',
            'model_type' => 'Interface',
            'description' => 'Mise en place de la nouvelle barre de navigation premium.',
            'ip_address' => '127.0.0.1'
        ]);

        // 15. Reservations (5 room reservations by professors)
        Reservation::create(['professor_id' => $professors[0]->id, 'room_id' => $rooms[1]->id, 'start_time' => now()->addDays(1)->setHour(9)->setMinute(0), 'end_time' => now()->addDays(1)->setHour(11)->setMinute(0), 'purpose' => 'Séance complémentaire de programmation Flutter', 'status' => 'approved']);
        Reservation::create(['professor_id' => $professors[1]->id, 'room_id' => $rooms[2]->id, 'start_time' => now()->addDays(2)->setHour(14)->setMinute(0), 'end_time' => now()->addDays(2)->setHour(16)->setMinute(0), 'purpose' => 'Soutenance finale de projet de fin d\'études (PFE)', 'status' => 'pending']);
        Reservation::create(['professor_id' => $professors[2]->id, 'room_id' => $rooms[3]->id, 'start_time' => now()->addDays(3)->setHour(10)->setMinute(0), 'end_time' => now()->addDays(3)->setHour(12)->setMinute(0), 'purpose' => 'Atelier de recherche sur la Cybersécurité', 'status' => 'approved']);
        Reservation::create(['professor_id' => $professors[3]->id, 'room_id' => $rooms[0]->id, 'start_time' => now()->addDays(4)->setHour(16)->setMinute(0), 'end_time' => now()->addDays(4)->setHour(18)->setMinute(0), 'purpose' => 'Séminaire invité : L\'Intelligence Artificielle au Maroc', 'status' => 'rejected']);
        Reservation::create(['professor_id' => $professors[4]->id, 'room_id' => $rooms[4]->id, 'start_time' => now()->addDays(5)->setHour(9)->setMinute(0), 'end_time' => now()->addDays(5)->setHour(12)->setMinute(0), 'purpose' => 'Rattrapage d\'examen d\'Algorithmique avancée', 'status' => 'approved']);

        // 16. Classroom Posts & Comments (5 posts & comments for academic interaction)
        $post1 = ClassroomPost::create(['user_id' => $professors[0]->user_id, 'group_id' => $groups[1]->id, 'module_id' => $modules[0]->id, 'title' => 'Support de cours - Structures de Données', 'content' => 'Bonjour à tous, voici le support du chapitre 2 sur les arbres binaires de recherche. Bonne lecture !']);
        $post2 = ClassroomPost::create(['user_id' => $professors[1]->user_id, 'group_id' => $groups[1]->id, 'module_id' => $modules[1]->id, 'title' => 'Rendu du Projet Flutter', 'content' => 'Le rendu du mini-projet est fixé pour le dimanche prochain avant minuit sur GitHub.']);
        $post3 = ClassroomPost::create(['user_id' => $professors[2]->user_id, 'group_id' => $groups[0]->id, 'module_id' => $modules[2]->id, 'title' => 'Préparation à l\'examen Oracle', 'content' => 'Je vous propose une séance de révision supplémentaire ce jeudi à 15h dans la salle C-12.']);
        $post4 = ClassroomPost::create(['user_id' => $professors[3]->user_id, 'group_id' => $groups[2]->id, 'module_id' => $modules[3]->id, 'title' => 'Exercice de Cryptographie', 'content' => 'Essayez de résoudre le défi de chiffrement RSA partagé en pièce jointe.']);
        $post5 = ClassroomPost::create(['user_id' => $professors[4]->user_id, 'group_id' => $groups[3]->id, 'module_id' => $modules[4]->id, 'title' => 'Ressources Deep Learning', 'content' => 'Voici un excellent lien pour comprendre les réseaux de neurones convolutifs (CNN) : https://coursera.org']);

        Comment::create(['user_id' => $students[0]->user_id, 'classroom_post_id' => $post1->id, 'content' => 'Merci professeur, le cours est très clair !']);
        Comment::create(['user_id' => $students[1]->user_id, 'classroom_post_id' => $post2->id, 'content' => 'Est-ce qu\'on peut travailler en binôme pour le projet ?']);
        Comment::create(['user_id' => $students[2]->user_id, 'classroom_post_id' => $post3->id, 'content' => 'Présent ! Merci pour cette séance.']);
        Comment::create(['user_id' => $students[3]->user_id, 'classroom_post_id' => $post4->id, 'content' => 'J\'ai réussi à décrypter le message de test, le voici : UPF2026 !']);
        Comment::create(['user_id' => $students[4]->user_id, 'classroom_post_id' => $post5->id, 'content' => 'Ce lien est formidable pour notre projet de fin d\'année.']);

        // 17. Automatically configure Filieres & linkages
        $gi = \App\Models\Filiere::firstOrCreate(
            ['code' => 'GI'],
            ['name' => 'Génie Informatique', 'description' => 'Ingénierie du logiciel et développement système']
        );

        $gc = \App\Models\Filiere::firstOrCreate(
            ['code' => 'GC'],
            ['name' => 'Génie Civil', 'description' => 'Construction, BTP et Infrastructures']
        );

        $ge = \App\Models\Filiere::firstOrCreate(
            ['code' => 'GE'],
            ['name' => 'Génie Électrique', 'description' => 'Électronique, Électrotechnique et Automatisme']
        );

        // Link Groups to Filieres
        $allGroups = \App\Models\Group::all();
        foreach ($allGroups as $group) {
            $name = strtolower($group->name);
            if (str_contains($name, 'civil') || str_contains($name, 'btp') || str_contains($name, 'archi')) {
                $group->update(['filiere_id' => $gc->id]);
            } elseif (str_contains($name, 'élec') || str_contains($name, 'elec') || str_contains($name, 'auto')) {
                $group->update(['filiere_id' => $ge->id]);
            } else {
                $group->update(['filiere_id' => $gi->id]);
            }
        }

        // Link Modules to Filieres
        $allModules = \App\Models\Module::all();
        foreach ($allModules as $module) {
            $name = strtolower($module->name);
            if (str_contains($name, 'matériaux') || str_contains($name, 'béton') || str_contains($name, 'structure')) {
                $module->update(['filiere_id' => $gc->id]);
            } elseif (str_contains($name, 'circuit') || str_contains($name, 'énergie') || str_contains($name, 'moteur')) {
                $module->update(['filiere_id' => $ge->id]);
            } else {
                $module->update(['filiere_id' => $gi->id]);
            }
        }

        // Create Academic Year
        $year = \App\Models\AcademicYear::firstOrCreate(
            ['name' => '2025/2026'],
            ['is_current' => true]
        );

        // Create Semesters
        $s1 = \App\Models\Semester::firstOrCreate(['name' => 'S1'], ['level' => 1]);
        $s2 = \App\Models\Semester::firstOrCreate(['name' => 'S2'], ['level' => 1]);
        $s3 = \App\Models\Semester::firstOrCreate(['name' => 'S3'], ['level' => 2]);
        $s4 = \App\Models\Semester::firstOrCreate(['name' => 'S4'], ['level' => 2]);

        // Assign Academic Year to Students
        \App\Models\Student::query()->update(['academic_year_id' => $year->id]);

        // Assign Modules to Semesters
        foreach($allModules as $index => $mod) {
            $mod->update(['semester_id' => ($index % 2 == 0) ? $s1->id : $s2->id]);
        }
    }
}
