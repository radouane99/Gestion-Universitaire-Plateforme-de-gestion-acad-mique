<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Convocation extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Generate a unique reference for the convocation.
     */
    public static function generateReference(): string
    {
        $last = static::max('id') ?? 0;
        return 'CONV-' . now()->year . '-' . str_pad($last + 1, 6, '0', STR_PAD_LEFT);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeForSession($query, int $sessionId)
    {
        return $query->whereHas('exam', fn ($q) => $q->where('exam_session_id', $sessionId));
    }

    // ─── Status helpers ───────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'En attente',
            'generated'  => 'Générée',
            'sent'       => 'Envoyée',
            'downloaded' => 'Téléchargée',
            default      => 'Inconnu',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'bg-gray-100 text-gray-600',
            'generated'  => 'bg-blue-100 text-blue-700',
            'sent'       => 'bg-emerald-100 text-emerald-700',
            'downloaded' => 'bg-purple-100 text-purple-700',
            default      => 'bg-gray-100 text-gray-600',
        };
    }
}
