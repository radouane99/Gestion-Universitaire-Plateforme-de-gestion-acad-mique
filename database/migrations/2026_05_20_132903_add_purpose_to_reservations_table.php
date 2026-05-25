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
        if (!Schema::hasColumn('reservations', 'purpose')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->text('purpose')->nullable()->after('end_time');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('reservations', 'purpose')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropColumn('purpose');
            });
        }
    }
};
