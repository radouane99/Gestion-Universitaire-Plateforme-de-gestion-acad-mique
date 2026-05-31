<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'inscription_start_date' => 'datetime',
        'inscription_end_date' => 'datetime',
        'reinscription_start_date' => 'datetime',
        'reinscription_end_date' => 'datetime',
    ];

    /**
     * Récupère une valeur de setting par clé avec une valeur par défaut.
     * Exemple: Setting::get('absence_discipline_threshold', 120)
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = static::first();
        if (!$settings) return $default;
        return $settings->{$key} ?? $default;
    }

    /**
     * Récupère la configuration complète ou null si aucun enregistrement.
     */
    public static function current(): ?static
    {
        return static::first();
    }

    /**
     * Vérifie si la campagne d'inscription est ouverte
     */
    public static function isInscriptionOpen(): bool
    {
        $settings = static::first();
        if (!$settings || !$settings->inscription_start_date || !$settings->inscription_end_date) {
            return false;
        }
        return now()->between($settings->inscription_start_date, $settings->inscription_end_date);
    }

    /**
     * Vérifie si la campagne de réinscription est ouverte
     */
    public static function isReinscriptionOpen(): bool
    {
        $settings = static::first();
        if (!$settings || !$settings->reinscription_start_date || !$settings->reinscription_end_date) {
            return false;
        }
        return now()->between($settings->reinscription_start_date, $settings->reinscription_end_date);
    }
}

