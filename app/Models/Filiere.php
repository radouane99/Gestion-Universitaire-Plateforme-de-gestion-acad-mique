<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Filiere extends Model
{
    protected $fillable = ['code', 'name', 'description'];

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }
}
