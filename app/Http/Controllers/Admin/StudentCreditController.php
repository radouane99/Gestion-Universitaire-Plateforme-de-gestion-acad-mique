<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Module;
use App\Models\Filiere;
use App\Models\AcademicYear;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentCreditController extends Controller
{
    /**
     * Display a listing of students with their credits & derogation status.
     */
    public function index(Request $request)
    {
        $query = Student::with(['user', 'group.filiere', 'creditModules']);

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('student_number', 'like', "%{$search}%")
                  ->orWhere('cin', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filiere Filter
        if ($request->filled('filiere_id')) {
            $query->whereHas('group', function($q) use ($request) {
                $q->where('filiere_id', $request->filiere_id);
            });
        }

        // Special Status Filters
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'derogation') {
                $query->where('has_derogation', true);
            } elseif ($status === 'last_chance') {
                $query->where('is_last_chance', true);
            } elseif ($status === 'with_credits') {
                $query->whereHas('creditModules', function($q) {
                    $q->where('student_credit_modules.status', 'pending');
                });
            }
        }

        $students = $query->paginate(15)->withQueryString();

        // Calculate Stats
        $stats = [
            'total_students' => Student::count(),
            'with_credits' => Student::whereHas('creditModules', function($q) {
                $q->where('student_credit_modules.status', 'pending');
            })->count(),
            'with_derogation' => Student::where('has_derogation', true)->count(),
            'last_chance' => Student::where('is_last_chance', true)->count(),
        ];

        $filieres = Filiere::orderBy('name')->get();

        return view('admin.students.credits.index', compact('students', 'filieres', 'stats'));
    }

    /**
     * Show the credit management dashboard for a specific student.
     */
    public function manage(Student $student)
    {
        $student->load(['user', 'group.filiere', 'creditModules']);
        
        // Modules from student's filiere
        $filiereModules = Module::where('filiere_id', $student->group->filiere_id)
            ->whereNotIn('id', $student->creditModules->pluck('id'))
            ->orderBy('name')
            ->get();

        $currentYear = AcademicYear::where('is_current', true)->first();
        
        return view('admin.students.credits.manage', compact('student', 'filiereModules', 'currentYear'));
    }

    /**
     * Add a module as credit for a student.
     */
    public function addCredit(Request $request, Student $student)
    {
        $validated = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'status' => 'required|in:pending,validated,not_validated'
        ]);

        $currentYear = AcademicYear::where('is_current', true)->first();

        try {
            $student->creditModules()->attach($validated['module_id'], [
                'academic_year_id' => $currentYear?->id,
                'status' => $validated['status']
            ]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'model_type' => 'Student',
                'description' => "Ajout du crédit module ID {$validated['module_id']} à l'étudiant {$student->user->name}.",
                'ip_address' => $request->ip()
            ]);

            return back()->with('success', 'Crédit de module ajouté avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Ce module est déjà assigné en crédit pour cet étudiant.');
        }
    }

    /**
     * Update the status of a specific credit module.
     */
    public function updateCredit(Request $request, Student $student, Module $module)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,validated,not_validated'
        ]);

        $student->creditModules()->updateExistingPivot($module->id, [
            'status' => $validated['status']
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'model_type' => 'Student',
            'description' => "Mise à jour du statut du crédit module '{$module->name}' en '{$validated['status']}' pour {$student->user->name}.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Statut du crédit mis à jour avec succès.');
    }

    /**
     * Remove a module from a student's credit list.
     */
    public function removeCredit(Request $request, Student $student, Module $module)
    {
        $student->creditModules()->detach($module->id);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'model_type' => 'Student',
            'description' => "Suppression du crédit module '{$module->name}' de l'étudiant {$student->user->name}.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Crédit de module supprimé avec succès.');
    }

    /**
     * Update the derogation status of a student.
     */
    public function updateDerogation(Request $request, Student $student)
    {
        $validated = $request->validate([
            'has_derogation' => 'required|boolean',
            'is_last_chance' => 'required|boolean',
            'derogation_note' => 'nullable|string'
        ]);

        $student->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'model_type' => 'Student',
            'description' => "Mise à jour de la dérogation pour l'étudiant {$student->user->name}. Statut: " . ($validated['has_derogation'] ? 'Acceptée' : 'Non active') . ".",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Dossier de dérogation / statut exceptionnel mis à jour.');
    }
}
