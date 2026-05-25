<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $student = $user->student;

        $grades   = \App\Models\Grade::where('student_id', $student->id)->with('module')->get();
        $absences = \App\Models\Absence::where('student_id', $student->id)->with('module')->get();
        $requests = \App\Models\Request::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        $schedule = \App\Models\Schedule::where('group_id', $student->group_id)
            ->with(['module', 'professor.user', 'room'])
            ->get();

        // Next upcoming class
        $currentDay  = (int) date('N');
        $currentTime = date('H:i:s');
        $nextClass   = $schedule
            ->filter(fn($s) => $s->day_of_week == $currentDay && $s->start_time > $currentTime)
            ->sortBy('start_time')
            ->first();
        if (!$nextClass) {
            // Look for next day
            $nextClass = $schedule->sortBy(['day_of_week', 'start_time'])->first();
        }

        $gradesCount  = $grades->whereNotNull('final_grade')->count();
        $gpa          = $gradesCount > 0 ? $grades->whereNotNull('final_grade')->avg('final_grade') : 0;
        $gpaPercent   = round(($gpa / 20) * 100);
        $unjustified  = $absences->where('is_justified', false)->count();

        return view('student.dashboard', compact(
            'grades', 'absences', 'requests', 'schedule',
            'gpa', 'gpaPercent', 'nextClass', 'unjustified'
        ));
    }

    public function grades()
    {
        $student = Auth::user()->student;
        $grades = \App\Models\Grade::where('student_id', $student->id)->with(['module.semester'])->get();
        
        $gradesBySemester = $grades->groupBy(function($g) {
            return $g->module && $g->module->semester ? $g->module->semester->name : 'Autres Modules';
        })->sortKeys();
        
        // Calcul de la moyenne annuelle
        $totalGPA = 0;
        $validSemestersCount = 0;
        
        foreach($gradesBySemester as $sem => $semGrades) {
            $count = $semGrades->whereNotNull('final_grade')->count();
            if ($count > 0) {
                $totalGPA += $semGrades->whereNotNull('final_grade')->avg('final_grade');
                $validSemestersCount++;
            }
        }
        
        $yearlyGPA = $validSemestersCount > 0 ? $totalGPA / $validSemestersCount : 0;

        return view('student.grades', compact('gradesBySemester', 'yearlyGPA'));
    }

    public function absences()
    {
        $student = Auth::user()->student;
        $absences = \App\Models\Absence::where('student_id', $student->id)->with('module')->get();
        return view('student.absences', compact('absences'));
    }

    public function createRequest()
    {
        $requests = \App\Models\Request::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('student.requests.create', compact('requests'));
    }
}
