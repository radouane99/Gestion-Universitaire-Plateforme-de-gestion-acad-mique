<?php

namespace App\Http\Controllers\Student;

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
        $student = Auth::user()->student;
        if (!$student) {
            abort(403);
        }

        // Fetch student's internship (only one active per student at a time)
        $internship = Internship::where('student_id', $student->id)
            ->with(['academicTutor.user', 'reports'])
            ->first();

        return view('student.internships.index', compact('internship'));
    }

    public function store(Request $request)
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403);
        }

        // Enforce single active internship
        $exists = Internship::where('student_id', $student->id)
            ->whereIn('status', ['pending', 'active'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Vous avez déjà une demande de stage en cours ou un stage actif.');
        }

        $validated = $request->validate([
            'company_name'    => 'required|string|max:255',
            'company_address' => 'required|string|max:255',
            'tutor_name'      => 'required|string|max:255',
            'tutor_email'     => 'required|email|max:255',
            'tutor_phone'     => 'required|string|max:50',
            'subject'         => 'required|string|max:500',
            'start_date'      => 'required|date',
            'end_date'        => 'required|date|after:start_date',
        ]);

        $internship = Internship::create([
            'student_id'      => $student->id,
            'company_name'    => $validated['company_name'],
            'company_address' => $validated['company_address'],
            'tutor_name'      => $validated['tutor_name'],
            'tutor_email'     => $validated['tutor_email'],
            'tutor_phone'     => $validated['tutor_phone'],
            'subject'         => $validated['subject'],
            'start_date'      => $validated['start_date'],
            'end_date'        => $validated['end_date'],
            'status'          => 'pending',
        ]);

        ActivityLog::log('created', 'Internship', "Fiche de stage déposée par l'étudiant #{$student->id} pour {$validated['company_name']}.");

        return redirect()->route('student.internships.index')
            ->with('success', 'Votre fiche de stage a été soumise pour validation administrative.');
    }

    public function storeReport(Request $request, Internship $internship)
    {
        $student = Auth::user()->student;
        if (!$student || $internship->student_id !== $student->id) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'report_file' => 'required|file|mimes:pdf,docx,zip|max:10240', // Max 10MB
        ]);

        $nextReportNumber = $internship->reports()->count() + 1;

        $filePath = $request->file('report_file')->store('internship_reports');

        InternshipReport::create([
            'internship_id' => $internship->id,
            'report_number' => $nextReportNumber,
            'title' => $request->title,
            'content' => $request->content,
            'file_path' => $filePath,
            'submitted_at' => now(),
            'status' => 'pending',
        ]);

        ActivityLog::log('created', 'InternshipReport', "Rapport de stage mensuel N°{$nextReportNumber} déposé par l'étudiant #{$student->id}.");

        return back()->with('success', "Rapport mensuel N°{$nextReportNumber} soumis avec succès !");
    }

    public function downloadReportFile(InternshipReport $report)
    {
        $student = Auth::user()->student;
        $internship = $report->internship;

        if (!$student || $internship->student_id !== $student->id) {
            abort(403);
        }

        if (!$report->file_path || !Storage::exists($report->file_path)) {
            abort(404, 'Fichier introuvable.');
        }

        return Storage::download($report->file_path);
    }
}
