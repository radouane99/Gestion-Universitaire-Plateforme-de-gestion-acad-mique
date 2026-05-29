<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Change the status column from an enum to a string so we can add 'generated'
     * without SQLite-incompatible ALTER COLUMN issues, and keep future flexibility.
     */
    public function up(): void
    {
        // For SQLite (dev), we need to recreate. For MySQL the string type handles it cleanly.
        Schema::table('convocations', function (Blueprint $table) {
            // Drop the old enum column and replace with string (string is compatible with all DB drivers)
            $table->string('status', 20)->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('convocations', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->change();
        });
    }
};
