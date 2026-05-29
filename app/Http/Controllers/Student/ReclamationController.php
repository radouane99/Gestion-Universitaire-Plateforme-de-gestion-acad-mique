<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Reclamation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReclamationController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403, 'Profil étudiant introuvable.');
        }

        $reclamations = Reclamation::with(['module', 'exam', 'grade'])
            ->where('student_id', $student->id)
            ->latest()
            ->paginate(10);

        return view('student.reclamations.index', compact('reclamations'));
    }

    public function create()
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403, 'Profil étudiant introuvable.');
        }

        // Fetch student active grades (excluding archived)
        $grades = Grade::with(['module'])
            ->where('student_id', $student->id)
            ->where('is_archived', false)
            ->get();

        return view('student.reclamations.create', compact('grades'));
    }

    public function store(Request $request)
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403, 'Profil étudiant introuvable.');
        }

        $request->validate([
            'grade_id' => 'required|exists:grades,id',
            'reason' => 'required|string|min:10',
        ]);

        $grade = Grade::where('student_id', $student->id)->findOrFail($request->grade_id);

        // Fetch corresponding exam if any
        $exam = \App\Models\Exam::where('module_id', $grade->module_id)
            ->where('group_id', $student->group_id)
            ->where('is_archived', false)
            ->first();

        if (!$exam) {
            return back()->with('error', "Aucun examen actif n'a pu être lié pour ce module.");
        }

        Reclamation::create([
            'student_id' => $student->id,
            'module_id' => $grade->module_id,
            'exam_id' => $exam->id,
            'grade_id' => $grade->id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('student.reclamations.index')
            ->with('success', 'Votre réclamation a été soumise avec succès.');
    }
}
