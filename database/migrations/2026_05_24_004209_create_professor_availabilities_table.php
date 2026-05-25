<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professor_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained()->onDelete('cascade');
            $table->date('available_date');
            $table->string('exam_week')->nullable(); // e.g. "Semaine d'examens Juin 2026"
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['professor_id', 'available_date'], 'prof_date_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professor_availabilities');
    }
};
