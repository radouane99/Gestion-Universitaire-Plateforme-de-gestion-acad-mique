<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclamations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('grade_id')->constrained()->cascadeOnDelete();
            $table->text('reason');
            $table->text('prof_comment')->nullable();
            $table->string('status')->default('pending'); // pending, reviewed, accepted, rejected
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclamations');
    }
};
