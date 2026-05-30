<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Add derogation and status fields to students table
        Schema::table('students', function (Blueprint $table) {
            $table->boolean('has_derogation')->default(false)->after('group_id');
            $table->text('derogation_note')->nullable()->after('has_derogation');
            $table->boolean('is_last_chance')->default(false)->after('derogation_note');
        });

        // 2. Create student_credit_modules pivot table
        Schema::create('student_credit_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->nullable()->constrained()->onDelete('set null');
            $table->string('status')->default('pending'); // pending, validated, not_validated
            $table->timestamps();

            // Unique constraint to prevent duplicate credits for the same module
            $table->unique(['student_id', 'module_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_credit_modules');

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['has_derogation', 'derogation_note', 'is_last_chance']);
        });
    }
};
