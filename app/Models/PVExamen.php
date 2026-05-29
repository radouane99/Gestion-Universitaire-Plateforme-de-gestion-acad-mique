<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PVExamen extends Model
{
    use HasFactory;

    protected $table = 'pv_examens';

    protected $fillable = [
        'exam_id',
        'room_id',
        'presents_count',
        'absents_count',
        'retards_count',
        'incidents',
        'fraude_detected',
        'fraude_details',
        'remarques',
        'submitted_by',
        'submitted_at',
    ];

    protected $casts = [
        'fraude_detected' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
