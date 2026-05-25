<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $fillable = ['name', 'level'];

    public function modules()
    {
        return $this->hasMany(Module::class);
    }
}
