<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Assignment;
use App\Models\Professor;
use App\Models\Module;
use App\Models\Group;
use App\Exports\AssignmentsExport;
use App\Imports\AssignmentsImport;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $semesters = Semester::orderBy('level')->orderBy('name')->get();
        
        $currentYear = AcademicYear::where('is_current', true)->first();
        
        // Assignments for current year
        $assignments = [];
        if ($currentYear) {
            $assignments = Assignment::where('academic_year_id', $currentYear->id)
                ->with(['professor.user', 'module', 'group'])
                ->get();
        }

        $professors = Professor::with('user')->get();
        $modules = Module::all();
        $groups = Group::all();

        // Load exam sessions for current year
        $examSessions = [];
        if ($currentYear) {
            $sessions = \App\Models\ExamSession::where('academic_year_id', $currentYear->id)->get();
            foreach (['normal_autumn', 'normal_spring', 'retake_autumn', 'retake_spring'] as $type) {
                $examSessions[$type] = $sessions->firstWhere('type', $type);
            }
        }

        return view('admin.academic.index', compact('academicYears', 'semesters', 'currentYear', 'assignments', 'professors', 'modules', 'groups', 'examSessions'));
    }

    public function storeYear(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:academic_years,name'
        ]);

        $year = AcademicYear::create($validated);

        if ($request->has('set_current')) {
            AcademicYear::where('id', '!=', $year->id)->update(['is_current' => false]);
            $year->update(['is_current' => true]);
        }

        return back()->with('success', 'Année universitaire ajoutée avec succès.');
    }

    public function setCurrentYear(Request $request, AcademicYear $year)
    {
        AcademicYear::where('id', '!=', $year->id)->update(['is_current' => false]);
        $year->update(['is_current' => true]);

        return back()->with('success', "L'année {$year->name} est maintenant l'année en cours.");
    }

    public function setExamSessions(Request $request, AcademicYear $year)
    {
        $validated = $request->validate([
            'sessions' => 'required|array',
            'sessions.*.start_date' => 'nullable|date',
            'sessions.*.end_date'   => 'nullable|date|after_or_equal:sessions.*.start_date',
        ]);

        foreach ($validated['sessions'] as $type => $dates) {
            if (in_array($type, ['normal_autumn', 'normal_spring', 'retake_autumn', 'retake_spring'])) {
                \App\Models\ExamSession::updateOrCreate(
                    ['academic_year_id' => $year->id, 'type' => $type],
                    ['start_date' => $dates['start_date'] ?? null, 'end_date' => $dates['end_date'] ?? null]
                );
            }
        }

        return back()->with('success', "Périodes d'examens mises à jour pour l'année {$year->name}.");
    }

    public function storeAssignment(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'professor_id' => 'required|exists:professors,id',
            'module_id' => 'required|exists:modules,id',
            'group_id' => 'required|exists:groups,id',
        ]);

        // Check unique constraint manually to provide a better error message
        $exists = Assignment::where('professor_id', $validated['professor_id'])
            ->where('module_id', $validated['module_id'])
            ->where('group_id', $validated['group_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Cette affectation existe déjà.');
        }

        Assignment::create($validated);

        return back()->with('success', 'Affectation ajoutée avec succès.');
    }

    public function destroyAssignment(Assignment $assignment)
    {
        $assignment->delete();
        return back()->with('success', 'Affectation supprimée avec succès.');
    }

    public function exportAssignments(Request $request)
    {
        $currentYear = AcademicYear::where('is_current', true)->first();
        $yearId  = $request->query('year_id', $currentYear?->id);
        $yearName = AcademicYear::find($yearId)?->name ?? 'all';
        $filename = 'affectations_professeurs_' . str_replace('/', '-', $yearName) . '_' . now()->format('Ymd') . '.xlsx';

        return (new AssignmentsExport($yearId))->download($filename);
    }

    public function importAssignments(Request $request)
    {
        $request->validate([
            'file'             => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $import = new AssignmentsImport((int) $request->academic_year_id);
        $import->import($request->file('file'));

        $msg = "Import terminé : {$import->importedCount} affectation(s) ajoutée(s)";
        if ($import->skippedCount > 0) {
            $msg .= ", {$import->skippedCount} ignorée(s)";
        }
        if (!empty($import->errors)) {
            $msg .= '. Erreurs : ' . implode(' | ', array_slice($import->errors, 0, 5));
            return back()->with('warning', $msg);
        }

        return back()->with('success', $msg . '.');
    }

    public function downloadTemplate()
    {
        // Simple CSV template
        $csv = "email_professeur,module_code,groupe\n";
        $csv .= "prof@universite.ma,INFO101,Groupe A\n";
        $csv .= "autre@universite.ma,MATH201,Groupe B\n";

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="template_affectations.csv"');
    }
}
