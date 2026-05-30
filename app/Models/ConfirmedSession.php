<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfirmedSession extends Model
{
    protected $fillable = [
        'professor_id',
        'schedule_id',
        'group_id',
        'module_id',
        'date',
        'start_time',
        'end_time',
        'duration',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
