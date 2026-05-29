<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DisciplineCase;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentRiskController extends Controller
{
    public function index(Request $request)
    {
        $allStudents = Student::with(['user', 'group.filiere', 'absences', 'grades'])->get();

        $studentsWithRisk = $allStudents->map(function ($student) {
            $absences = $student->absence_score;
            $grades = $student->grades()->where('is_archived', false)->pluck('final_grade');
            $moyenne = $grades->count() > 0 ? $grades->average() : null;

            // Classify risk level based on logic
            $riskLevel = 'normal';
            $riskLabel = 'Normal';
            $riskColor = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            
            if ($absences > 18) {
                $riskLevel = 'discipline_council';
                $riskLabel = 'Conseil de discipline';
                $riskColor = 'bg-rose-100 text-rose-800 border-rose-200 font-extrabold animate-pulse';
            } elseif ($absences > 12 || ($moyenne !== null && $moyenne < 8)) {
                $riskLevel = 'pedagogical_risk';
                $riskLabel = 'Risque Pédagogique';
                $riskColor = 'bg-orange-100 text-orange-800 border-orange-200';
            } elseif ($absences > 6 || ($moyenne !== null && $moyenne < 10)) {
                $riskLevel = 'to_watch';
                $riskLabel = 'À Surveiller';
                $riskColor = 'bg-amber-100 text-amber-800 border-amber-200';
            }

            return (object) [
                'student' => $student,
                'absences' => $absences,
                'moyenne' => $moyenne,
                'risk_level' => $riskLevel,
                'risk_label' => $riskLabel,
                'risk_color' => $riskColor,
            ];
        });

        // Optional filter
        if ($request->filled('risk_level')) {
            $studentsWithRisk = $studentsWithRisk->filter(fn($item) => $item->risk_level === $request->risk_level);
        }

        // Paginate manually since it's a mapped collection
        $page = $request->get('page', 1);
        $perPage = 15;
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $studentsWithRisk->forPage($page, $perPage),
            $studentsWithRisk->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $stats = [
            'total' => $studentsWithRisk->count(),
            'normal' => $studentsWithRisk->where('risk_level', 'normal')->count(),
            'to_watch' => $studentsWithRisk->where('risk_level', 'to_watch')->count(),
            'pedagogical_risk' => $studentsWithRisk->where('risk_level', 'pedagogical_risk')->count(),
            'discipline_council' => $studentsWithRisk->where('risk_level', 'discipline_council')->count(),
        ];

        return view('admin.students-risk.index', compact('paginated', 'stats'));
    }

    public function summon(Student $student)
    {
        // 1. Create or update discipline case
        $disciplineCase = DisciplineCase::updateOrCreate(
            [
                'student_id' => $student->id,
                'status' => 'open',
            ],
            [
                'total_unjustified_hours' => $student->absence_score,
                'admin_comment' => 'Étudiant convoqué au conseil de discipline suite à une détection par le système prédictif (Étudiant à Risque).',
            ]
        );

        // 2. Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'DISCIPLINE_SUMMON',
            'description' => "L'étudiant " . $student->user?->name . " (Matricule: " . $student->student_number . ") a été convoqué au conseil de discipline via le monitoring prédictif.",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.students_risk.index')->with('success', "L'étudiant a été convoqué avec succès au conseil de discipline. Un dossier de discipline actif a été généré.");
    }
}
