<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retake_eligibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_session_id')->nullable()->constrained()->nullOnDelete();
            // Raison de l'éligibilité
            $table->enum('reason', [
                'exam_absence_justified', // Absent à l'examen avec justification approuvée
                'low_grade',              // Présent mais note < seuil minimum
                'admin_decision',         // Décision administrative directe
            ]);
            // eligible = calculé automatiquement, not_eligible, pending = en attente décision admin
            $table->enum('status', ['eligible', 'not_eligible', 'pending'])->default('pending');
            // Décision finale admin
            $table->enum('admin_decision', ['approved', 'rejected'])->nullable();
            $table->text('admin_comment')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();

            // Un seul enregistrement par étudiant par examen
            $table->unique(['student_id', 'exam_id']);
            $table->index(['exam_session_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retake_eligibilities');
    }
};
