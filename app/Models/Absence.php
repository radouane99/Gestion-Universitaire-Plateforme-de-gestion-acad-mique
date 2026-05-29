<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    protected $fillable = [
        'student_id',
        'module_id',
        'schedule_id',
        'date',
        'session_type',
        'duration',
        'is_justified',
        'justification_path',
        'justification_status',
        'is_archived',
        'academic_year_id',
    ];

    protected $casts = [
        'duration'     => 'float',
        'is_justified' => 'boolean',
        'is_archived'  => 'boolean',
        'date'         => 'date',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    public function scopeJustified($query)
    {
        return $query->where('is_justified', true);
    }

    public function scopeUnjustified($query)
    {
        return $query->where('is_justified', false);
    }

    public function scopePendingJustification($query)
    {
        return $query->where('justification_status', 'pending');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function getJustificationStatusLabelAttribute(): string
    {
        return match ($this->justification_status) {
            'none'     => 'Aucun',
            'pending'  => 'En Attente',
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
            default    => 'Inconnu',
        };
    }

    /**
     * Durée formatée: "1h30" ou "2h00"
     */
    public function getFormattedDurationAttribute(): string
    {
        $hours   = (int) $this->duration;
        $minutes = (int) round(($this->duration - $hours) * 60);
        return $hours . 'h' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
    }
}
