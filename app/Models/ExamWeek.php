<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamWeek extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
    ];

    /**
     * Get the exams belonging to this week.
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }
}
?>
