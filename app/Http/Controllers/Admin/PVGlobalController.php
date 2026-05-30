<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Filiere;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Module;
use App\Models\Grade;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\PVCompilerTrait;

class PVGlobalController extends Controller
{
    use PVCompilerTrait;
    /**
     * Display the PV Global dashboard.
     */
    public function index(Request $request)
    {
        $filieres = Filiere::orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('name')->get();
        
        $activeYear = AcademicYear::where('is_current', true)->first() ?? AcademicYear::latest()->first();
        $currentYearId = $request->get('academic_year_id', $activeYear ? $activeYear->id : null);
        
        $selectedFiliereId = $request->get('filiere_id');
        $selectedLevel = $request->get('level'); // 1, 2, 3
        $selectedSemesterId = $request->get('semester_id'); // If set, single semester PV. Otherwise, annual PV for the level.

        // Get semesters for the selected level if selected
        $semesters = collect();
        if ($selectedLevel) {
            $semesters = Semester::where('level', $selectedLevel)->orderBy('name')->get();
        }

        $modules = collect();
        $students = collect();
        $grades = collect();
        $isAnnual = true;
        $semester = null;

        if ($selectedFiliereId && $selectedLevel) {
            if ($selectedSemesterId) {
                $isAnnual = false;
                $semester = Semester::find($selectedSemesterId);
                $modules = Module::where('filiere_id', $selectedFiliereId)
                    ->where('semester_id', $selectedSemesterId)
                    ->orderBy('id')
                    ->get();
            } else {
                $isAnnual = true;
                $modules = Module::where('filiere_id', $selectedFiliereId)
                    ->whereIn('semester_id', $semesters->pluck('id'))
                    ->with('semester')
                    ->orderBy('semester_id')
                    ->orderBy('id')
                    ->get();
            }

            // Fetch students matching filiere, level and year
            $students = Student::whereHas('group', function ($q) use ($selectedFiliereId, $selectedLevel) {
                $q->where('filiere_id', $selectedFiliereId)->where('level', $selectedLevel);
            })
            ->where('academic_year_id', $currentYearId)
            ->with(['user', 'group', 'absences', 'disciplineCases'])
            ->get();

            if ($students->isNotEmpty() && $modules->isNotEmpty()) {
                $grades = Grade::whereIn('student_id', $students->pluck('id'))
                    ->whereIn('module_id', $modules->pluck('id'))
                    ->get()
                    ->groupBy('student_id');
            }
        }

        // Fetch settings for absence thresholds
        $settings = Setting::first() ?? new Setting();

        // Calculate compiled data for each student
        $pvData = $this->compilePVData($students, $modules, $grades, $isAnnual, $semesters);

        return view('admin.pv_globaux.index', compact(
            'filieres',
            'academicYears',
            'currentYearId',
            'selectedFiliereId',
            'selectedLevel',
            'selectedSemesterId',
            'semesters',
            'modules',
            'students',
            'pvData',
            'isAnnual',
            'semester',
            'settings'
        ));
    }

    /**
     * Export the PV Global as Excel (.xls HTML table).
     */
    public function exportExcel(Request $request)
    {
        $filiere = Filiere::findOrFail($request->filiere_id);
        $academicYear = AcademicYear::findOrFail($request->academic_year_id);
        $selectedLevel = $request->level;
        $selectedSemesterId = $request->semester_id;

        $semesters = Semester::where('level', $selectedLevel)->orderBy('name')->get();
        $isAnnual = !$selectedSemesterId;
        $semester = $selectedSemesterId ? Semester::find($selectedSemesterId) : null;

        if ($semester) {
            $modules = Module::where('filiere_id', $filiere->id)
                ->where('semester_id', $semester->id)
                ->orderBy('id')
                ->get();
        } else {
            $modules = Module::where('filiere_id', $filiere->id)
                ->whereIn('semester_id', $semesters->pluck('id'))
                ->orderBy('semester_id')
                ->orderBy('id')
                ->get();
        }

        $students = Student::whereHas('group', function ($q) use ($filiere, $selectedLevel) {
            $q->where('filiere_id', $filiere->id)->where('level', $selectedLevel);
        })
        ->where('academic_year_id', $academicYear->id)
        ->with(['user', 'group', 'absences', 'disciplineCases'])
        ->get();

        $grades = collect();
        if ($students->isNotEmpty() && $modules->isNotEmpty()) {
            $grades = Grade::whereIn('student_id', $students->pluck('id'))
                ->whereIn('module_id', $modules->pluck('id'))
                ->get()
                ->groupBy('student_id');
        }

        $pvData = $this->compilePVData($students, $modules, $grades, $isAnnual, $semesters);
        $settings = Setting::first() ?? new Setting();

        $title = $isAnnual 
            ? "PV_Global_{$filiere->name}_Niveau_{$selectedLevel}_{$academicYear->name}"
            : "PV_Semestre_{$semester->name}_{$filiere->name}_{$academicYear->name}";

        $title = str_replace([' ', '/', '\\'], '_', $title);

        $viewName = $isAnnual ? 'admin.pv_globaux.excel_annual' : 'admin.pv_globaux.excel_semester';
        
        $content = view($viewName, compact(
            'filiere',
            'academicYear',
            'selectedLevel',
            'semester',
            'semesters',
            'modules',
            'students',
            'pvData',
            'settings'
        ))->render();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$title}.xls\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Export the PV Global as landscape A4 PDF.
     */
    public function exportPdf(Request $request)
    {
        $filiere = Filiere::findOrFail($request->filiere_id);
        $academicYear = AcademicYear::findOrFail($request->academic_year_id);
        $selectedLevel = $request->level;
        $selectedSemesterId = $request->semester_id;

        $semesters = Semester::where('level', $selectedLevel)->orderBy('name')->get();
        $isAnnual = !$selectedSemesterId;
        $semester = $selectedSemesterId ? Semester::find($selectedSemesterId) : null;

        if ($semester) {
            $modules = Module::where('filiere_id', $filiere->id)
                ->where('semester_id', $semester->id)
                ->orderBy('id')
                ->get();
        } else {
            $modules = Module::where('filiere_id', $filiere->id)
                ->whereIn('semester_id', $semesters->pluck('id'))
                ->orderBy('semester_id')
                ->orderBy('id')
                ->get();
        }

        $students = Student::whereHas('group', function ($q) use ($filiere, $selectedLevel) {
            $q->where('filiere_id', $filiere->id)->where('level', $selectedLevel);
        })
        ->where('academic_year_id', $academicYear->id)
        ->with(['user', 'group', 'absences', 'disciplineCases'])
        ->get();

        $grades = collect();
        if ($students->isNotEmpty() && $modules->isNotEmpty()) {
            $grades = Grade::whereIn('student_id', $students->pluck('id'))
                ->whereIn('module_id', $modules->pluck('id'))
                ->get()
                ->groupBy('student_id');
        }

        $pvData = $this->compilePVData($students, $modules, $grades, $isAnnual, $semesters);
        $settings = Setting::first() ?? new Setting();

        $viewName = $isAnnual ? 'pdf.pv_annual' : 'pdf.pv_semester';
        
        $pdf = Pdf::loadView($viewName, compact(
            'filiere',
            'academicYear',
            'selectedLevel',
            'semester',
            'semesters',
            'modules',
            'students',
            'pvData',
            'settings'
        ));

        // Landscape format for huge dense grids
        $pdf->setPaper('A4', 'landscape');

        $title = $isAnnual 
            ? "PV_Global_{$filiere->name}_Niveau_{$selectedLevel}_{$academicYear->name}.pdf"
            : "PV_Semestre_{$semester->name}_{$filiere->name}_{$academicYear->name}.pdf";

        return $pdf->download(str_replace([' ', '/', '\\'], '_', $title));
    }

    /**
     * Validate the PV Global for a given cohort.
     */
    public function validatePV(Request $request)
    {
        $request->validate([
            'filiere_id' => 'required|exists:filieres,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'level' => 'required|string',
        ]);

        \App\Models\PVGlobalApproval::updateOrCreate(
            [
                'filiere_id' => $request->filiere_id,
                'academic_year_id' => $request->academic_year_id,
                'level' => $request->level,
            ],
            [
                'is_validated' => true,
                'validated_by' => auth()->id(),
                'validated_at' => now(),
            ]
        );

        // Notify all students in this cohort
        $students = Student::whereHas('group', function ($q) use ($request) {
            $q->where('filiere_id', $request->filiere_id)->where('level', $request->level);
        })
        ->where('academic_year_id', $request->academic_year_id)
        ->get();

        foreach ($students as $student) {
            $student->user->notify(new \App\Notifications\AcademicNotification(
                "🎉 Délibérations Terminées ! Le PV académique de votre niveau (" . $request->level . "ème année) a été officiellement validé. Vos documents officiels sont disponibles.",
                'success',
                route('student.dashboard')
            ));
        }

        return back()->with('success', 'Le Procès-Verbal Global a été officiellement validé avec succès. Les attestations de réussite et diplômes sont désormais débloqués pour les étudiants éligibles ! 🎓');
    }

    /**
     * Cancel the validation of a PV Global.
     */
    public function rejectPV(Request $request)
    {
        $request->validate([
            'filiere_id' => 'required|exists:filieres,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'level' => 'required|string',
        ]);

        \App\Models\PVGlobalApproval::where([
            'filiere_id' => $request->filiere_id,
            'academic_year_id' => $request->academic_year_id,
            'level' => $request->level,
        ])->delete();

        return back()->with('success', 'La validation du Procès-Verbal Global a été annulée. Les attestations et diplômes sont à nouveau verrouillés.');
    }
}
