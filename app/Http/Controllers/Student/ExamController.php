<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttendance;
use App\Models\RetakeEligibility;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    /**
     * Mes examens avec statut de présence.
     */
    public function index()
    {
        $student = Auth::user()->student;
        if (!$student) abort(403);

        // Examens du groupe de l'étudiant
        $exams = Exam::where('group_id', $student->group_id)
            ->with(['module', 'room', 'examSession'])
            ->orderByDesc('date')
            ->get();

        // Présences de l'étudiant
        $attendances = ExamAttendance::where('student_id', $student->id)
            ->with('justification')
            ->get()
            ->keyBy('exam_id');

        // Éligibilités rattrapage
        $retakes = RetakeEligibility::where('student_id', $student->id)
            ->with('exam.module')
            ->get()
            ->keyBy('exam_id');

        return view('student.exams.index', compact('exams', 'attendances', 'retakes'));
    }

    /**
     * Mon droit au rattrapage.
     */
    public function showRetake()
    {
        $student = Auth::user()->student;
        if (!$student) abort(403);

        $retakes = RetakeEligibility::where('student_id', $student->id)
            ->with(['exam.module', 'exam.examSession', 'decidedBy'])
            ->orderByDesc('created_at')
            ->get();

        $settings = Setting::current();

        return view('student.retake.index', compact('retakes', 'settings'));
    }
}
