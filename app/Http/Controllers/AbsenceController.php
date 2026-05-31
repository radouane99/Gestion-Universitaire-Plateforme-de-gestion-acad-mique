<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Absence;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\ActivityLog;
use App\Notifications\AbsenceRecorded;
use App\Services\AbsenceAlertService;

class AbsenceController extends Controller
{
    public function __construct(private AbsenceAlertService $alertService) {}

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
            'schedule_id'  => 'required|exists:schedules,id',
            'absences'     => 'required|array',
            'date'         => 'required|date',
            'session_type' => 'required|string',
        ]);

        $schedule  = Schedule::with('group.students')->findOrFail($validated['schedule_id']);
        $professor = Auth::user()->professor;
        if (!$professor || $schedule->professor_id !== $professor->id) {
            abort(403, "Vous n'êtes pas autorisé à enregistrer des absences pour ce cours.");
        }

        // Sécurité: uniquement les étudiants du groupe
        $authorizedStudentIds = $schedule->group->students->pluck('id')->toArray();
        foreach (array_keys($validated['absences']) as $student_id) {
            if (!in_array($student_id, $authorizedStudentIds)) {
                abort(403, "L'étudiant #{$student_id} n'appartient pas à ce groupe.");
            }
        }

        // ✅ Calcul en heures DÉCIMALES (1h30 = 1.5h)
        $startTime    = \Carbon\Carbon::parse($schedule->start_time);
        $endTime      = \Carbon\Carbon::parse($schedule->end_time);
        $durationMins = max($startTime->diffInMinutes($endTime), 30); // Min 30 minutes
        $durationHours = round($durationMins / 60, 2); // Ex: 90min → 1.5h

        $affectedStudents = [];

        foreach ($validated['absences'] as $student_id => $isPresent) {
            $matchThese = [
                'student_id'   => $student_id,
                'module_id'    => $schedule->module_id,
                'date'         => $validated['date'],
                'session_type' => $validated['session_type'],
            ];

            if ($isPresent == '0') {
                $absence = Absence::firstOrCreate($matchThese, [
                    'schedule_id'          => $schedule->id,
                    'duration'             => $durationHours,
                    'is_justified'         => false,
                    'justification_status' => 'none',
                ]);

                if ($absence->wasRecentlyCreated) {
                    $affectedStudents[] = $student_id;

                    // Notifier l'étudiant de son absence
                    $student = Student::find($student_id);
                    if ($student?->user) {
                        $student->user->notify(new AbsenceRecorded(
                            $schedule->module?->name ?? 'Module',
                            $validated['date']
                        ));

                        // 🚨 Vérifier seuils et déclencher alertes
                        $this->alertService->checkAndTriggerAlerts($student);
                    }
                }
            } else {
                Absence::where($matchThese)->delete();
            }
        }

        // ✅ Confirmer et enregistrer la séance et ses heures travaillées automatiquement !
        \App\Models\ConfirmedSession::updateOrCreate([
            'professor_id' => $professor->id,
            'schedule_id'  => $schedule->id,
            'date'         => $validated['date'],
        ], [
            'group_id'     => $schedule->group_id,
            'module_id'    => $schedule->module_id,
            'start_time'   => $schedule->start_time,
            'end_time'     => $schedule->end_time,
            'duration'     => $durationHours,
        ]);

        ActivityLog::log(
            'created',
            'Absence',
            "Feuille de présence enregistrée pour le module ID {$schedule->module_id}. "
            . count($affectedStudents) . " absence(s) enregistrée(s)."
        );

        return redirect()->route('professor.absences.index')
            ->with('success', 'Feuille de présence enregistrée avec succès.');
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

        if ($request->filled('status')) {
            $query->where('justification_status', $request->status);
        }
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->filled('module_id')) {
            $query->where('module_id', $request->module_id);
        }
        if ($request->filled('filiere_id')) {
            $query->whereHas('student.group', function ($q) use ($request) {
                $q->where('filiere_id', $request->filiere_id);
            });
        }
        if ($request->filled('group_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('group_id', $request->group_id);
            });
        }

        $absences = $query->orderBy('date', 'desc')->paginate(30);

        // Fetch options for the filters
        $filieres = \App\Models\Filiere::all();
        $groups = \App\Models\Group::all();

        // Seuil configurable depuis settings
        $threshold      = \App\Models\Setting::get('absence_discipline_threshold', 120);
        $studentsAtRisk = \App\Models\Student::with(['user', 'group'])->get()->filter(
            fn($s) => $s->absence_score >= $threshold
        );

        return view('admin.absences.index', compact('absences', 'studentsAtRisk', 'threshold', 'filieres', 'groups'));
    }

    /**
     * Admin approves a justification.
     */
    public function approveJustification(Request $request, Absence $absence)
    {
        $absence->update([
            'is_justified'         => true,
            'justification_status' => 'approved',
        ]);

        ActivityLog::log('approved', 'Absence', "Justificatif approuvé pour l'absence #{$absence->id}.");

        // Recalculer le statut discipline après justification
        $student = $absence->student;
        if ($student) {
            $this->alertService->recalculateAfterJustification($student);

            // Notifier l'étudiant
            $student->user?->notify(new \App\Notifications\AcademicNotification(
                "Votre justificatif pour l'absence du " . $absence->date?->format('d/m/Y') . ' a été accepté.',
                'success',
                route('student.absences')
            ));
        }

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
