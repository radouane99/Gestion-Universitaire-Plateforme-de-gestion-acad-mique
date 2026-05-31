<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $student = $user->student;

        // If the student's registration is still pending administrative review
        if ($student && $student->registration_status == 'pending') {
            return view('student.pending_validation', compact('student'));
        }

        $grades   = \App\Models\Grade::where('student_id', $student->id)->with('module')->get();
        $absences = \App\Models\Absence::where('student_id', $student->id)->with('module')->get();
        $requests = \App\Models\Request::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        $schedule = \App\Models\Schedule::where('group_id', $student->group_id)
            ->with(['module', 'professor.user', 'room'])
            ->get();

        // Next upcoming class
        $currentDay  = (int) date('N');
        $currentTime = date('H:i:s');
        $nextClass   = $schedule
            ->filter(fn($s) => $s->day_of_week == $currentDay && $s->start_time > $currentTime)
            ->sortBy('start_time')
            ->first();
        if (!$nextClass) {
            // Look for next day
            $nextClass = $schedule->sortBy(['day_of_week', 'start_time'])->first();
        }

        $gradesCount  = $grades->whereNotNull('final_grade')->count();
        $gpa          = $gradesCount > 0 ? $grades->whereNotNull('final_grade')->avg('final_grade') : 0;
        $gpaPercent   = round(($gpa / 20) * 100);
        $unjustified  = $absences->where('is_justified', false)->count();

        // 📈 Moyenne par Semestre (GPA History)
        $gradesForGpa = \App\Models\Grade::where('student_id', $student->id)->with(['module.semester'])->get();
        $gpaHistory = [];
        $gradesBySemester = $gradesForGpa->groupBy(function($g) {
            return $g->module && $g->module->semester ? $g->module->semester->name : 'Autres';
        })->sortKeys();

        foreach ($gradesBySemester as $semesterName => $semesterGrades) {
            $count = $semesterGrades->whereNotNull('final_grade')->count();
            if ($count > 0) {
                $gpaHistory[] = [
                    'semester' => $semesterName,
                    'gpa' => round($semesterGrades->whereNotNull('final_grade')->avg('final_grade'), 2)
                ];
            }
        }

        $pendingRetakes = \App\Models\RetakeEligibility::where('student_id', $student->id)
            ->whereIn('admin_decision', ['pending', 'approved'])
            ->count();

        $pendingReclamations = \App\Models\Reclamation::where('student_id', $student->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        return view('student.dashboard', compact(
            'grades', 'absences', 'requests', 'schedule',
            'gpa', 'gpaPercent', 'nextClass', 'unjustified',
            'pendingRetakes', 'pendingReclamations', 'gpaHistory'
        ));
    }

    public function grades()
    {
        $student = Auth::user()->student;
        $grades = \App\Models\Grade::where('student_id', $student->id)->with(['module.semester'])->get();
        
        $gradesBySemester = $grades->groupBy(function($g) {
            return $g->module && $g->module->semester ? $g->module->semester->name : 'Autres Modules';
        })->sortKeys();
        
        // Calcul de la moyenne annuelle
        $totalGPA = 0;
        $validSemestersCount = 0;
        
        foreach($gradesBySemester as $sem => $semGrades) {
            $count = $semGrades->whereNotNull('final_grade')->count();
            if ($count > 0) {
                $totalGPA += $semGrades->whereNotNull('final_grade')->avg('final_grade');
                $validSemestersCount++;
            }
        }
        
        $yearlyGPA = $validSemestersCount > 0 ? $totalGPA / $validSemestersCount : 0;

        return view('student.grades', compact('gradesBySemester', 'yearlyGPA'));
    }

    public function absences()
    {
        $student = Auth::user()->student;
        $absences = \App\Models\Absence::where('student_id', $student->id)->with('module')->get();
        return view('student.absences', compact('absences'));
    }

    public function createRequest()
    {
        $requests = \App\Models\Request::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('student.requests.create', compact('requests'));
    }

    public function showReinscriptionForm()
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(404);
        }

        if (!\App\Models\Setting::isReinscriptionOpen()) {
            return redirect()->route('student.dashboard')->with('error', 'La campagne de réinscription est actuellement fermée.');
        }

        if (!$student->isEligibleForReinscription()) {
            return redirect()->route('student.dashboard')->with('error', 'Vous n\'êtes pas éligible pour la réinscription ou vous êtes déjà réinscrit pour cette année.');
        }

        $failedModules = $student->getFailedModules();
        $gpa = $student->getYearlyGpa();

        return view('student.reinscription', compact('student', 'failedModules', 'gpa'));
    }

    public function processReinscription(Request $request)
    {
        $student = Auth::user()->student;
        
        if (!\App\Models\Setting::isReinscriptionOpen()) {
            return redirect()->route('student.dashboard')->with('error', 'La campagne de réinscription est actuellement fermée.');
        }

        if (!$student || !$student->isEligibleForReinscription()) {
            return redirect()->route('student.dashboard')->with('error', 'Action non autorisée.');
        }

        $request->validate([
            'confirm_details' => 'required|accepted',
        ]);

        $gpa = $student->getYearlyGpa();
        $failedModules = $student->getFailedModules();

        return \Illuminate\Support\Facades\DB::transaction(function () use ($student, $gpa, $failedModules, $request) {
            $currentGroup = $student->group;
            $nextGroup = null;

            if ($gpa >= 10) {
                // Passed! Try to promote to next group level
                if ($currentGroup) {
                    $currentLevel = $currentGroup->level; // e.g. "Licence 1"
                    $currentName = $currentGroup->name;   // e.g. "GI-1"

                    // Look for level 2 or similar
                    $nextLevel = str_replace(['1', 'one', 'un'], ['2', 'two', 'deux'], $currentLevel);
                    $nextName = str_replace('1', '2', $currentName);

                    $nextGroup = \App\Models\Group::where('filiere_id', $student->filiere_id)
                        ->where(function($q) use ($nextLevel, $nextName) {
                            $q->where('level', $nextLevel)->orWhere('name', $nextName);
                        })->first();
                }

                // If next group doesn't exist, keep in current group but mark year promoted
                if (!$nextGroup) {
                    $nextGroup = $currentGroup;
                }
            } else {
                // Failed (GPA < 10): Remains in the same repeating level group
                $nextGroup = $currentGroup;
            }

            // Carry over failed modules as debts (Crédits Modules)
            $oldYearId = $student->academic_year_id;
            foreach ($failedModules as $module) {
                // Insert into student_credit_modules if not already exists
                \DB::table('student_credit_modules')->updateOrInsert(
                    ['student_id' => $student->id, 'module_id' => $module->id],
                    [
                        'academic_year_id' => $oldYearId,
                        'status' => 'pending',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            // Get current active academic year to promote them into
            $newYearId = \DB::table('academic_years')->where('is_current', true)->value('id')
                ?? \DB::table('academic_years')->first()?->id;

            // Promote Student
            $student->update([
                'group_id' => $nextGroup ? $nextGroup->id : null,
                'academic_year_id' => $newYearId,
                'registration_type' => 'reinscription', // Marked as re-registered
                'registration_status' => 'approved',
            ]);

            \App\Models\ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'model_type' => 'Student',
                'description' => "Réinscription effectuée avec succès pour '{$student->user->name}'. GPA Annuel: {$gpa}/20. " . $failedModules->count() . " crédits reportés.",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('student.dashboard')->with('success', 'Votre réinscription a été traitée avec succès ! Vos modules de dette ont été reportés en crédits.');
        });
    }

    /**
     * Téléchargement de l'Attestation de Réussite officielle en PDF avec QR Code.
     */
    public function downloadAttestation(Request $request)
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(404);
        }

        $level = $student->group->level ?? null;
        $isPvValidated = \App\Models\PVGlobalApproval::where('filiere_id', $student->filiere_id)
            ->where('academic_year_id', $student->academic_year_id)
            ->where('level', $level)
            ->where('is_validated', true)
            ->exists();

        if (!$isPvValidated) {
            return redirect()->route('student.dashboard')->with('error', "L'attestation de réussite n'est pas encore disponible. Le PV académique annuel doit d'abord être validé par la direction.");
        }

        // Éligibilité : Moyenne annuelle >= 10 et aucun module en échec (0 dette active)
        if ($student->getYearlyGpa() < 10 || !$student->getFailedModules()->isEmpty()) {
            return redirect()->route('student.dashboard')->with('error', "Vous n'êtes pas éligible au téléchargement de l'attestation de réussite car vous avez des dettes ou n'avez pas validé l'année.");
        }

        $gpa = $student->getYearlyGpa();
        $mention = match (true) {
            $gpa >= 16.0 => 'Très Bien',
            $gpa >= 14.0 => 'Bien',
            $gpa >= 12.0 => 'Assez Bien',
            $gpa >= 10.0 => 'Passable',
            default      => 'Passable',
        };

        $verifyUrl = route('verify.document', $student->document_token);
        $isPdf = true;

        // Génération du PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.attestation', compact('student', 'gpa', 'mention', 'verifyUrl', 'isPdf'));
        
        $nameSlug = \Illuminate\Support\Str::slug($student->user->name, '_');
        $fileName = "attestation_reussite_{$nameSlug}.pdf";
        if ($request->query('preview') == 1) {
            return $pdf->stream($fileName);
        }
        return $pdf->download($fileName);
    }

    /**
     * Téléchargement du Reçu d'Inscription.
     */
    public function downloadReceipt(Request $request)
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(404);
        }

        if ($student->registration_status !== 'approved') {
            return redirect()->route('student.dashboard')->with('error', "Votre reçu d'inscription n'est pas encore disponible. L'administration doit d'abord approuver votre dossier.");
        }

        $verifyUrl = route('verify.document', $student->document_token);
        $isPdf = true;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.registration_receipt', compact('student', 'verifyUrl', 'isPdf'));
        
        $nameSlug = \Illuminate\Support\Str::slug($student->user->name, '_');
        $fileName = "recu_inscription_{$nameSlug}.pdf";
        if ($request->query('preview') == 1) {
            return $pdf->stream($fileName);
        }
        return $pdf->download($fileName);
    }

    /**
     * Téléchargement du Diplôme de Réussite officielle en PDF.
     */
    public function downloadDiplome(Request $request)
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(404);
        }

        $level = $student->group->level ?? null;
        if ($level != 3) {
            return redirect()->route('student.dashboard')->with('error', "Action non autorisée. Les diplômes ne sont délivrés qu'aux lauréats de 3ème année.");
        }

        $isPvValidated = \App\Models\PVGlobalApproval::where('filiere_id', $student->filiere_id)
            ->where('academic_year_id', $student->academic_year_id)
            ->where('level', $level)
            ->where('is_validated', true)
            ->exists();

        if (!$isPvValidated) {
            return redirect()->route('student.dashboard')->with('error', "Le diplôme n'est pas encore disponible. Le PV académique annuel doit d'abord être validé par la direction.");
        }

        // Éligibilité : Moyenne annuelle >= 10 et aucun module en échec (0 dette active)
        if ($student->getYearlyGpa() < 10 || !$student->getFailedModules()->isEmpty()) {
            return redirect()->route('student.dashboard')->with('error', "Vous n'êtes pas éligible à la délivrance du diplôme car vous avez des dettes ou n'avez pas validé l'année.");
        }

        $gpa = $student->getYearlyGpa();
        $mention = match (true) {
            $gpa >= 16.0 => 'Très Bien',
            $gpa >= 14.0 => 'Bien',
            $gpa >= 12.0 => 'Assez Bien',
            $gpa >= 10.0 => 'Passable',
            default      => 'Passable',
        };

        $verifyUrl = route('verify.document', $student->document_token);
        $isPdf = true;

        // Génération du PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.diplome', compact('student', 'gpa', 'mention', 'verifyUrl', 'isPdf'));
        $pdf->setPaper('A4', 'landscape');
        
        $nameSlug = \Illuminate\Support\Str::slug($student->user->name, '_');
        $fileName = "diplome_{$nameSlug}.pdf";
        if ($request->query('preview') == 1) {
            return $pdf->stream($fileName);
        }
        return $pdf->download($fileName);
    }
}
