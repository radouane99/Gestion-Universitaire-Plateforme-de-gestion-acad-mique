<?php

namespace App\Policies;

use App\Models\Schedule;
use App\Models\Professor;

class SchedulePolicy
{
    /**
     * Determine whether the professor can view the schedule.
     */
    public function view(Professor $professor, Schedule $schedule)
    {
        return $professor->id === $schedule->professor_id;
    }

    /**
     * Determine whether the professor can update the schedule (or related actions like absences, grades).
     */
    public function update(Professor $professor, Schedule $schedule)
    {
        return $professor->id === $schedule->professor_id;
    }
}
