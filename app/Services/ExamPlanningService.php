<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Module;
use App\Models\Room;
use App\Models\Professor;
use App\Models\ProfessorAvailability;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ExamPlanningService
{
    protected $conflictChecker;

    public function __construct(ExamConflictChecker $conflictChecker)
    {
        $this->conflictChecker = $conflictChecker;
    }

    /**
     * Simulate planning for an exam session.
     * Generates a preview without saving anything to the database (or saves as draft/simulated).
     * Since we want to save the simulation and allow manual edits, we will save to the DB but mark the session as 'simulated'.
     */
    public function generatePlanning(ExamSession $session, $periodDays = null, $timeSlots = null)
    {
        // 1. Gather constraints and data
        $modules = Module::with('filiere')->get();
        // Assume each group needs an exam for each module of its filiere? 
        // Or do we just get all groups and their filiere's modules?
        $groups = \App\Models\Group::with('filiere.modules', 'students')->get();
        $rooms = Room::orderBy('capacity', 'desc')->get();
        
        // Time slots (default if not provided)
        if (!$timeSlots) {
            $timeSlots = [
                ['start' => '09:00', 'end' => '10:30'],
                ['start' => '11:00', 'end' => '12:30'],
                ['start' => '14:30', 'end' => '16:00'],
                ['start' => '16:30', 'end' => '18:00'],
            ];
        }

        // Days
        if (!$periodDays) {
            $period = CarbonPeriod::create($session->start_date, $session->end_date);
            $periodDays = [];
            foreach ($period as $date) {
                if ($date->isWeekday() || $date->isSaturday()) { // Excluding Sunday
                    $periodDays[] = $date->format('Y-m-d');
                }
            }
        }

        $results = [
            'planned' => 0,
            'unplanned' => [],
            'conflicts' => [],
        ];

        // Ensure session is marked as simulated
        $session->update(['status' => 'simulated']);

        // Clear existing drafted exams for this session to start fresh (optional, but good for pure generation)
        Exam::where('exam_session_id', $session->id)->delete();

        // Queue of exams to schedule: 1 per group per module
        $examsToSchedule = [];
        foreach ($groups as $group) {
            if (!$group->filiere) continue;
            foreach ($group->filiere->modules as $module) {
                $examsToSchedule[] = [
                    'group' => $group,
                    'module' => $module,
                    'student_count' => $group->students->count(),
                ];
            }
        }

        // Try to place each exam
        foreach ($examsToSchedule as $examData) {
            $group = $examData['group'];
            $module = $examData['module'];
            $studentCount = $examData['student_count'];
            $placed = false;

            // Find a slot
            foreach ($periodDays as $date) {
                if ($placed) break;

                foreach ($timeSlots as $slot) {
                    if ($placed) break;

                    // Check group availability
                    $groupAvail = $this->conflictChecker->isGroupAvailable($group->id, $date, $slot['start'], $slot['end']);
                    if (!$groupAvail['available']) {
                        continue;
                    }

                    // Find a room
                    $selectedRoom = null;
                    foreach ($rooms as $room) {
                        // Needs to be big enough (or we just pick the biggest available if no perfect fit, but here simple heuristic)
                        if ($room->capacity >= $studentCount) {
                            if ($this->conflictChecker->isRoomAvailable($room->id, $date, $slot['start'], $slot['end'])) {
                                $selectedRoom = $room;
                                break;
                            }
                        }
                    }

                    if ($selectedRoom) {
                        // We found a date, time and room!
                        
                        // Let's find proctors (assume 1 proctor per 30 students, min 1)
                        $proctorsNeeded = max(1, ceil($studentCount / 30));
                        $assignedProctors = [];
                        
                        // Fetch available professors for this date
                        $availableProfs = ProfessorAvailability::where('available_date', $date)
                            ->inRandomOrder() // Simple distribution
                            ->get();

                        foreach ($availableProfs as $avail) {
                            if (count($assignedProctors) >= $proctorsNeeded) break;
                            if ($this->conflictChecker->isProctorAvailable($avail->professor_id, $date, $slot['start'], $slot['end'])) {
                                $assignedProctors[] = $avail->professor_id;
                            }
                        }

                        // Create the exam
                        $exam = Exam::create([
                            'exam_session_id' => $session->id,
                            'module_id' => $module->id,
                            'group_id' => $group->id,
                            'room_id' => $selectedRoom->id,
                            'date' => $date,
                            'start_time' => $slot['start'],
                            'duration' => 90, // 1h30 minutes
                        ]);

                        if (count($assignedProctors) > 0) {
                            $exam->proctors()->attach($assignedProctors);
                        } else {
                            // Warning: No proctors available
                            $results['conflicts'][] = "Examen (Module {$module->name}, Groupe {$group->name}) planifié le $date à {$slot['start']} mais AUCUN surveillant disponible.";
                        }

                        $placed = true;
                        $results['planned']++;
                    }
                }
            }

            if (!$placed) {
                $results['unplanned'][] = "Module {$module->name} pour le groupe {$group->name} (Manque de salle de capacité >= $studentCount ou créneau saturé).";
            }
        }

        return $results;
    }
}
