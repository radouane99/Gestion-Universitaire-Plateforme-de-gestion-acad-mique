<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Make group_id nullable so that pending students don't require an immediate group assignment
            $table->foreignId('group_id')->nullable()->change();

            // Personal & Birth Info
            $table->date('birth_date')->nullable()->after('cin');
            $table->string('birth_place')->nullable()->after('birth_date');

            // Father's details
            $table->string('father_name')->nullable()->after('birth_place');
            $table->string('father_cin')->nullable()->after('father_name');
            $table->string('father_occupation')->nullable()->after('father_cin');

            // Mother's details
            $table->string('mother_name')->nullable()->after('father_occupation');
            $table->string('mother_cin')->nullable()->after('mother_name');
            $table->string('mother_occupation')->nullable()->after('mother_cin');

            // Baccalaureate Info
            $table->string('bac_filiere')->nullable()->after('mother_occupation');
            $table->decimal('bac_grade', 4, 2)->nullable()->after('bac_filiere');
            $table->string('bac_mention')->nullable()->after('bac_grade');
            $table->integer('bac_year')->nullable()->after('bac_mention');

            // Registration Fields
            $table->foreignId('filiere_id')->nullable()->constrained('filieres')->onDelete('set null')->after('bac_year');
            $table->string('registration_status')->default('approved')->after('filiere_id'); // pending, approved, rejected
            $table->string('registration_type')->default('new')->after('registration_status'); // new, reinscription
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['filiere_id']);
            $table->dropColumn([
                'birth_date',
                'birth_place',
                'father_name',
                'father_cin',
                'father_occupation',
                'mother_name',
                'mother_cin',
                'mother_occupation',
                'bac_filiere',
                'bac_grade',
                'bac_mention',
                'bac_year',
                'filiere_id',
                'registration_status',
                'registration_type',
            ]);

            // Revert group_id to non-nullable if desired, but making it nullable is generally safe to leave
        });
    }
};
