<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;
use App\Models\Grade;
use App\Models\Absence;
use App\Models\Student;

class ProfessorController extends Controller
{
    public function index()
    {
        $professor = Auth::user()->professor;
        
        // 1. Get all schedules taught by this professor
        $schedules = Schedule::where('professor_id', $professor->id)
            ->with(['group.students.user', 'module'])
            ->get();
            
        $taughtModuleIds = $schedules->pluck('module_id')->unique();
        $taughtGroupIds = $schedules->pluck('group_id')->unique();
        
        // 2. Success Rate (Taux de réussite)
        // Grades of students in these modules
        $grades = Grade::whereIn('module_id', $taughtModuleIds)
            ->whereHas('student', function ($query) use ($taughtGroupIds) {
                $query->whereIn('group_id', $taughtGroupIds);
            })->whereNotNull('final_grade')->get();
            
        $totalGrades = $grades->count();
        $passedGrades = $grades->where('final_grade', '>=', 10)->count();
        $successRate = $totalGrades > 0 ? round(($passedGrades / $totalGrades) * 100) : 0;
        
        // 3. Top 3 Students
        // Best average in the professor's modules
        $topStudents = Student::whereIn('group_id', $taughtGroupIds)
            ->with('user')
            ->get()
            ->map(function ($student) use ($taughtModuleIds) {
                $avg = Grade::where('student_id', $student->id)
                    ->whereIn('module_id', $taughtModuleIds)
                    ->whereNotNull('final_grade')
                    ->avg('final_grade');
                $student->prof_avg = $avg;
                return $student;
            })
            ->filter(fn($student) => $student->prof_avg !== null)
            ->sortByDesc('prof_avg')
            ->take(3);
            
        // 4. Frequent Absentees (in professor's modules)
        $frequentAbsentees = Student::whereIn('group_id', $taughtGroupIds)
            ->with('user')
            ->get()
            ->map(function ($student) use ($taughtModuleIds) {
                $absences = Absence::where('student_id', $student->id)
                    ->whereIn('module_id', $taughtModuleIds)
                    ->where('is_justified', false)
                    ->count();
                $student->prof_absences = $absences;
                return $student;
            })
            ->filter(fn($student) => $student->prof_absences > 0)
            ->sortByDesc('prof_absences')
            ->take(5);

        return view('professor.dashboard', compact('schedules', 'successRate', 'topStudents', 'frequentAbsentees'));
    }
}
