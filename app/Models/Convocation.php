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
}
