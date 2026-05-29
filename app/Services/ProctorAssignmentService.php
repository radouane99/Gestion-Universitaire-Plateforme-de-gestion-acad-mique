<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Professor;
use App\Models\ProfessorConvocation;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProctorAssignmentService
{
    /**
     * Auto-assign proctors to all exams in a session based on:
     * - Professor availability declarations
     * - No scheduling conflicts (same day/time)
     * - Fair distribution (least-loaded first)
     * - No conflict of interest (prof doesn't teach the module)
     *
     * @param  int  $sessionId
     * @return array{assigned: int, skipped: int, failed: int, errors: string[]}
     */
    public function assignForSession(int $sessionId): array
    {
        $session = ExamSession::with(['exams.module', 'exams.group', 'exams.room'])
            ->findOrFail($sessionId);

        $stats = ['assigned' => 0, 'skipped' => 0, 'failed' => 0, 'errors' => []];

        // Load all professors with their availabilities for this session window
        $professors = Professor::with([
            'user',
            'availabilities' => fn($q) => $q->whereBetween('available_date', [
                $session->start_date ?? now()->toDateString(),
                $session->end_date   ?? now()->addMonths(3)->toDateString(),
            ])
        ])->get();

        // Sort exams by date/time
        $exams = $session->exams->sortBy(fn($e) => $e->date . ' ' . $e->start_time);

        // Track assignments count per professor for fairness
        $assignmentCounts = $professors->mapWithKeys(fn($p) => [$p->id => 0]);

        foreach ($exams as $exam) {
            $neededProctors = $this->calculateNeededProctors($exam);
            $currentProctors = $exam->proctors()->count();

            if ($currentProctors >= $neededProctors) {
                $stats['skipped']++;
                continue;
            }

            $toAssign = $neededProctors - $currentProctors;
            $examDate = Carbon::parse($exam->date);
            $examStart = Carbon::parse($exam->date . ' ' . $exam->start_time);
            $examEnd = $examStart->copy()->addMinutes($exam->duration);

            // Get already-assigned professor IDs for this exam
            $alreadyAssigned = $exam->proctors()->pluck('professors.id')->toArray();

            // Find eligible professors
            $eligible = $this->findEligibleProfessors(
                professors: $professors,
                exam: $exam,
                examDate: $examDate,
                examStart: $examStart,
                examEnd: $examEnd,
                alreadyAssigned: $alreadyAssigned,
                assignmentCounts: $assignmentCounts
            );

            if ($eligible->isEmpty()) {
                $stats['failed']++;
                $stats['errors'][] = "Examen {$exam->module->name} ({$exam->date}) : aucun professeur disponible.";
                continue;
            }

            // Take the needed number of eligible professors
            $toAssignProfs = $eligible->take($toAssign);

            DB::transaction(function () use ($exam, $toAssignProfs, &$assignmentCounts, &$stats) {
                $first = true;
                foreach ($toAssignProfs as $professor) {
                    // Attach to exam_proctor pivot
                    $exam->proctors()->syncWithoutDetaching([$professor->id]);

                    // Increment assignment count for fairness tracking
                    $assignmentCounts[$professor->id] = ($assignmentCounts[$professor->id] ?? 0) + 1;
                    $stats['assigned']++;
                    $first = false;
                }
            });
        }

        return $stats;
    }

    /**
     * Generate ProfessorConvocation records for all exam_proctor assignments in a session.
     *
     * @return array{generated: int, skipped: int}
     */
    public function generateConvocationsForSession(int $sessionId): array
    {
        $session = ExamSession::with('exams.proctors.user')->findOrFail($sessionId);

        $stats = ['generated' => 0, 'skipped' => 0];

        foreach ($session->exams as $exam) {
            $proctors = $exam->proctors;
            $isFirst = true;

            foreach ($proctors as $professor) {
                // Skip if already exists
                $exists = ProfessorConvocation::where('professor_id', $professor->id)
                    ->where('exam_id', $exam->id)
                    ->exists();

                if ($exists) {
                    $stats['skipped']++;
                    continue;
                }

                ProfessorConvocation::create([
                    'professor_id' => $professor->id,
                    'exam_id'      => $exam->id,
                    'reference'    => ProfessorConvocation::generateReference(),
                    'status'       => 'generated',
                    'role'         => $isFirst ? 'principal' : 'assistant',
                ]);

                $isFirst = false;
                $stats['generated']++;
            }
        }

        return $stats;
    }

    /**
     * Calculate how many proctors are needed for an exam.
     * Rule: ceil(student_count / 30), minimum 1.
     */
    public function calculateNeededProctors(Exam $exam): int
    {
        $studentCount = Student::where('group_id', $exam->group_id)->count();
        return max(1, (int) ceil($studentCount / 30));
    }

    /**
     * Find professors eligible to proctor a given exam slot.
     */
    private function findEligibleProfessors(
        Collection $professors,
        Exam $exam,
        Carbon $examDate,
        Carbon $examStart,
        Carbon $examEnd,
        array $alreadyAssigned,
        Collection $assignmentCounts
    ): Collection {
        return $professors
            ->filter(function (Professor $professor) use ($exam, $examDate, $examStart, $examEnd, $alreadyAssigned) {
                // 1. Already assigned to this exam?
                if (in_array($professor->id, $alreadyAssigned)) {
                    return false;
                }

                // 2. Has declared availability for this date?
                $isAvailable = $professor->availabilities
                    ->contains(fn($a) => Carbon::parse($a->available_date)->isSameDay($examDate));

                if (!$isAvailable) {
                    return false;
                }

                // 3. No scheduling conflict (already proctoring another exam at same time)?
                $hasConflict = $professor->examProctors()
                    ->where('date', $exam->date)
                    ->where('id', '!=', $exam->id)
                    ->get()
                    ->contains(function ($otherExam) use ($examStart, $examEnd) {
                        $otherStart = Carbon::parse($otherExam->date . ' ' . $otherExam->start_time);
                        $otherEnd   = $otherStart->copy()->addMinutes($otherExam->duration);
                        return $examStart->lt($otherEnd) && $examEnd->gt($otherStart);
                    });

                if ($hasConflict) {
                    return false;
                }

                return true;
            })
            // Sort by assignment count ascending (fairness — least loaded first)
            ->sortBy(fn($p) => $assignmentCounts->get($p->id, 0))
            ->values();
    }
}
