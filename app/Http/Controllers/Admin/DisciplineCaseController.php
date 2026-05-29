<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DisciplineCase;
use App\Models\Student;
use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisciplineCaseController extends Controller
{
    /**
     * Liste des dossiers de discipline.
     */
    public function index(Request $request)
    {
        $query = DisciplineCase::with(['student.user', 'student.group.filiere', 'treatedBy'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $cases = $query->paginate(20);

        // Stats rapides
        $discipline = Setting::get('absence_discipline_threshold', 120);
        $stats = [
            'open'     => DisciplineCase::where('status', 'open')->count(),
            'notified' => DisciplineCase::where('status', 'notified')->count(),
            'treated'  => DisciplineCase::where('status', 'treated')->count(),
        ];

        return view('admin.discipline.index', compact('cases', 'stats', 'discipline'));
    }

    /**
     * Détail d'un dossier de discipline.
     */
    public function show(DisciplineCase $case)
    {
        $case->load(['student.user', 'student.group.filiere', 'student.absences.module', 'treatedBy']);

        $student = $case->student;
        $absencesByModule = $student->absences()
            ->with('module')
            ->get()
            ->groupBy('module.name');

        return view('admin.discipline.show', compact('case', 'student', 'absencesByModule'));
    }

    /**
     * Marquer un dossier comme traité avec commentaire admin.
     */
    public function treat(Request $request, DisciplineCase $case)
    {
        $request->validate([
            'admin_comment' => 'required|string|min:10|max:1000',
        ]);

        $case->update([
            'status'        => 'treated',
            'admin_comment' => $request->admin_comment,
            'treated_at'    => now(),
            'treated_by'    => Auth::id(),
        ]);

        ActivityLog::log(
            'updated',
            'DisciplineCase',
            "Dossier discipline #{$case->id} de l'étudiant #{$case->student_id} marqué comme traité."
        );

        return back()->with('success', 'Dossier de discipline marqué comme traité avec succès.');
    }

    /**
     * Recréer manuellement un dossier de discipline pour un étudiant.
     */
    public function create(Request $request)
    {
        $request->validate(['student_id' => 'required|exists:students,id']);

        $student = Student::findOrFail($request->student_id);
        $score   = $student->absence_score;

        $case = DisciplineCase::create([
            'student_id'              => $student->id,
            'total_unjustified_hours' => $score,
            'status'                  => 'open',
        ]);

        ActivityLog::log(
            'created',
            'DisciplineCase',
            "Dossier discipline créé manuellement pour l'étudiant #{$student->id} ({$score}h)."
        );

        return redirect()->route('admin.discipline.show', $case)->with('success', 'Dossier créé.');
    }
}
