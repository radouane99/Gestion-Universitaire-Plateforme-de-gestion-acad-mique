<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Exam;
use App\Models\Filiere;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Module;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Setting;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $filieres = Filiere::all();
        $groups = Group::all();
        $modules = Module::all();
        return view('admin.reports.index', compact('filieres', 'groups', 'modules'));
    }

    public function exportAbsences(Request $request)
    {
        $settings = Setting::first();
        $query = Student::with(['user', 'group.filiere']);

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        } elseif ($request->filled('filiere_id')) {
            $query->whereHas('group', function ($q) use ($request) {
                $q->where('filiere_id', $request->filiere_id);
            });
        }

        $students = $query->get()->sortByDesc('absence_score');
        $groupName = $request->filled('group_id') ? Group::find($request->group_id)?->name : 'Tous';

        $pdf = Pdf::loadView('pdf.reports.absences', compact('students', 'settings', 'groupName'));
        return $pdf->download('rapport_absences_' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportGrades(Request $request)
    {
        $settings = Setting::first();
        $request->validate([
            'module_id' => 'required|exists:modules,id',
        ]);

        $module = Module::with('filiere')->find($request->module_id);
        $grades = Grade::with('student.user')
            ->where('module_id', $request->module_id)
            ->where('is_archived', false)
            ->get();

        $stats = [
            'avg' => $grades->avg('final_grade') ?? 0,
            'max' => $grades->max('final_grade') ?? 0,
            'min' => $grades->min('final_grade') ?? 0,
            'passed' => $grades->where('final_grade', '>=', 10)->count(),
            'failed' => $grades->where('final_grade', '<', 10)->count(),
        ];

        $pdf = Pdf::loadView('pdf.reports.grades', compact('grades', 'module', 'stats', 'settings'));
        return $pdf->download('rapport_notes_' . $module->code . '_' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportExams(Request $request)
    {
        $settings = Setting::first();
        $exams = Exam::with(['module', 'group', 'room', 'proctors.user'])
            ->where('is_archived', false)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        $pdf = Pdf::loadView('pdf.reports.exams', compact('exams', 'settings'));
        return $pdf->download('rapport_examens_' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportRooms(Request $request)
    {
        $settings = Setting::first();
        $rooms = Room::with('reservations.professor.user')->get();

        $pdf = Pdf::loadView('pdf.reports.rooms', compact('rooms', 'settings'));
        return $pdf->download('occupations_salles_' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportAtRisk(Request $request)
    {
        $settings = Setting::first();
        
        // Load all active students and calculate risk
        $allStudents = Student::with(['user', 'group.filiere', 'absences', 'grades'])->get();
        
        $atRiskStudents = $allStudents->map(function ($student) {
            $absences = $student->absence_score;
            $grades = $student->grades()->where('is_archived', false)->pluck('final_grade');
            $moyenne = $grades->count() > 0 ? $grades->average() : null;

            // Classify risk level
            $risk = 'Normal';
            $color = 'text-emerald-600';
            
            if ($absences > 18) {
                $risk = 'Conseil de discipline';
                $color = 'text-red-700';
            } elseif ($absences > 12 || ($moyenne !== null && $moyenne < 8)) {
                $risk = 'Risque pédagogique';
                $color = 'text-orange-600';
            } elseif ($absences > 6 || ($moyenne !== null && $moyenne < 10)) {
                $risk = 'À surveiller';
                $color = 'text-amber-600';
            }

            return (object) [
                'student' => $student,
                'absences' => $absences,
                'moyenne' => $moyenne,
                'risk' => $risk,
                'color' => $color,
            ];
        })->filter(fn($item) => $item->risk !== 'Normal');

        $pdf = Pdf::loadView('pdf.reports.at_risk', compact('atRiskStudents', 'settings'));
        return $pdf->download('rapport_etudiants_a_risque_' . now()->format('Ymd_His') . '.pdf');
    }
}
