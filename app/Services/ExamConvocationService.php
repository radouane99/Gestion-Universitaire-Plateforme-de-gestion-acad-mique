<?php

namespace App\Services;

use App\Models\ExamSession;
use App\Models\Convocation;
use App\Models\ProfessorConvocation;

class ExamConvocationService
{
    /**
     * Generate convocations and assign seats for all exams in a session.
     */
    public function generateConvocationsForSession(ExamSession $session)
    {
        // 1. Students
        $exams = $session->exams()->with('group.students')->get();
        $studentConvCount = 0;

        foreach ($exams as $exam) {
            $students = $exam->group->students()->orderBy('user_id')->get();
            $seatNumber = 1;

            foreach ($students as $student) {
                // Check if already exists
                $existing = Convocation::where('exam_id', $exam->id)
                    ->where('student_id', $student->id)
                    ->first();

                if (!$existing) {
                    Convocation::create([
                        'exam_id' => $exam->id,
                        'student_id' => $student->id,
                        'status' => 'generated',
                        'seat_number' => 'Place ' . $seatNumber,
                    ]);
                    $studentConvCount++;
                } else {
                    $existing->update([
                        'status' => 'generated',
                        'seat_number' => 'Place ' . $seatNumber,
                    ]);
                }
                $seatNumber++;
            }
        }

        // 2. Professors
        $profConvCount = 0;
        foreach ($exams as $exam) {
            foreach ($exam->proctors as $proctor) {
                $existingProf = ProfessorConvocation::where('exam_id', $exam->id)
                    ->where('professor_id', $proctor->id)
                    ->first();

                if (!$existingProf) {
                    ProfessorConvocation::create([
                        'exam_id' => $exam->id,
                        'professor_id' => $proctor->id,
                        'status' => 'generated',
                    ]);
                    $profConvCount++;
                } else {
                    $existingProf->update(['status' => 'generated']);
                }
            }
        }

        return [
            'students' => $studentConvCount,
            'professors' => $profConvCount,
        ];
    }
}
