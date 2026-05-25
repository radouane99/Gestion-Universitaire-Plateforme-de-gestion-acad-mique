<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('convocations', function (Blueprint $table) {
            $table->string('room_name')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('module_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('convocations', function (Blueprint $table) {
            $table->dropColumn(['room_name', 'start_time', 'end_time', 'module_name']);
        });
    }
};
?>
