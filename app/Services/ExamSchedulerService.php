<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Group;
use App\Models\Room;
use App\Models\Professor;
use App\Models\ProfessorAvailability;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExamSchedulerService
{
    /**
     * Generate an exam schedule for a specific session.
     * Sources group+module combos from the Schedules table (timetable),
     * since the Assignments table may be empty.
     *
     * @param int      $examSessionId
     * @param int|null $filiereId     Optional – restrict to one filière
     * @return array   Result summary
     */
    public function generateSchedule(int $examSessionId, ?int $filiereId = null, bool $overwrite = false): array
    {
        $session = ExamSession::findOrFail($examSessionId);

        if ($overwrite) {
            $existingExamsQuery = Exam::where('exam_session_id', $examSessionId);
            if ($filiereId) {
                $existingExamsQuery->whereHas('group', function ($q) use ($filiereId) {
                    $q->where('filiere_id', $filiereId);
                });
            }
            $existingExamIds = $existingExamsQuery->pluck('id');
            
            // Delete convocations and proctors first
            \App\Models\Convocation::whereIn('exam_id', $existingExamIds)->delete();
            DB::table('exam_proctor')->whereIn('exam_id', $existingExamIds)->delete();
            
            // Delete the exams themselves
            $existingExamsQuery->delete();
        }

        // ── 1. Collect unique (group, module) pairs from the Schedules table ──
        //    We use Schedule because Assignments is typically empty.
        $scheduleQuery = \App\Models\Schedule::query()
            ->select('group_id', 'module_id', 'professor_id')
            ->with(['group.filiere', 'module', 'professor']);

        // Filter by filière if requested
        if ($filiereId) {
            $scheduleQuery->whereHas('group', function ($q) use ($filiereId) {
                $q->where('filiere_id', $filiereId);
            });
        }

        // Get unique group+module pairs (one row per combination)
        $pairs = $scheduleQuery->get()
            ->unique(fn($s) => $s->group_id . '_' . $s->module_id)
            ->values();

        if ($pairs->isEmpty()) {
            return [
                'success'   => false,
                'scheduled' => 0,
                'failed'    => 0,
                'errors'    => [],
                'message'   => 'Aucun couple groupe/module trouvé dans l\'emploi du temps' .
                               ($filiereId ? ' pour cette filière.' : '.') .
                               ' Vérifiez que des emplois du temps sont bien saisis.',
            ];
        }

        $startDate = Carbon::parse($session->start_date);
        $endDate   = Carbon::parse($session->end_date);
        $days      = $startDate->diffInDays($endDate);

        $slots    = ['09:00', '14:30'];
        $duration = 90;

        $scheduledCount = 0;
        $failedCount    = 0;
        $errors         = [];

        DB::beginTransaction();
        try {
            foreach ($pairs as $pair) {
                // Skip if an exam already exists for this group+module in this session
                $alreadyExists = Exam::where('exam_session_id', $session->id)
                    ->where('module_id', $pair->module_id)
                    ->where('group_id', $pair->group_id)
                    ->exists();

                if ($alreadyExists) {
                    continue;
                }

                $groupStudentCount = \App\Models\Student::where('group_id', $pair->group_id)->count();
                $requiredCapacity  = max($groupStudentCount, 1);

                $examScheduled = false;

                for ($d = 0; $d <= $days; $d++) {
                    $currentDate = $startDate->copy()->addDays($d);

                    // Skip Sundays
                    if ($currentDate->isSunday()) {
                        continue;
                    }

                    foreach ($slots as $startTimeStr) {
                        $start = Carbon::parse($currentDate->format('Y-m-d') . ' ' . $startTimeStr);
                        $end   = $start->copy()->addMinutes($duration);

                        // Max 2 exams per day for this group
                        $groupExamsThatDay = Exam::where('group_id', $pair->group_id)
                            ->where('date', $currentDate->format('Y-m-d'))
                            ->count();
                        if ($groupExamsThatDay >= 2) {
                            continue;
                        }

                        // Check group time-slot overlap
                        $groupOverlap = Exam::where('group_id', $pair->group_id)
                            ->where('date', $currentDate->format('Y-m-d'))
                            ->whereRaw("TIME(start_time) < ?", [$end->format('H:i:s')])
                            ->whereRaw("ADDTIME(TIME(start_time), SEC_TO_TIME(duration * 60)) > ?", [$start->format('H:i:s')])
                            ->exists();
                        if ($groupOverlap) {
                            continue;
                        }

                        // Find an available room with enough capacity
                        $rooms = Room::where('capacity', '>=', $requiredCapacity)->get();
                        $selectedRoomId = null;

                        foreach ($rooms as $room) {
                            $roomOverlap = Exam::where('room_id', $room->id)
                                ->where('date', $currentDate->format('Y-m-d'))
                                ->whereRaw("TIME(start_time) < ?", [$end->format('H:i:s')])
                                ->whereRaw("ADDTIME(TIME(start_time), SEC_TO_TIME(duration * 60)) > ?", [$start->format('H:i:s')])
                                ->exists();

                            if (!$roomOverlap) {
                                $selectedRoomId = $room->id;
                                break;
                            }
                        }

                        // If no room with sufficient capacity, try any available room
                        if (!$selectedRoomId) {
                            foreach (Room::all() as $room) {
                                $roomOverlap = Exam::where('room_id', $room->id)
                                    ->where('date', $currentDate->format('Y-m-d'))
                                    ->whereRaw("TIME(start_time) < ?", [$end->format('H:i:s')])
                                    ->whereRaw("ADDTIME(TIME(start_time), SEC_TO_TIME(duration * 60)) > ?", [$start->format('H:i:s')])
                                    ->exists();
                                if (!$roomOverlap) {
                                    $selectedRoomId = $room->id;
                                    break;
                                }
                            }
                        }

                        if (!$selectedRoomId) {
                            continue; // No room free for this slot
                        }

                        // ── Create the exam ──────────────────────────────────
                        $exam = Exam::create([
                            'exam_session_id' => $session->id,
                            'module_id'       => $pair->module_id,
                            'group_id'        => $pair->group_id,
                            'room_id'         => $selectedRoomId,
                            'date'            => $currentDate->format('Y-m-d'),
                            'start_time'      => $startTimeStr,
                            'duration'        => $duration,
                            'type'            => 'Final',
                        ]);

                        // Auto-assign proctors
                        $this->assignProctors($exam, $pair->professor_id, $requiredCapacity);

                        // Auto-generate convocations for all students in the group
                        $students = \App\Models\Student::where('group_id', $pair->group_id)
                            ->with(['user', 'group.filiere'])
                            ->get();

                        foreach ($students as $student) {
                            \App\Models\Convocation::firstOrCreate(
                                ['exam_id' => $exam->id, 'student_id' => $student->id],
                                [
                                    'reference' => \App\Models\Convocation::generateReference(),
                                    'status'    => 'pending',
                                ]
                            );
                        }

                        $examScheduled = true;
                        $scheduledCount++;
                        break 2; // Move to next group+module pair
                    }
                }

                if (!$examScheduled) {
                    $failedCount++;
                    $errors[] = "Impossible de planifier : " .
                        ($pair->module->name ?? '?') . " — groupe " . ($pair->group->name ?? '?') .
                        " (aucun créneau/salle disponible)";
                }
            }

            DB::commit();

            return [
                'success'   => true,
                'scheduled' => $scheduledCount,
                'failed'    => $failedCount,
                'errors'    => $errors,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }


    private function assignProctors(Exam $exam, $defaultProfessorId, $studentCount)
    {
        // 1 proctor per 30 students
        $proctorsNeeded = ceil($studentCount / 30);
        $assignedProctors = [];

        $start = Carbon::parse($exam->date . " " . $exam->start_time);
        $end = $start->copy()->addMinutes($exam->duration);

        // Try to assign the default professor (the one who teaches the module) first
        if ($defaultProfessorId && $this->isProfessorAvailableAndFree($defaultProfessorId, $exam->date, $start, $end)) {
            $assignedProctors[] = $defaultProfessorId;
        }

        // Fill remaining proctor spots
        if (count($assignedProctors) < $proctorsNeeded) {
            // Find professors who indicated availability on this date
            // Order by the number of exams they are already proctoring (load balancing)
            $availableProfs = ProfessorAvailability::where('available_date', $exam->date)
                ->pluck('professor_id');

            if ($availableProfs->isNotEmpty()) {
                $professors = Professor::whereIn('id', $availableProfs)
                    ->whereNotIn('id', $assignedProctors)
                    ->withCount('exams')
                    ->orderBy('exams_count', 'asc')
                    ->get();

                foreach ($professors as $prof) {
                    if ($this->isProfessorAvailableAndFree($prof->id, $exam->date, $start, $end)) {
                        $assignedProctors[] = $prof->id;
                        if (count($assignedProctors) >= $proctorsNeeded) {
                            break;
                        }
                    }
                }
            }
        }

        if (!empty($assignedProctors)) {
            $exam->proctors()->attach($assignedProctors);
        }
    }

    private function isProfessorAvailableAndFree($professorId, $date, $start, $end): bool
    {
        $overlap = Exam::whereHas('proctors', function($query) use ($professorId) {
                $query->where('professors.id', $professorId);
            })
            ->where('date', $date)
            ->whereRaw("TIME(start_time) < ?", [$end->format('H:i:s')])
            ->whereRaw("ADDTIME(TIME(start_time), SEC_TO_TIME(duration * 60)) > ?", [$start->format('H:i:s')])
            ->exists();

        return !$overlap;
    }
}
