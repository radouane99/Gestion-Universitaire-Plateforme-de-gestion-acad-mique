<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pv_examens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->integer('presents_count')->default(0);
            $table->integer('absents_count')->default(0);
            $table->integer('retards_count')->default(0);
            $table->text('incidents')->nullable();
            $table->boolean('fraude_detected')->default(false);
            $table->text('fraude_details')->nullable();
            $table->text('remarques')->nullable();
            $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pv_examens');
    }
};
