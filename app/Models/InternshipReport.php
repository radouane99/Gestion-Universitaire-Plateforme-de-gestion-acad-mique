<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternshipReport extends Model
{
    protected $fillable = [
        'internship_id',
        'report_number',
        'title',
        'content',
        'file_path',
        'submitted_at',
        'tutor_feedback',
        'status',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function internship()
    {
        return $this->belongsTo(Internship::class);
    }
}
