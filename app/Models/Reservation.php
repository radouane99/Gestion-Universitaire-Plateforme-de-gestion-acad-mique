<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = ['professor_id', 'room_id', 'start_time', 'end_time', 'purpose', 'status'];

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
