<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reclamation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'module_id',
        'exam_id',
        'grade_id',
        'reason',
        'prof_comment',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => '⏳ En attente',
            'reviewed' => '👀 En cours',
            'accepted' => '✅ Acceptée',
            'rejected' => '❌ Refusée',
            default => 'Inconnu',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
            'reviewed' => 'bg-blue-50 text-blue-700 border-blue-200',
            'accepted' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'rejected' => 'bg-red-50 text-red-700 border-red-200',
            default => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    }
}
