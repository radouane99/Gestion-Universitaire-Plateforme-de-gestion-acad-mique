<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Seuil d'avertissement (80h par défaut)
            $table->integer('absence_warning_threshold')->default(80)->after('exam_rules');
            // Seuil conseil de discipline (120h par défaut)
            $table->integer('absence_discipline_threshold')->default(120)->after('absence_warning_threshold');
            // Note minimale pour droit au rattrapage (< 10)
            $table->decimal('retake_min_grade', 4, 2)->default(10.00)->after('absence_discipline_threshold');
            // Texte personnalisé notification discipline
            $table->text('discipline_notification_text')->nullable()->after('retake_min_grade');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'absence_warning_threshold',
                'absence_discipline_threshold',
                'retake_min_grade',
                'discipline_notification_text',
            ]);
        });
    }
};
