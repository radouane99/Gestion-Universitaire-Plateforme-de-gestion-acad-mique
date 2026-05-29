<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false)->after('final_grade');
            $table->foreignId('academic_year_id')->nullable()->after('is_archived')->constrained()->nullOnDelete();
        });

        Schema::table('absences', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false)->after('duration');
            $table->foreignId('academic_year_id')->nullable()->after('is_archived')->constrained()->nullOnDelete();
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false)->after('exam_session_id');
            $table->foreignId('academic_year_id')->nullable()->after('is_archived')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn(['is_archived', 'academic_year_id']);
        });

        Schema::table('absences', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn(['is_archived', 'academic_year_id']);
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn(['is_archived', 'academic_year_id']);
        });
    }
};
