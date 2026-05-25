<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Indexes for schedules
        Schema::table('schedules', function (Blueprint $table) {
            $table->index(['room_id', 'date', 'start_time', 'end_time'], 'schedules_room_date_time_idx');
            $table->index(['professor_id', 'date', 'start_time', 'end_time'], 'schedules_professor_date_time_idx');
        });

        // Indexes for reservations
        Schema::table('reservations', function (Blueprint $table) {
            $table->index(['room_id', 'start_time', 'end_time', 'status'], 'reservations_room_time_status_idx');
        });

        // Indexes for exams
        Schema::table('exams', function (Blueprint $table) {
            $table->index(['room_id', 'date', 'start_time'], 'exams_room_date_time_idx');
            $table->index(['group_id', 'date', 'start_time'], 'exams_group_date_time_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropIndex('schedules_room_date_time_idx');
            $table->dropIndex('schedules_professor_date_time_idx');
        });
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex('reservations_room_time_status_idx');
        });
        Schema::table('exams', function (Blueprint $table) {
            $table->dropIndex('exams_room_date_time_idx');
            $table->dropIndex('exams_group_date_time_idx');
        });
    }
};
