<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttendance;
use App\Models\ActivityLog;
use App\Services\RetakeEligibilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Permet au professeur surveillant de marquer la présence/absence des étudiants
 * lors d'un examen dont il est le surveillant (proctor).
 */
class ExamAttendanceController extends Controller
{
    public function __construct(private RetakeEligibilityService $retakeService) {}

    /**
     * Feuille de présence — professeur surveillant.
     * Autorisé uniquement si le professeur est affecté comme surveillant pour cet examen.
     */
    public function index(Exam $exam)
    {
        $professor = Auth::user()->professor;

        // Vérifier que ce professeur est bien surveillant de cet examen
        if (!$exam->proctors->contains($professor->id)) {
            abort(403, "Vous n'êtes pas affecté à la surveillance de cet examen.");
        }

        $exam->load(['module', 'group.students.user', 'room']);

        $attendances = ExamAttendance::where('exam_id', $exam->id)
            ->with(['student.user', 'markedBy'])
            ->get()
            ->keyBy('student_id');

        $students = $exam->group->students()->with('user')->get();

        return view('professor.exam-attendance.index', compact('exam', 'attendances', 'students'));
    }

    /**
     * Enregistrer les présences — professeur surveillant (bulk).
     */
    public function store(Request $request, Exam $exam)
    {
        $professor = Auth::user()->professor;

        if (!$exam->proctors->contains($professor->id)) {
            abort(403, "Vous n'êtes pas autorisé à modifier la présence pour cet examen.");
        }

        $request->validate([
            'attendances'          => 'required|array',
            'attendances.*.status' => 'required|in:present,absent,late,fraud',
            'attendances.*.notes'  => 'nullable|string|max:500',
        ]);

        $students = $exam->group->students->pluck('id')->toArray();

        foreach ($request->attendances as $studentId => $data) {
            if (!in_array($studentId, $students)) continue;

            $attendance = ExamAttendance::updateOrCreate(
                ['exam_id' => $exam->id, 'student_id' => $studentId],
                [
                    'status'     => $data['status'],
                    'notes'      => $data['notes'] ?? null,
                    'marked_by'  => Auth::id(),
                    'marked_at'  => now(),
                    'ip_address' => $request->ip(),
                ]
            );

            $this->retakeService->evaluateAfterExamAttendance($attendance);
        }

        ActivityLog::log(
            'created',
            'ExamAttendance',
            "Professeur #{$professor->id} — feuille de présence pour examen #{$exam->id}."
        );

        return back()->with('success', 'Feuille de présence enregistrée avec succès.');
    }
}
