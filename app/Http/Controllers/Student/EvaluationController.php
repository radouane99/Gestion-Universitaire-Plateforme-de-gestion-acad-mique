<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Module;
use App\Models\Professor;
use App\Models\ModuleEvaluation;
use App\Models\Setting;
use App\Models\AcademicYear;
use App\Models\ActivityLog;

class EvaluationController extends Controller
{
    /**
     * Display a listing of modules available for anonymous evaluation.
     */
    public function index()
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403, 'Accès réservé aux étudiants.');
        }

        // Check if evaluations are open globally
        $isEvaluationOpen = Setting::get('evaluation_open', false);
        if (!$isEvaluationOpen) {
            return redirect()->route('student.dashboard')
                ->with('error', "La période d'évaluation des enseignements n'est pas ouverte actuellement.");
        }

        // Get modules assigned to the student's group
        $group = $student->group()->with('modules')->first();
        if (!$group) {
            return view('student.evaluations.index', [
                'modulesToEvaluate' => collect(),
                'evaluatedModuleIds' => collect()
            ]);
        }

        $modules = $group->modules;

        // Fetch professors assigned to these modules for this group's schedule
        $schedules = \App\Models\Schedule::where('group_id', $group->id)
            ->with(['module', 'professor.user'])
            ->get();

        $modulesToEvaluate = [];
        foreach ($modules as $module) {
            // Find professor from schedules
            $schedule = $schedules->firstWhere('module_id', $module->id);
            $professor = $schedule->professor ?? null;

            if ($professor) {
                // Compute the unique anonymous hash for this student and module
                $hash = md5($student->id . '_eval_' . $module->id);
                
                // Check if already evaluated
                $hasEvaluated = ModuleEvaluation::where('student_hash', $hash)
                    ->where('module_id', $module->id)
                    ->exists();

                $modulesToEvaluate[] = [
                    'module' => $module,
                    'professor' => $professor,
                    'has_evaluated' => $hasEvaluated,
                ];
            }
        }

        return view('student.evaluations.index', compact('modulesToEvaluate'));
    }

    /**
     * Store a newly created evaluation in storage (anonymously).
     */
    public function store(Request $request)
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403);
        }

        if (!Setting::get('evaluation_open', false)) {
            return back()->with('error', "La période d'évaluation est fermée.");
        }

        $validated = $request->validate([
            'module_id'    => 'required|exists:modules,id',
            'professor_id' => 'required|exists:professors,id',
            'q1_rating'    => 'required|integer|between:1,5',
            'q2_rating'    => 'required|integer|between:1,5',
            'q3_rating'    => 'required|integer|between:1,5',
            'q4_rating'    => 'required|integer|between:1,5',
            'comment'      => 'nullable|string|max:1000',
        ]);

        $hash = md5($student->id . '_eval_' . $validated['module_id']);

        // Check duplicate submission
        $exists = ModuleEvaluation::where('student_hash', $hash)
            ->where('module_id', $validated['module_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Vous avez déjà évalué ce module.');
        }

        // Get current academic year
        $academicYearId = AcademicYear::where('is_current', true)->value('id') 
            ?? $student->academic_year_id;

        ModuleEvaluation::create([
            'student_hash'     => $hash,
            'module_id'        => $validated['module_id'],
            'professor_id'     => $validated['professor_id'],
            'academic_year_id' => $academicYearId,
            'q1_rating'        => $validated['q1_rating'],
            'q2_rating'        => $validated['q2_rating'],
            'q3_rating'        => $validated['q3_rating'],
            'q4_rating'        => $validated['q4_rating'],
            'comment'          => $validated['comment'],
        ]);

        ActivityLog::log('created', 'ModuleEvaluation', "Une évaluation anonyme de cours a été soumise pour le module ID {$validated['module_id']}.");

        return back()->with('success', 'Votre évaluation a été soumise anonymement avec succès. Merci pour votre retour !');
    }
}
