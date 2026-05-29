<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Module;
use App\Models\Student;
use App\Models\Professor;
use App\Models\Room;
use App\Models\Grade;
use App\Models\Absence;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'students_count' => Student::count(),
            'professors_count' => Professor::count(),
            'modules_count' => Module::count(),
            'rooms_count' => Room::count(),
            'grades_avg' => Grade::avg('final_grade') ?? 0,
            'absences_total' => Absence::count(),
            'pending_reclamations' => \App\Models\Reclamation::where('status', 'pending')->count(),
            'pending_justifications' => \App\Models\ExamJustification::where('status', 'pending')->count(),
        ];

        // Chart Data: Grades distribution
        $gradeDistribution = Grade::select(DB::raw('ROUND(final_grade) as grade'), DB::raw('count(*) as count'))
            ->groupBy('grade')
            ->orderBy('grade')
            ->get();

        // Chart Data: Absences by month
        $absencesByMonth = Absence::select(DB::raw('MONTH(date) as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Chart Data: Success rate by filiere
        $filiereStats = \App\Models\Filiere::with(['groups.students.grades'])->get()->map(function ($filiere) {
            $total = 0;
            $pass = 0;
            foreach ($filiere->groups as $group) {
                foreach ($group->students as $student) {
                    $avg = $student->grades->avg('final_grade');
                    if ($avg !== null) {
                        $total++;
                        if ($avg >= 10) $pass++;
                    }
                }
            }
            return [
                'name' => $filiere->code ?? $filiere->name,
                'total' => $total,
                'pass' => $pass,
                'rate' => $total > 0 ? round($pass / $total * 100) : 0,
            ];
        })->filter(fn($f) => $f['total'] > 0)->values();

        // Recent requests
        $recentRequests = \App\Models\Request::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'gradeDistribution', 'absencesByMonth', 'filiereStats', 'recentRequests'));

    }
}
