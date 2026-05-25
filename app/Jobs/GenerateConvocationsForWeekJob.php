<?php
namespace App\Jobs;

use App\Models\ExamWeek;
use App\Models\Convocation;
use App\Models\ModuleSchedule;
use App\Mail\ConvocationMail;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class GenerateConvocationsForWeekJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected int $examWeekId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $examWeekId)
    {
        $this->examWeekId = $examWeekId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        DB::transaction(function () {
            $examWeek = ExamWeek::with(['schedules.module', 'schedules.room', 'schedules.professor', 'exams.students'])
                ->findOrFail($this->examWeekId);

            // Validate schedule conflicts before proceeding
            $validator = app('\App\Services\ConvocationValidator');
            $conflicts = $validator->checkRoomTimeConflicts($this->examWeekId);
            if ($conflicts->isNotEmpty()) {
                Log::error('Room time conflicts detected for ExamWeek ID ' . $this->examWeekId, $conflicts->toArray());
                // Abort generation – admin will resolve conflicts
                return;
            }

            foreach ($examWeek->schedules as $schedule) {
                // For each schedule, generate a convocation per student in the linked exam(s)
                foreach ($examWeek->exams as $exam) {
                    foreach ($exam->students as $student) {
                        $convocation = Convocation::create([
                            'exam_id'        => $exam->id,
                            'student_id'     => $student->id,
                            'reference'      => Convocation::generateReference(),
                            'status'         => \App\Enums\ConvocationStatus::Pending,
                            'room_name'      => $schedule->room->name,
                            'start_time'     => $schedule->start_time,
                            'end_time'       => $schedule->end_time,
                            'module_name'    => $schedule->module->name,
                        ]);

                        // Dispatch email job (synchronous for simplicity, can be queued later)
                        Mail::to($student->email)->queue(new ConvocationMail($convocation));
                    }
                }
            }
        });
    }
}
?>
