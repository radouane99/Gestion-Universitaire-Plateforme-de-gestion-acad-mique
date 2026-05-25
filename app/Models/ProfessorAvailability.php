<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessorAvailability extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'available_date' => 'date',
    ];

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }
}
