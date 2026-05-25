<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Module;
use App\Models\Grade;
use App\Models\Absence;
use App\Models\ClassroomPost;
use App\Models\Schedule;
use App\Models\Professor;

class FakeActivitiesSeeder extends Seeder
{
    public function run()
    {
        $students = Student::all();
        $schedules = Schedule::with('professor.user', 'module')->get();
        
        // 1. Create Classroom Posts (from professors to their groups)
        $groupedSchedules = $schedules->unique(function ($s) {
            return $s->group_id . '-' . $s->module_id;
        });
        
        foreach ($groupedSchedules as $schedule) {
            if ($schedule->professor && rand(1, 10) > 3) {
                ClassroomPost::firstOrCreate([
                    'user_id' => $schedule->professor->user_id,
                    'group_id' => $schedule->group_id,
                    'module_id' => $schedule->module_id,
                    'title' => 'Bienvenue au cours de ' . $schedule->module->name,
                    'content' => "Bonjour à tous, je suis heureux de vous accueillir pour ce module. Le plan de cours est disponible.",
                ]);
            }
        }
        
        // 2. Create Grades and Absences for Students
        foreach ($students as $student) {
            $studentModules = Schedule::where('group_id', $student->group_id)->pluck('module_id')->unique();
            
            foreach ($studentModules as $moduleId) {
                // Grades
                if (rand(1, 10) > 2) {
                    $cc1 = rand(8, 18);
                    $cc2 = rand(8, 18);
                    $exam = rand(6, 18);
                    $final = (($cc1 + $cc2) / 2 * 0.4) + ($exam * 0.6);
                    
                    Grade::firstOrCreate([
                        'student_id' => $student->id,
                        'module_id' => $moduleId
                    ], [
                        'cc1' => $cc1,
                        'cc2' => $cc2,
                        'exam' => $exam,
                        'final_grade' => round($final, 2)
                    ]);
                }
                
                // Absences
                if (rand(1, 10) > 7) {
                    $isJustified = rand(0, 1);
                    Absence::firstOrCreate([
                        'student_id' => $student->id,
                        'module_id' => $moduleId,
                        'date' => now()->subDays(rand(1, 30))->format('Y-m-d')
                    ], [
                        'session_type' => 'Cours',
                        'duration' => 2,
                        'is_justified' => $isJustified,
                        'justification_status' => $isJustified ? 'Validée' : 'Non justifiée'
                    ]);
                }
            }
        }
    }
}
