<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = ['student_id', 'module_id', 'cc1', 'cc2', 'exam', 'final_grade'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    // final = ((CC1 + CC2)/2 * 0.4 + Exam * 0.6)
    public function calculateFinalGrade()
    {
        if ($this->cc1 !== null && $this->cc2 !== null && $this->exam !== null) {
            $this->final_grade = (($this->cc1 + $this->cc2) / 2 * 0.4) + ($this->exam * 0.6);
            $this->save();
        }
    }
}
