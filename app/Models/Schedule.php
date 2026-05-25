<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'group_id', 
        'module_id', 
        'professor_id', 
        'room_id', 
        'date',
        'day_of_week', 
        'start_time', 
        'end_time'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
