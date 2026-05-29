<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'cin')) {
                $table->string('cin')->nullable()->after('student_number');
            }
        });

        Schema::table('grades', function (Blueprint $table) {
            if (!Schema::hasColumn('grades', 'rattrapage')) {
                $table->decimal('rattrapage', 5, 2)->nullable()->after('exam');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'cin')) {
                $table->dropColumn('cin');
            }
        });

        Schema::table('grades', function (Blueprint $table) {
            if (Schema::hasColumn('grades', 'rattrapage')) {
                $table->dropColumn('rattrapage');
            }
        });
    }
};
