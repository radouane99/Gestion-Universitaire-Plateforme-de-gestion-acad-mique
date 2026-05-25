<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['normal_autumn', 'normal_spring', 'retake_autumn', 'retake_spring']);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
            
            // Unicité: chaque type n'apparaît qu'une fois par année
            $table->unique(['academic_year_id', 'type']);
        });

        // Supprimer les colonnes globales de la table academic_years
        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropColumn(['exam_start_date', 'exam_end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->date('exam_start_date')->nullable();
            $table->date('exam_end_date')->nullable();
        });

        Schema::dropIfExists('exam_sessions');
    }
};
