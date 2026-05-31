<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Module;
use App\Models\Grade;
use App\Models\ActivityLog;
use App\Models\Student;
use App\Notifications\GradePublished;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    /**
     * Display a listing of groups and modules for grade management.
     */
    public function index()
    {
        // Admin can see all groups and all modules. 
        // A good way is to list all groups and let the admin select a module.
        $filieres = \App\Models\Filiere::orderBy('name')->get();
        $groups = Group::orderBy('level')->orderBy('name')->get();
        $modules = Module::orderBy('name')->get();
        
        return view('admin.grades.index', compact('filieres', 'groups', 'modules'));
    }

    /**
     * Show the form for editing grades for a specific group and module.
     */
    public function edit(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'module_id' => 'required|exists:modules,id',
        ]);

        $group_id = $validated['group_id'];
        $module_id = $validated['module_id'];

        $group = Group::with('students.user')->findOrFail($group_id);
        $module = Module::findOrFail($module_id);
        
        $grades = Grade::where('module_id', $module_id)
            ->whereIn('student_id', $group->students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        return view('admin.grades.edit', compact('group', 'module', 'grades'));
    }

    /**
     * Store or update grades.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'module_id' => 'required|exists:modules,id',
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.cc1' => 'nullable|numeric|between:0,20',
            'grades.*.cc2' => 'nullable|numeric|between:0,20',
            'grades.*.exam' => 'nullable|numeric|between:0,20',
        ]);

        foreach ($validated['grades'] as $student_id => $data) {
            $grade = Grade::updateOrCreate(
                ['student_id' => $data['student_id'], 'module_id' => $validated['module_id']],
                [
                    'cc1' => $data['cc1'],
                    'cc2' => $data['cc2'],
                    'exam' => $data['exam']
                ]
            );
            $grade->calculateFinalGrade();

            // 🔔 Notifier l'étudiant de sa note
            if ($grade->final_grade !== null) {
                $student = Student::find($data['student_id']);
                $student?->user?->notify(new GradePublished(
                    $module->name ?? 'Module',
                    (float) $grade->final_grade
                ));
            }
        }

        $module = Module::find($validated['module_id']);
        $group = Group::find($validated['group_id']);
        
        ActivityLog::log(
            'updated',
            'Grade',
            "Saisie globale des notes pour le groupe '{$group->name}' - module '{$module->name}' par l'administration (" . (auth()->user()->name ?? '') . ")"
        );

        return redirect()->route('admin.grades.index')->with('success', 'Les notes ont été mises à jour avec succès.');
    }
}
