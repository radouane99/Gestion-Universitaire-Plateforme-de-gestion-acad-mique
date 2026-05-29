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
        Schema::table('students', function (Blueprint $table) {
            $table->decimal('rachat_average', 4, 2)->nullable()->after('academic_year_id');
            $table->string('rachat_decision')->nullable()->after('rachat_average');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->integer('module_exclusion_threshold')->default(12)->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['rachat_average', 'rachat_decision']);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('module_exclusion_threshold');
        });
    }
};
