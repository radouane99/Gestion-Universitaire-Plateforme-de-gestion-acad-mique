<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            // present, absent, late, fraud (triche)
            $table->enum('status', ['present', 'absent', 'late', 'fraud'])->default('absent');
            // Qui a marqué la présence (prof surveillant ou admin)
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('marked_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('notes')->nullable(); // Remarques libres (pour fraude etc.)
            $table->timestamps();

            // Un seul enregistrement par étudiant par examen
            $table->unique(['exam_id', 'student_id']);
            $table->index(['student_id', 'exam_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attendances');
    }
};
