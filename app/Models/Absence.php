<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    protected $fillable = [
        'student_id', 
        'module_id',
        'date', 
        'session_type', 
        'duration',
        'is_justified', 
        'justification_path', 
        'justification_status'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
