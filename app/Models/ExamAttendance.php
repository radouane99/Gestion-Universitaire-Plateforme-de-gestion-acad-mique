<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAttendance extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'status',
        'marked_by',
        'marked_at',
        'ip_address',
        'notes',
    ];

    protected $casts = [
        'marked_at' => 'datetime',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function justification()
    {
        return $this->hasOne(ExamJustification::class);
    }

    public function retakeEligibility()
    {
        return $this->hasOneThrough(
            RetakeEligibility::class,
            Exam::class,
            'id',        // exam.id
            'exam_id',   // retake_eligibilities.exam_id
            'exam_id',   // exam_attendances.exam_id
            'id'         // exam.id
        );
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'present' => 'Présent',
            'absent'  => 'Absent',
            'late'    => 'Retard',
            'fraud'   => 'Fraude',
            default   => 'Inconnu',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'present' => 'bg-emerald-100 text-emerald-700',
            'absent'  => 'bg-red-100 text-red-700',
            'late'    => 'bg-amber-100 text-amber-700',
            'fraud'   => 'bg-purple-100 text-purple-700',
            default   => 'bg-gray-100 text-gray-600',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'present' => '✅',
            'absent'  => '❌',
            'late'    => '⏰',
            'fraud'   => '🚨',
            default   => '❓',
        };
    }

    public function isAbsent(): bool
    {
        return $this->status === 'absent';
    }

    public function needsJustification(): bool
    {
        return in_array($this->status, ['absent', 'fraud']);
    }
}
