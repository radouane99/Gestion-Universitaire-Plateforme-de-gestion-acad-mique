<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Reclamation;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReclamationController extends Controller
{
    public function index()
    {
        $professor = Auth::user()->professor;
        if (!$professor) {
            abort(403, 'Profil professeur introuvable.');
        }

        // Fetch modules taught by this professor
        $moduleIds = Schedule::where('professor_id', $professor->id)->pluck('module_id')->unique();

        $reclamations = Reclamation::with(['student.user', 'module', 'grade'])
            ->whereIn('module_id', $moduleIds)
            ->latest()
            ->paginate(15);

        return view('professor.reclamations.index', compact('reclamations'));
    }

    public function resolve(Request $request, Reclamation $reclamation)
    {
        $professor = Auth::user()->professor;
        if (!$professor) {
            abort(403, 'Profil professeur introuvable.');
        }

        $request->validate([
            'status' => 'required|in:accepted,rejected',
            'prof_comment' => 'required|string|min:5',
            'cc1' => 'nullable|numeric|min:0|max:20',
            'cc2' => 'nullable|numeric|min:0|max:20',
            'exam' => 'nullable|numeric|min:0|max:20',
        ]);

        $reclamation->status = $request->status;
        $reclamation->prof_comment = $request->prof_comment;
        $reclamation->save();

        if ($request->status === 'accepted') {
            $grade = $reclamation->grade;
            if ($grade) {
                if ($request->filled('cc1')) $grade->cc1 = $request->cc1;
                if ($request->filled('cc2')) $grade->cc2 = $request->cc2;
                if ($request->filled('exam')) $grade->exam = $request->exam;
                
                // Recalculate final grade
                $grade->calculateFinalGrade();
                
                // Log grade change
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'GRADE_UPDATED_VIA_RECLAMATION',
                    'description' => "Note modifiée suite à une réclamation acceptée pour l'étudiant " . $reclamation->student?->user?->name . " sur le module " . $reclamation->module?->name,
                    'ip_address' => $request->ip(),
                ]);
            }
        }

        return redirect()->route('professor.reclamations.index')
            ->with('success', 'La réclamation a été résolue avec succès.');
    }
}
