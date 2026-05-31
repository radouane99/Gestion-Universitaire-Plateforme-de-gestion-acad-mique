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
        // 1. Create internships table
        Schema::create('internships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_tutor_id')->nullable()->constrained('professors')->nullOnDelete();
            $table->string('company_name');
            $table->string('company_address');
            $table->string('tutor_name');
            $table->string('tutor_email');
            $table->string('tutor_phone');
            $table->string('subject');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('pending'); // pending, active, completed, rejected
            $table->decimal('grade', 4, 2)->nullable(); // Grade out of 20
            $table->text('tutor_feedback')->nullable();
            $table->timestamps();
        });

        // 2. Create internship_reports table
        Schema::create('internship_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('report_number'); // Month index: 1, 2, 3, etc.
            $table->string('title');
            $table->text('content');
            $table->string('file_path')->nullable(); // Month report PDF/DOCX
            $table->dateTime('submitted_at');
            $table->text('tutor_feedback')->nullable();
            $table->string('status')->default('pending'); // pending, reviewed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internship_reports');
        Schema::dropIfExists('internships');
    }
};
