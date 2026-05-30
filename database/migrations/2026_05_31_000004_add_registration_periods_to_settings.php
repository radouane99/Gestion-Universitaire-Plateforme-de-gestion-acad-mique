<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dateTime('inscription_start_date')->nullable()->after('exam_rules');
            $table->dateTime('inscription_end_date')->nullable()->after('inscription_start_date');
            $table->dateTime('reinscription_start_date')->nullable()->after('inscription_end_date');
            $table->dateTime('reinscription_end_date')->nullable()->after('reinscription_start_date');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'inscription_start_date',
                'inscription_end_date',
                'reinscription_start_date',
                'reinscription_end_date',
            ]);
        });
    }
};
