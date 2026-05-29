<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamJustification extends Model
{
    protected $fillable = [
        'exam_attendance_id',
        'student_id',
        'justification_path',
        'student_comment',
        'status',
        'admin_comment',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function examAttendance()
    {
        return $this->belongsTo(ExamAttendance::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'En Attente',
            'approved' => 'Approuvée',
            'rejected' => 'Refusée',
            default    => 'Inconnu',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'bg-amber-100 text-amber-700 border-amber-200',
            'approved' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'rejected' => 'bg-red-100 text-red-700 border-red-200',
            default    => 'bg-gray-100 text-gray-600 border-gray-200',
        };
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
