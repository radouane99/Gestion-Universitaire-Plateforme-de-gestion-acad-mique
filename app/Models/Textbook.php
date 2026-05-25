<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Textbook extends Model
{
    protected $fillable = [
        'professor_id',
        'group_id',
        'module_id',
        'date',
        'start_time',
        'end_time',
        'type',
        'objective',
    ];

    public function professor()
    {
        return $this->belongsTo(Professor::class);
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
