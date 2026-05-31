<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Internship;
use App\Models\InternshipReport;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Storage;

class InternshipController extends Controller
{
    public function index()
    {
        $professor = Auth::user()->professor;
        if (!$professor) {
            abort(403);
        }

        $internships = Internship::where('academic_tutor_id', $professor->id)
            ->with(['student.user', 'student.group'])
            ->get();

        return view('professor.internships.index', compact('internships'));
    }

    public function show(Internship $internship)
    {
        $professor = Auth::user()->professor;
        if (!$professor || $internship->academic_tutor_id !== $professor->id) {
            abort(403);
        }

        $internship->load(['student.user', 'student.group', 'reports']);

        return view('professor.internships.show', compact('internship'));
    }

    public function reviewReport(Request $request, InternshipReport $report)
    {
        $professor = Auth::user()->professor;
        $internship = $report->internship;

        if (!$professor || $internship->academic_tutor_id !== $professor->id) {
            abort(403);
        }

        $validated = $request->validate([
            'tutor_feedback' => 'required|string|max:1000',
        ]);

        $report->update([
            'tutor_feedback' => $validated['tutor_feedback'],
            'status' => 'reviewed',
        ]);

        // Notify student of report feedback
        $internship->student->user?->notify(new \App\Notifications\AcademicNotification(
            "📝 Votre tuteur a évalué votre rapport mensuel N°{$report->report_number}",
            'success',
            route('student.internships.index')
        ));

        ActivityLog::log('updated', 'InternshipReport', "Commentaire de tuteur ajouté pour le rapport N°{$report->report_number} de l'étudiant #{$internship->student_id}.");

        return back()->with('success', 'Votre avis sur le rapport mensuel a été enregistré.');
    }

    public function grade(Request $request, Internship $internship)
    {
        $professor = Auth::user()->professor;
        if (!$professor || $internship->academic_tutor_id !== $professor->id) {
            abort(403);
        }

        $validated = $request->validate([
            'grade' => 'required|numeric|between:0,20',
            'tutor_feedback' => 'required|string|max:1500',
        ]);

        $internship->update([
            'grade' => $validated['grade'],
            'tutor_feedback' => $validated['tutor_feedback'],
            'status' => 'completed',
        ]);

        // Notify student of final stage grading
        $internship->student->user?->notify(new \App\Notifications\AcademicNotification(
            "🎓 Note finale de stage saisie : {$validated['grade']}/20. Félicitations !",
            'success',
            route('student.internships.index')
        ));

        ActivityLog::log('updated', 'Internship', "Saisie de note finale de stage pour l'étudiant #{$internship->student_id} : {$validated['grade']}/20.");

        return redirect()->route('professor.internships.index')
            ->with('success', "Le stage a été évalué et clôturé avec succès !");
    }

    public function downloadReportFile(InternshipReport $report)
    {
        $professor = Auth::user()->professor;
        $internship = $report->internship;

        if (!$professor || $internship->academic_tutor_id !== $professor->id) {
            abort(403);
        }

        if (!$report->file_path || !Storage::exists($report->file_path)) {
            abort(404, 'Fichier introuvable.');
        }

        return Storage::download($report->file_path);
    }
}
