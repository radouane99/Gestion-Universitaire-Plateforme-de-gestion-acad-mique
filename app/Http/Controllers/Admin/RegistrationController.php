<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Filiere;
use App\Models\Group;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['user', 'filiere', 'group']);

        // Filters
        if ($request->filled('filiere_id')) {
            $query->where('filiere_id', $request->filiere_id);
        }

        if ($request->filled('status')) {
            $query->where('registration_status', $request->status);
        } else {
            // Default show pending
            $query->where('registration_status', 'pending');
        }

        if ($request->filled('type')) {
            $query->where('registration_type', $request->type);
        }

        $students = $query->orderBy('created_at', 'desc')->get();
        $filieres = Filiere::all();

        return view('admin.registrations.index', compact('students', 'filieres'));
    }

    public function approve(Request $request, Student $student)
    {
        $student->update([
            'registration_status' => 'approved',
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'approved',
            'model_type' => 'Student',
            'description' => "Candidature d'inscription de '{$student->user->name}' approuvée.",
            'ip_address' => $request->ip(),
        ]);

        // Notify Student
        $student->user->notify(new \App\Notifications\AcademicNotification(
            "🎉 Félicitations ! Votre dossier d'inscription a été APPROUVÉ par la direction académique. Vous serez prochainement affecté à votre groupe d'études.",
            'success',
            route('dashboard')
        ));

        return back()->with('success', "Le dossier de l'étudiant {$student->user->name} a été approuvé avec succès !");
    }

    public function reject(Request $request, Student $student)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $student->update([
            'registration_status' => 'rejected',
            'derogation_note' => $request->rejection_reason, // Re-use notes field to store rejection reason
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'rejected',
            'model_type' => 'Student',
            'description' => "Candidature d'inscription de '{$student->user->name}' rejetée. Motif : {$request->rejection_reason}",
            'ip_address' => $request->ip(),
        ]);

        // Notify Student
        $student->user->notify(new \App\Notifications\AcademicNotification(
            "❌ Votre dossier d'inscription a été REFUSÉ. Motif : " . $request->rejection_reason,
            'error',
            route('dashboard')
        ));

        return back()->with('success', "Le dossier de l'étudiant {$student->user->name} a été rejeté.");
    }

    /**
     * Round-Robin Balanced Group Dispatcher (Pro Max)
     */
    public function autoDispatch(Request $request)
    {
        $request->validate([
            'filiere_id' => 'required|exists:filieres,id',
        ]);

        $filiere = Filiere::findOrFail($request->filiere_id);

        // 1. Get approved new students for this filiere who don't have a group yet
        $students = Student::where('filiere_id', $filiere->id)
            ->where('registration_status', 'approved')
            ->where('registration_type', 'new')
            ->whereNull('group_id')
            ->get();

        if ($students->isEmpty()) {
            return back()->with('warning', "Aucun étudiant approuvé sans groupe trouvé pour la filière : {$filiere->name}");
        }

        // 2. Get first-year groups associated with this filiere
        // We look for groups that are Level "Licence 1" or end in "-1" or simply belong to S1 of this filiere.
        // Let's get all groups belonging to this filiere that contain '1' in their name or level, or just all groups of the filiere
        $groups = Group::where('filiere_id', $filiere->id)->get();

        if ($groups->isEmpty()) {
            return back()->with('error', "Aucun groupe d'études trouvé pour la filière : {$filiere->name}. Veuillez d'abord créer des groupes (ex: GI-1, GI-2).");
        }

        // We filter for Year 1 groups if possible, or use all of them if only Year 1 groups exist.
        // Usually, in a clean database, new registrations enter Level 1 groups. Let's look for groups with Level containing '1' or 'Licence 1'
        $level1Groups = $groups->filter(function($g) {
            return stripos($g->level, '1') !== false || stripos($g->name, '1') !== false;
        });

        $targetGroups = $level1Groups->isNotEmpty() ? $level1Groups->values() : $groups->values();
        $groupCount = $targetGroups->count();

        // 3. Balanced Dispatch using Round-Robin Distribution
        $assignments = [];
        foreach ($targetGroups as $g) {
            $assignments[$g->id] = 0;
        }

        $currentYearText = date('Y');

        DB::transaction(function () use ($students, $targetGroups, $groupCount, $currentYearText, &$assignments) {
            foreach ($students as $index => $student) {
                // Round-Robin group selection
                $targetGroup = $targetGroups[$index % $groupCount];

                // Generate official permanent student number (format: EST-YYYY-000000)
                $paddedId = str_pad($student->id, 5, '0', STR_PAD_LEFT);
                $officialStudentNumber = "EST-{$currentYearText}-{$paddedId}";

                $student->update([
                    'group_id' => $targetGroup->id,
                    'student_number' => $officialStudentNumber,
                ]);

                $assignments[$targetGroup->id]++;
            }
        });

        // Log action
        $assignedTotal = $students->count();
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'model_type' => 'Student',
            'description' => "Affectation automatique équilibrée de {$assignedTotal} étudiant(s) dans la filière {$filiere->name}.",
            'ip_address' => $request->ip(),
        ]);

        $summary = [];
        foreach ($targetGroups as $g) {
            if ($assignments[$g->id] > 0) {
                $summary[] = "<strong>{$g->name}</strong> : +{$assignments[$g->id]}";
            }
        }

        $msg = "🎉 Affectation équilibrée réussie ! {$assignedTotal} étudiants ont été répartis uniformément : " . implode(', ', $summary);

        return back()->with('success', $msg);
    }
}
