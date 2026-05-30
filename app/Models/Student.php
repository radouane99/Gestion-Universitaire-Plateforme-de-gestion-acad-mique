<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id', 
        'group_id', 
        'student_number', 
        'cin', 
        'academic_year_id',
        'has_derogation',
        'derogation_note',
        'is_last_chance',
        'birth_date',
        'birth_place',
        'father_name',
        'father_cin',
        'father_occupation',
        'mother_name',
        'mother_cin',
        'mother_occupation',
        'bac_filiere',
        'bac_grade',
        'bac_mention',
        'bac_year',
        'filiere_id',
        'registration_status',
        'registration_type'
    ];

    protected $casts = [
        'has_derogation' => 'boolean',
        'is_last_chance' => 'boolean',
        'birth_date' => 'date',
        'bac_grade' => 'decimal:2',
    ];

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function creditModules()
    {
        return $this->belongsToMany(Module::class, 'student_credit_modules')
                    ->withPivot('id', 'academic_year_id', 'status')
                    ->withTimestamps();
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    public function disciplineCases()
    {
        return $this->hasMany(DisciplineCase::class);
    }

    public function examAttendances()
    {
        return $this->hasMany(ExamAttendance::class);
    }

    public function examJustifications()
    {
        return $this->hasMany(ExamJustification::class);
    }

    public function retakeEligibilities()
    {
        return $this->hasMany(RetakeEligibility::class);
    }

    public function reclamations()
    {
        return $this->hasMany(Reclamation::class);
    }

    // ─── Absence Helpers ──────────────────────────────────────────────────────

    /**
     * Total heures d'absences non justifiées (anciennement absence_score)
     */
    public function getAbsenceScoreAttribute(): float
    {
        return (float) $this->absences()->where('is_justified', false)->sum('duration');
    }

    /**
     * Total heures d'absences justifiées
     */
    public function getJustifiedHoursAttribute(): float
    {
        return (float) $this->absences()->where('is_justified', true)->sum('duration');
    }

    /**
     * Total général d'absences (justifiées + non justifiées)
     */
    public function getTotalAbsenceHoursAttribute(): float
    {
        return (float) $this->absences()->sum('duration');
    }

    /**
     * Statut discipline basé sur les seuils configurables
     */
    public function getDisciplineStatusAttribute(): string
    {
        $settings   = \App\Models\Setting::first();
        $warning    = $settings?->absence_warning_threshold ?? 80;
        $discipline = $settings?->absence_discipline_threshold ?? 120;
        $score      = $this->absence_score;

        if ($score >= $discipline) return 'conseil_discipline';
        if ($score >= $warning)    return 'a_surveiller';
        return 'normal';
    }

    /**
     * A-t-il un dossier de discipline actif (ouvert ou notifié) ?
     */
    public function hasActiveDisciplineCase(): bool
    {
        return $this->disciplineCases()->whereIn('status', ['open', 'notified'])->exists();
    }

    /**
     * Retourne le dossier discipline actif s'il existe
     */
    public function getActiveDisciplineCaseAttribute(): ?DisciplineCase
    {
        return $this->disciplineCases()->whereIn('status', ['open', 'notified'])->latest()->first();
    }

    /**
     * Règle d'éligibilité à la réinscription
     */
    public function isEligibleForReinscription(): bool
    {
        $hasGrades = $this->grades()->exists();
        return $hasGrades && $this->registration_status === 'approved' && $this->registration_type !== 'reinscription';
    }

    /**
     * Calcul de la moyenne annuelle
     */
    public function getYearlyGpa(): float
    {
        $grades = $this->grades()->whereNotNull('final_grade')->get();
        if ($grades->isEmpty()) return 0.0;
        return (float) $grades->avg('final_grade');
    }

    /**
     * Liste des modules non validés (note < 10)
     */
    public function getFailedModules()
    {
        return $this->grades()
            ->whereNotNull('final_grade')
            ->where('final_grade', '<', 10)
            ->with('module')
            ->get()
            ->pluck('module');
    }

    /**
     * Token de sécurité unique pour l'Attestation de Réussite
     */
    public function getDocumentTokenAttribute(): string
    {
        return sha1($this->id . '_' . $this->cin);
    }
}

