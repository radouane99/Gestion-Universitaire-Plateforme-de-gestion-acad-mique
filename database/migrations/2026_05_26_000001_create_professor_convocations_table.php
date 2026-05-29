<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professor_convocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->string('reference')->unique(); // e.g. PCONV-2026-000001
            $table->string('status')->default('pending'); // pending|generated|sent|downloaded|confirmed
            $table->string('role')->default('assistant');  // principal|assistant
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['professor_id', 'exam_id'], 'prof_exam_conv_unique');
            $table->index(['professor_id', 'status']);
            $table->index(['exam_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professor_convocations');
    }
};
