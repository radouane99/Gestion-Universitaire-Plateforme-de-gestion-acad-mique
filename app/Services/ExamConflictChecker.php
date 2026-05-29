<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\Reservation;
use App\Models\Schedule;

class ExamConflictChecker
{
    /**
     * Check if a room is available at a given date and time.
     */
    public function isRoomAvailable($roomId, $date, $startTime, $endTime, $excludeExamId = null): bool
    {
        $startDateTime = $date . ' ' . $startTime;
        $endDateTime = $date . ' ' . $endTime;

        // Check against other exams
        $examConflict = Exam::where('room_id', $roomId)
            ->where('date', $date)
            ->when($excludeExamId, fn($q) => $q->where('id', '!=', $excludeExamId))
            ->get()
            ->contains(function ($exam) use ($startTime, $endTime) {
                return ($startTime < $exam->end_time && $endTime > $exam->start_time);
            });

        if ($examConflict) return false;

        // Check against approved reservations
        $reservationConflict = Reservation::where('room_id', $roomId)
            ->where('status', 'approved')
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                $query->where('start_time', '<', $endDateTime)
                      ->where('end_time', '>', $startDateTime);
            })->exists();

        if ($reservationConflict) return false;

        // Check against schedules
        $dayOfWeek = date('N', strtotime($date));
        $scheduleConflict = Schedule::where('room_id', $roomId)
            ->where('day_of_week', $dayOfWeek)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
            })->exists();

        if ($scheduleConflict) return false;

        return true;
    }

    /**
     * Check if a group already has an exam overlapping, or if they reached max 2 exams per day.
     */
    public function isGroupAvailable($groupId, $date, $startTime, $endTime, $excludeExamId = null): array
    {
        $examsOnDate = Exam::where('group_id', $groupId)
            ->where('date', $date)
            ->when($excludeExamId, fn($q) => $q->where('id', '!=', $excludeExamId))
            ->get();

        if ($examsOnDate->count() >= 2) {
            return ['available' => false, 'reason' => 'Le groupe a déjà le maximum de 2 examens ce jour-là.'];
        }

        foreach ($examsOnDate as $exam) {
            if ($startTime < $exam->end_time && $endTime > $exam->start_time) {
                return ['available' => false, 'reason' => 'Le groupe a déjà un examen sur ce créneau.'];
            }
        }

        return ['available' => true];
    }

    /**
     * Check if a professor is available for surveillance.
     */
    public function isProctorAvailable($professorId, $date, $startTime, $endTime, $excludeExamId = null): bool
    {
        // 1. Has the professor declared availability for this date?
        $isAvailable = \App\Models\ProfessorAvailability::where('professor_id', $professorId)
            ->where('available_date', $date)
            ->exists();

        if (!$isAvailable) return false;

        // 2. Is the professor already proctoring another exam at this time?
        $proctorConflict = Exam::whereHas('proctors', function($q) use ($professorId) {
                $q->where('professor_id', $professorId);
            })
            ->where('date', $date)
            ->when($excludeExamId, fn($q) => $q->where('id', '!=', $excludeExamId))
            ->get()
            ->contains(function ($exam) use ($startTime, $endTime) {
                return ($startTime < $exam->end_time && $endTime > $exam->start_time);
            });

        if ($proctorConflict) return false;

        return true;
    }
}
