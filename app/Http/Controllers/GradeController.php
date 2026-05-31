<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\GradePublished;

class GradeController extends Controller
{
    public function index()
    {
        $professor = Auth::user()->professor;
        // Get modules taught by this professor from schedule
        $taught = \App\Models\Schedule::where('professor_id', $professor->id)
            ->with(['group', 'module'])
            ->get()
            ->unique(function ($item) {
                return $item->group_id . '-' . $item->module_id;
            });

        return view('professor.grades.index', compact('taught'));
    }

    public function editGroup($group_id, $module_id)
    {
        $professor = Auth::user()->professor;
        if (!$professor) {
            abort(403, "Accès réservé aux professeurs.");
        }

        // Verify if professor teaches this module to this group
        $isAuthorized = \App\Models\Schedule::where('professor_id', $professor->id)
            ->where('group_id', $group_id)
            ->where('module_id', $module_id)
            ->exists();

        if (!$isAuthorized) {
            abort(403, "Vous n'êtes pas autorisé à saisir les notes de ce groupe pour ce module.");
        }

        $group = \App\Models\Group::with('students.user')->findOrFail($group_id);
        $module = \App\Models\Module::findOrFail($module_id);

        // Get regular group students
        $students = $group->students;

        // Get students with pending credit for this module from other groups
        $creditStudentIds = \DB::table('student_credit_modules')
            ->where('module_id', $module_id)
            ->where('status', 'pending')
            ->pluck('student_id');

        $creditStudents = \App\Models\Student::whereIn('id', $creditStudentIds)
            ->where('group_id', '!=', $group_id)
            ->with(['user', 'group'])
            ->get();

        // Combine regular students and credit students
        $allStudents = $students->concat($creditStudents);

        $grades = \App\Models\Grade::where('module_id', $module_id)
            ->whereIn('student_id', $allStudents->pluck('id'))
            ->get()
            ->keyBy('student_id');

        return view('professor.grades.edit', compact('group', 'module', 'grades', 'allStudents'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'module_id'             => 'required|exists:modules,id',
            'grades'                => 'required|array',
            'grades.*.student_id'   => 'required|exists:students,id',
            'grades.*.cc1'          => 'nullable|numeric|between:0,20',
            'grades.*.cc2'          => 'nullable|numeric|between:0,20',
            'grades.*.exam'         => 'nullable|numeric|between:0,20',
        ]);

        $professor = Auth::user()->professor;
        if (!$professor) {
            abort(403, "Accès réservé aux professeurs.");
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $professor) {
            foreach ($validated['grades'] as $student_id => $data) {
                $student = \App\Models\Student::findOrFail($data['student_id']);

                // Verify professor teaches this student's group for this module
                $isAuthorized = \App\Models\Schedule::where('professor_id', $professor->id)
                    ->where('module_id', $validated['module_id'])
                    ->where('group_id', $student->group_id)
                    ->exists();

                if (!$isAuthorized) {
                    // Check if student has a pending credit for this module
                    $hasCredit = \DB::table('student_credit_modules')
                        ->where('student_id', $student->id)
                        ->where('module_id', $validated['module_id'])
                        ->where('status', 'pending')
                        ->exists();

                    if (!$hasCredit) {
                        abort(403, "Vous n'êtes pas autorisé à modifier les notes de l'étudiant {$student->user->name} pour ce cours.");
                    }
                }

                $grade = \App\Models\Grade::updateOrCreate(
                    ['student_id' => $data['student_id'], 'module_id' => $validated['module_id']],
                    [
                        'cc1'  => $data['cc1'],
                        'cc2'  => $data['cc2'],
                        'exam' => $data['exam'],
                    ]
                );
                $grade->calculateFinalGrade();

                // 🔔 Notifier l'étudiant si une note finale vient d'être calculée
                if ($grade->final_grade !== null) {
                    $student->user?->notify(new GradePublished(
                        $module->name ?? 'Module',
                        (float) $grade->final_grade
                    ));
                }
            }
        });

        $module = \App\Models\Module::find($validated['module_id']);
        \App\Models\ActivityLog::log(
            'updated',
            'Grade',
            "Saisie des notes pour le module '{$module->name}' par le professeur " . (auth()->user()->name ?? '')
        );

        return back()->with('success', 'Grades updated successfully.');
    }
}
