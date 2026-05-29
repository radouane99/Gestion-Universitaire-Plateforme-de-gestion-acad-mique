<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discipline_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->decimal('total_unjustified_hours', 6, 2)->default(0);
            // open = alerte déclenchée, notified = étudiant notifié, treated = dossier traité par admin
            $table->enum('status', ['open', 'notified', 'treated'])->default('open');
            $table->text('admin_comment')->nullable();
            $table->timestamp('treated_at')->nullable();
            $table->foreignId('treated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Un seul dossier actif par étudiant (le dernier ouvert)
            $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discipline_cases');
    }
};
