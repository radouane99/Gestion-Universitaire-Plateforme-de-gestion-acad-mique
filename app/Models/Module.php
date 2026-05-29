<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['code', 'name', 'coefficient', 'filiere_id', 'semester_id'];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function reclamations()
    {
        return $this->hasMany(Reclamation::class);
    }
}
