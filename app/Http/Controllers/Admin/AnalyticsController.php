<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\Absence;
use App\Models\Module;
use App\Models\Student;
use App\Models\RetakeEligibility;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // 1. Taux de présence globale (Absences justifiées vs injustifiées)
        $totalAbsences = Absence::count();
        $justifiedAbsences = Absence::where('is_justified', true)->count();
        $unjustifiedAbsences = $totalAbsences - $justifiedAbsences;

        // 2. Top / Flop Modules (Moyenne des notes par module)
        $moduleStats = Grade::select('module_id', DB::raw('AVG(final_grade) as avg_grade'))
            ->whereNotNull('final_grade')
            ->groupBy('module_id')
            ->with('module')
            ->get();

        $topModules = $moduleStats->sortByDesc('avg_grade')->take(5);
        $flopModules = $moduleStats->sortBy('avg_grade')->take(5);

        // 3. Statistiques des Rattrapages
        $retakeStats = RetakeEligibility::select('module_id', DB::raw('count(*) as count'))
            ->groupBy('module_id')
            ->with('module')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        // 4. Répartition des Décisions (Estimation simple basée sur les moyennes >= 10)
        $studentAverages = Grade::select('student_id', DB::raw('AVG(final_grade) as student_avg'))
            ->whereNotNull('final_grade')
            ->groupBy('student_id')
            ->get();

        $admis = $studentAverages->where('student_avg', '>=', 10)->count();
        $ajournes = $studentAverages->where('student_avg', '<', 10)->count();

        // 5. KPIs
        $totalStudents = Student::count();
        $successRate = $studentAverages->count() > 0 ? round(($admis / $studentAverages->count()) * 100) : 0;
        
        return view('admin.analytics.index', compact(
            'totalAbsences',
            'justifiedAbsences',
            'unjustifiedAbsences',
            'topModules',
            'flopModules',
            'retakeStats',
            'admis',
            'ajournes',
            'totalStudents',
            'successRate'
        ));
    }
}
