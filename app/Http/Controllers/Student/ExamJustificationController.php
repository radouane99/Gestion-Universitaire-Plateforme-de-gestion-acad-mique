<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamAttendance;
use App\Models\ExamJustification;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamJustificationController extends Controller
{
    /**
     * Formulaire de dépôt de justification pour une absence à un examen.
     */
    public function create(ExamAttendance $attendance)
    {
        $student = Auth::user()->student;

        // Vérifier que c'est bien l'absence de cet étudiant
        if (!$student || $attendance->student_id !== $student->id) {
            abort(403, "Accès non autorisé.");
        }

        // Vérifier que l'étudiant est bien absent
        if (!$attendance->isAbsent() && $attendance->status !== 'fraud') {
            return back()->with('error', "Vous étiez présent à cet examen. Aucune justification nécessaire.");
        }

        // Vérifier qu'il n'y a pas déjà une justification
        $existing = ExamJustification::where('exam_attendance_id', $attendance->id)->first();

        $attendance->load(['exam.module', 'exam.room']);

        return view('student.exam-justifications.create', compact('attendance', 'existing'));
    }

    /**
     * Soumettre la justification.
     */
    public function store(Request $request, ExamAttendance $attendance)
    {
        $student = Auth::user()->student;

        if (!$student || $attendance->student_id !== $student->id) {
            abort(403, "Accès non autorisé.");
        }

        $request->validate([
            'justification_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'student_comment'    => 'nullable|string|max:1000',
        ]);

        // Vérifier qu'il n'y a pas déjà une justification approuvée
        $existing = ExamJustification::where('exam_attendance_id', $attendance->id)->first();
        if ($existing && $existing->status === 'approved') {
            return back()->with('error', 'Votre justification a déjà été approuvée.');
        }

        // Téléverser le fichier
        $file     = $request->file('justification_file');
        $filename = 'exam_justif_' . $attendance->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path     = $file->storeAs('exam_justifications', $filename, 'local');

        if ($existing) {
            $existing->update([
                'justification_path' => $path,
                'student_comment'    => $request->student_comment,
                'status'             => 'pending',
            ]);
        } else {
            ExamJustification::create([
                'exam_attendance_id' => $attendance->id,
                'student_id'         => $student->id,
                'justification_path' => $path,
                'student_comment'    => $request->student_comment,
                'status'             => 'pending',
            ]);
        }

        ActivityLog::log(
            'created',
            'ExamJustification',
            "Étudiant #{$student->id} a déposé une justification pour l'absence examen #{$attendance->exam_id}."
        );

        return redirect()->route('student.exams.index')
            ->with('success', 'Justification déposée avec succès. En attente de validation administrative.');
    }
}
