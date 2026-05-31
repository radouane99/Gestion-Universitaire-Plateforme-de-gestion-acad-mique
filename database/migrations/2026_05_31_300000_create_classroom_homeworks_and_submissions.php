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
        // 1. Create classroom_homeworks table
        Schema::create('classroom_homeworks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('attachment_path')->nullable();
            $table->dateTime('due_date');
            $table->timestamps();
        });

        // 2. Create classroom_submissions table
        Schema::create('classroom_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_homework_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->dateTime('submitted_at');
            $table->decimal('grade', 4, 2)->nullable(); // Grade out of 20
            $table->text('professor_comment')->nullable();
            $table->timestamps();

            // Enforce single submission per student per homework
            $table->unique(['classroom_homework_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classroom_submissions');
        Schema::dropIfExists('classroom_homeworks');
    }
};
