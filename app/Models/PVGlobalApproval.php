<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PVGlobalApproval extends Model
{
    use HasFactory;

    protected $table = 'pv_global_approvals';

    protected $fillable = [
        'filiere_id',
        'academic_year_id',
        'level',
        'is_validated',
        'validated_by',
        'validated_at'
    ];

    protected $casts = [
        'is_validated' => 'boolean',
        'validated_at' => 'datetime'
    ];

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
