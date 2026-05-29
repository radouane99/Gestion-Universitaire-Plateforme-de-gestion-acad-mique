<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisciplineCase extends Model
{
    protected $fillable = [
        'student_id',
        'total_unjustified_hours',
        'status',
        'admin_comment',
        'treated_at',
        'treated_by',
    ];

    protected $casts = [
        'total_unjustified_hours' => 'float',
        'treated_at' => 'datetime',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function treatedBy()
    {
        return $this->belongsTo(User::class, 'treated_by');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open'     => 'Ouvert',
            'notified' => 'Étudiant Notifié',
            'treated'  => 'Traité',
            default    => 'Inconnu',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open'     => 'bg-red-100 text-red-700 border-red-200',
            'notified' => 'bg-amber-100 text-amber-700 border-amber-200',
            'treated'  => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            default    => 'bg-gray-100 text-gray-600 border-gray-200',
        };
    }
}
