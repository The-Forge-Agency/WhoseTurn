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
        Schema::create('task_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coloc_id')->constrained()->cascadeOnDelete();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_roommate_id')->constrained('roommates')->cascadeOnDelete();
            $table->foreignId('actual_roommate_id')->nullable()->constrained('roommates')->nullOnDelete();
            $table->string('status');
            $table->unsignedInteger('week');
            $table->unsignedInteger('year');
            $table->timestamps();

            $table->unique(['task_id', 'week', 'year']);
            $table->index(['coloc_id', 'week', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_completions');
    }
};
