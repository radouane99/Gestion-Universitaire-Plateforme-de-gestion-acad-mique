<?php
namespace App\Services;

use App\Models\ExamWeek;
use App\Models\Module;
use App\Models\Room;
use App\Models\ModuleSchedule;
use Illuminate\Support\Facades\Log;

class RandomModuleScheduler
{
    /**
     * Generate a random schedule for the given ExamWeek.
     *
     * - Picks 7 modules at random (or all if less).
     * - Assigns rooms respecting capacity.
     * - Splits large groups into two sessions (morning/afternoon) when the room capacity is much larger than the number of students.
     * - Creates ModuleSchedule entries with default time slots (08:00‑10:00, 14:00‑16:00).
     */
    public function generate(int $examWeekId): void
    {
        $examWeek = ExamWeek::findOrFail($examWeekId);

        // 1️⃣ Random modules (max 7)
        $modules = Module::inRandomOrder()->take(7)->get();
        if ($modules->isEmpty()) {
            Log::warning("RandomModuleScheduler: no modules found for exam week $examWeekId");
            return;
        }

        // 2️⃣ Rooms ordered by capacity (biggest first)
        $rooms = Room::orderByDesc('capacity')->get();
        if ($rooms->isEmpty()) {
            Log::warning('RandomModuleScheduler: no rooms defined');
            return;
        }

        // Default time slots (morning / afternoon)
        $morningStart = '08:00:00';
        $morningEnd   = '10:00:00';
        $afternoonStart = '14:00:00';
        $afternoonEnd   = '16:00:00';

        $currentDate = $examWeek->start_date;
        $endDate = $examWeek->end_date;

        foreach ($modules as $module) {
            // ---- Determine number of students for this module ----
            // TODO: replace with real count, e.g.: $studentsCount = $module->students()->count();
            $studentsCount = random_int(30, 70); // placeholder for demo

            // Find a room with enough capacity (or the smallest that fits)
            $room = $rooms->firstWhere('capacity', '>=', $studentsCount);
            if (! $room) {
                // If no single room fits, take the largest and plan two sessions
                $room = $rooms->first();
            }

            // Decide whether we need to split into two groups (keep groups <= capacity/2 as requested)
            $split = $studentsCount > ($room->capacity / 2);
            $groupSizes = $split ? [$studentsCount / 2, $studentsCount / 2] : [$studentsCount];

            foreach ($groupSizes as $index => $size) {
                // Ensure we stay inside the exam week dates
                if ($currentDate->gt($endDate)) {
                    Log::warning('RandomModuleScheduler ran out of dates while scheduling modules');
                    break 2; // exit both loops
                }

                // Alternate morning / afternoon for each group
                $isMorning = ($index % 2) === 0;
                $schedule = new ModuleSchedule();
                $schedule->exam_week_id = $examWeek->id;
                $schedule->room_id      = $room->id;
                $schedule->module_id    = $module->id;
                // professor assignment will be done later by admin (null for now)
                $schedule->professor_id = null;
                $schedule->date         = $currentDate->toDateString();
                $schedule->start_time   = $isMorning ? $morningStart : $afternoonStart;
                $schedule->end_time     = $isMorning ? $morningEnd   : $afternoonEnd;
                $schedule->save();
            }

            // Advance to next day after we have placed the module (ensuring at least two slots per day)
            $currentDate = $currentDate->addDay();
        }
    }
}
?>
