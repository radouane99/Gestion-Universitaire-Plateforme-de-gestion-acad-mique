<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Convertir absences.duration de int (heures entières) vers decimal(5,2)
     * pour supporter les durées comme 1.5h (1h30), 2.5h, etc.
     */
    public function up(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            $table->decimal('duration', 5, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            $table->integer('duration')->default(0)->change();
        });
    }
};
