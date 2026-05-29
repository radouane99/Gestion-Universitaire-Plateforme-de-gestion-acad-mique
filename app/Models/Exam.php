<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function examSession()
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function proctors()
    {
        return $this->belongsToMany(Professor::class, 'exam_proctor');
    }

    public function convocations()
    {
        return $this->hasMany(Convocation::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function pvExamen()
    {
        return $this->hasOne(PVExamen::class);
    }

    public function reclamations()
    {
        return $this->hasMany(Reclamation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    public function getEndTimeAttribute()
    {
        return date('H:i', strtotime($this->start_time) + ($this->duration * 60));
    }
}
