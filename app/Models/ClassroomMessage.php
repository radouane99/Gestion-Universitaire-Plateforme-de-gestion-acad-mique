<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassroomMessage extends Model
{
    protected $fillable = [
        'user_id',
        'group_id',
        'module_id',
        'message',
        'file_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
