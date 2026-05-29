<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttendance;
use App\Models\Student;
use App\Models\ActivityLog;
use App\Services\RetakeEligibilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamAttendanceController extends Controller
{
    public function __construct(private RetakeEligibilityService $retakeService) {}

    /**
     * Feuille de présence d'un examen — admin.
     */
    public function index(Exam $exam)
    {
        $exam->load(['module', 'group.students.user', 'room', 'proctors.user']);

        // Récupérer les présences déjà enregistrées
        $attendances = ExamAttendance::where('exam_id', $exam->id)
            ->with(['student.user', 'markedBy'])
            ->get()
            ->keyBy('student_id');

        $students = $exam->group->students()->with('user')->get();

        return view('admin.exam-attendances.index', compact('exam', 'attendances', 'students'));
    }

    /**
     * Enregistrer les présences — admin (bulk).
     */
    public function store(Request $request, Exam $exam)
    {
        $request->validate([
            'attendances'           => 'required|array',
            'attendances.*.status'  => 'required|in:present,absent,late,fraud',
            'attendances.*.notes'   => 'nullable|string|max:500',
        ]);

        $students = $exam->group->students->pluck('id')->toArray();

        foreach ($request->attendances as $studentId => $data) {
            if (!in_array($studentId, $students)) continue;

            $attendance = ExamAttendance::updateOrCreate(
                ['exam_id' => $exam->id, 'student_id' => $studentId],
                [
                    'status'    => $data['status'],
                    'notes'     => $data['notes'] ?? null,
                    'marked_by' => Auth::id(),
                    'marked_at' => now(),
                    'ip_address' => $request->ip(),
                ]
            );

            // Calculer éligibilité rattrapage automatiquement
            $this->retakeService->evaluateAfterExamAttendance($attendance);
        }

        ActivityLog::log(
            'created',
            'ExamAttendance',
            "Feuille de présence enregistrée pour l'examen #{$exam->id} — {$exam->module?->name}."
        );

        return back()->with('success', 'Feuille de présence enregistrée avec succès.');
    }

    /**
     * Marquer un seul étudiant rapidement (AJAX-friendly).
     */
    public function markOne(Request $request, Exam $exam, Student $student)
    {
        $request->validate(['status' => 'required|in:present,absent,late,fraud']);

        $attendance = ExamAttendance::updateOrCreate(
            ['exam_id' => $exam->id, 'student_id' => $student->id],
            [
                'status'     => $request->status,
                'notes'      => $request->notes,
                'marked_by'  => Auth::id(),
                'marked_at'  => now(),
                'ip_address' => $request->ip(),
            ]
        );

        $this->retakeService->evaluateAfterExamAttendance($attendance);

        return response()->json([
            'ok'     => true,
            'status' => $attendance->status_label,
        ]);
    }
}
