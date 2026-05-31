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
        // 1. Add evaluation_open field to settings table if not exists
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'evaluation_open')) {
                $table->boolean('evaluation_open')->default(false);
            }
        });

        // 2. Create module_evaluations table
        Schema::create('module_evaluations', function (Blueprint $table) {
            $table->id();
            $table->string('student_hash')->index();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->foreignId('professor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete();
            
            // Ratings (1 to 5)
            $table->unsignedTinyInteger('q1_rating'); // Organisation du cours
            $table->unsignedTinyInteger('q2_rating'); // Clarté des explications
            $table->unsignedTinyInteger('q3_rating'); // Disponibilité du professeur
            $table->unsignedTinyInteger('q4_rating'); // Utilité du contenu
            
            $table->text('comment')->nullable();
            $table->timestamps();

            // Prevent duplicate submissions per student per module
            $table->unique(['student_hash', 'module_id'], 'eval_student_module_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_evaluations');
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'evaluation_open')) {
                $table->dropColumn('evaluation_open');
            }
        });
    }
};
