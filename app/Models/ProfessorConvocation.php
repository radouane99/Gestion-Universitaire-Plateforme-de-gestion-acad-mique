<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessorConvocation extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'sent_at'      => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Generate a unique reference for the professor convocation.
     */
    public static function generateReference(): string
    {
        $last = static::max('id') ?? 0;
        return 'SURV-' . now()->year . '-' . str_pad($last + 1, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Mark this convocation as generated.
     */
    public function markAsGenerated(): void
    {
        $this->update(['status' => 'generated']);
    }

    /**
     * Mark this convocation as confirmed by the professor.
     */
    public function markAsConfirmed(): void
    {
        $this->update([
            'status'       => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Get a human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'En attente',
            'generated'   => 'Générée',
            'sent'        => 'Envoyée',
            'downloaded'  => 'Téléchargée',
            'confirmed'   => 'Confirmée',
            default       => 'Inconnu',
        };
    }

    /**
     * Get a Tailwind CSS color class for the status badge.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'bg-gray-100 text-gray-600',
            'generated'  => 'bg-blue-100 text-blue-700',
            'sent'       => 'bg-emerald-100 text-emerald-700',
            'downloaded' => 'bg-purple-100 text-purple-700',
            'confirmed'  => 'bg-green-100 text-green-800',
            default      => 'bg-gray-100 text-gray-600',
        };
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeForSession($query, int $sessionId)
    {
        return $query->whereHas('exam', fn ($q) => $q->where('exam_session_id', $sessionId));
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUpcoming($query)
    {
        return $query->whereHas('exam', fn ($q) => $q->where('date', '>=', now()->toDateString()));
    }
}
