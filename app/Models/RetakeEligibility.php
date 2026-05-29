<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetakeEligibility extends Model
{
    protected $fillable = [
        'student_id',
        'exam_id',
        'exam_session_id',
        'reason',
        'status',
        'admin_decision',
        'admin_comment',
        'decided_by',
        'decided_at',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function examSession()
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function decidedBy()
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function getReasonLabelAttribute(): string
    {
        return match ($this->reason) {
            'exam_absence_justified' => 'Absence justifiée à l\'examen',
            'low_grade'              => 'Note insuffisante (< ' . $this->getMinGrade() . '/20)',
            'admin_decision'         => 'Décision administrative',
            default                  => 'Autre',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'eligible'     => 'Éligible',
            'not_eligible' => 'Non Éligible',
            'pending'      => 'En Attente',
            default        => 'Inconnu',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'eligible'     => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'not_eligible' => 'bg-red-100 text-red-700 border-red-200',
            'pending'      => 'bg-amber-100 text-amber-700 border-amber-200',
            default        => 'bg-gray-100 text-gray-600 border-gray-200',
        };
    }

    public function getAdminDecisionLabelAttribute(): string
    {
        return match ($this->admin_decision) {
            'approved' => 'Rattrapage Autorisé',
            'rejected' => 'Rattrapage Refusé',
            default    => 'En attente de décision',
        };
    }

    public function isApproved(): bool
    {
        return $this->admin_decision === 'approved';
    }

    private function getMinGrade(): float
    {
        return \App\Models\Setting::first()?->retake_min_grade ?? 10.0;
    }
}
