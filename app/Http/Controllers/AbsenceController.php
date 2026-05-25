<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Absence;
use App\Models\Schedule;
use App\Models\ActivityLog;

class AbsenceController extends Controller
{
    public function index()
    {
        $professor = Auth::user()->professor;
        $taught = Schedule::where('professor_id', $professor->id)
            ->with(['group', 'module'])
            ->get();

        return view('professor.absences.index', compact('taught'));
    }

    public function createForm($schedule_id)
    {
        $professor = Auth::user()->professor;
        $session = Schedule::with(['group.students.user', 'module'])->findOrFail($schedule_id);
        if (!$professor || $session->professor_id !== $professor->id) {
            abort(403, "Vous n'êtes pas autorisé à modifier les absences pour ce cours.");
        }
        return view('professor.absences.create', compact('session'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'absences'    => 'required|array',
            'date'        => 'required|date',
            'session_type' => 'required|string',
        ]);

        $schedule = Schedule::with('group.students')->findOrFail($validated['schedule_id']);
        $professor = Auth::user()->professor;
        if (!$professor || $schedule->professor_id !== $professor->id) {
            abort(403, "Vous n'êtes pas autorisé à enregistrer des absences pour ce cours.");
        }

        // Security: only accept student IDs that actually belong to this group
        $authorizedStudentIds = $schedule->group->students->pluck('id')->toArray();
        foreach (array_keys($validated['absences']) as $student_id) {
            if (!in_array($student_id, $authorizedStudentIds)) {
                abort(403, "L'étudiant #{$student_id} n'appartient pas à ce groupe.");
            }
        }

        $durationHours = \Carbon\Carbon::parse($schedule->start_time)->diffInHours(\Carbon\Carbon::parse($schedule->end_time));
        if ($durationHours <= 0) $durationHours = 1;

        foreach ($validated['absences'] as $student_id => $isPresent) {
            $matchThese = [
                'student_id'   => $student_id,
                'module_id'    => $schedule->module_id,
                'date'         => $validated['date'],
                'session_type' => $validated['session_type'],
            ];

            if ($isPresent == '0') { // Marked as absent
                Absence::firstOrCreate($matchThese, [
                    'duration'             => $durationHours,
                    'is_justified'         => false,
                    'justification_status' => 'none',
                ]);
            } else { // Marked as present — remove any previously recorded absence
                Absence::where($matchThese)->delete();
            }
        }

        ActivityLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'created',
            'model_type' => 'Absence',
            'description' => "Feuille de présence enregistrée pour le module ID {$schedule->module_id}.",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('professor.absences.index')->with('success', 'Feuille de présence enregistrée avec succès.');
    }

    /**
     * Student uploads a medical certificate or justificatif.
     */
    public function uploadJustification(Request $request, Absence $absence)
    {
        // Safety check
        if (!Auth::user()->student || Auth::user()->student->id !== $absence->student_id) {
            return abort(403, 'Unauthorized.');
        }

        $request->validate([
            'justification_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
        ]);

        if ($request->hasFile('justification_file')) {
            $file = $request->file('justification_file');
            $filename = 'justif_' . $absence->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Store file privately
            $path = $file->storeAs('justifications', $filename, 'local');
            
            $absence->update([
                'justification_path' => $path,
                'justification_status' => 'pending',
            ]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'model_type' => 'Absence',
                'description' => "Dépôt d'un justificatif pour l'absence #{$absence->id}.",
                'ip_address' => $request->ip()
            ]);

            return back()->with('success', 'Justificatif téléversé avec succès. En attente de validation administrative.');
        }

        return back()->with('error', 'Échec du téléversement du justificatif.');
    }

    /**
     * Securely download or display justification file.
     */
    public function downloadJustification(Absence $absence)
    {
        $user = Auth::user();
        
        $isOwner = $user->student && $user->student->id === $absence->student_id;
        if ($user->isAdmin() || $isOwner) {
            if (!$absence->justification_path || !\Illuminate\Support\Facades\Storage::disk('local')->exists($absence->justification_path)) {
                abort(404, 'Fichier justificatif introuvable.');
            }
            return \Illuminate\Support\Facades\Storage::disk('local')->response($absence->justification_path);
        }

        abort(403, 'Accès non autorisé au justificatif.');
    }

    /**
     * Admin Index to review all absences and validate justifications.
     */
    public function adminIndex(Request $request)
    {
        $query = Absence::with(['student.user', 'module']);

        // Optional filtering by justification status
        if ($request->filled('status')) {
            $query->where('justification_status', $request->status);
        }

        $absences = $query->orderBy('date', 'desc')->get();

        $studentsAtRisk = \App\Models\Student::with(['user', 'group'])->get()->filter(function($student) {
            return $student->absence_score >= 120; // Example threshold
        });

        return view('admin.absences.index', compact('absences', 'studentsAtRisk'));
    }

    /**
     * Admin approves a justification.
     */
    public function approveJustification(Request $request, Absence $absence)
    {
        $absence->update([
            'is_justified' => true,
            'justification_status' => 'approved',
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'approved',
            'model_type' => 'Absence',
            'description' => "Justificatif approuvé pour l'absence #{$absence->id}.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Justificatif approuvé. L\'absence est désormais marquée comme justifiée.');
    }

    /**
     * Admin rejects a justification.
     */
    public function rejectJustification(Request $request, Absence $absence)
    {
        $absence->update([
            'is_justified' => false,
            'justification_status' => 'rejected',
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'rejected',
            'model_type' => 'Absence',
            'description' => "Justificatif rejeté pour l'absence #{$absence->id}.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Justificatif rejeté.');
    }

    /**
     * Admin manually justifies an absence without a file.
     */
    public function forceJustify(Request $request, Absence $absence)
    {
        $absence->update([
            'is_justified' => true,
            'justification_status' => 'approved',
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'approved',
            'model_type' => 'Absence',
            'description' => "Absence manuellement justifiée #{$absence->id}.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'L\'absence a été justifiée manuellement.');
    }

    /**
     * Admin deletes an absence completely.
     */
    public function destroy(Request $request, Absence $absence)
    {
        $absence->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'model_type' => 'Absence',
            'description' => "Absence supprimée #{$absence->id}.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Absence supprimée avec succès.');
    }
}
