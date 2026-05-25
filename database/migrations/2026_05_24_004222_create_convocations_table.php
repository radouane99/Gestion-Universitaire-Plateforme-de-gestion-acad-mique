<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('convocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('reference')->unique(); // e.g. CONV-2026-000123
            $table->enum('status', ['pending', 'sent', 'downloaded'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['exam_id', 'student_id'], 'exam_student_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('convocations');
    }
};
