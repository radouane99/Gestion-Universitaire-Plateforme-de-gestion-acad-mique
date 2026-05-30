<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = ['student_id', 'module_id', 'cc1', 'cc2', 'exam', 'rattrapage', 'final_grade', 'is_archived', 'academic_year_id'];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    // final = ((CC1 + CC2)/2 * 0.4 + Exam * 0.6)
    // with retake = max(normal_grade, ((CC1 + CC2)/2 * 0.4 + Rattrapage * 0.6))
    public function calculateFinalGrade()
    {
        $cc_avg = 0;
        if ($this->cc1 !== null && $this->cc2 !== null) {
            $cc_avg = ($this->cc1 + $this->cc2) / 2;
        } elseif ($this->cc1 !== null) {
            $cc_avg = $this->cc1;
        } elseif ($this->cc2 !== null) {
            $cc_avg = $this->cc2;
        }

        $normal_grade = null;
        if ($this->exam !== null) {
            $normal_grade = ($cc_avg * 0.4) + ($this->exam * 0.6);
        }

        if ($this->rattrapage !== null) {
            $retake_grade = ($cc_avg * 0.4) + ($this->rattrapage * 0.6);
            if ($normal_grade !== null) {
                $this->final_grade = max($normal_grade, $retake_grade);
            } else {
                $this->final_grade = $retake_grade;
            }
        } elseif ($normal_grade !== null) {
            $this->final_grade = $normal_grade;
        }

        $this->save();

        // Automatically update credit status if this module is carrying credit for the student
        if ($this->final_grade !== null) {
            $credit = \DB::table('student_credit_modules')
                ->where('student_id', $this->student_id)
                ->where('module_id', $this->module_id)
                ->first();

            if ($credit) {
                // If final grade >= 10, it is validated, otherwise not_validated
                $status = ($this->final_grade >= 10) ? 'validated' : 'not_validated';
                \DB::table('student_credit_modules')
                    ->where('student_id', $this->student_id)
                    ->where('module_id', $this->module_id)
                    ->update([
                        'status' => $status,
                        'updated_at' => now()
                    ]);
            }
        }
    }
}
