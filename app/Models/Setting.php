<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];

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
}

