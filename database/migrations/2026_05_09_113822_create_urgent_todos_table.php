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
        Schema::create('urgent_todos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coloc_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by_roommate_id')->nullable()->constrained('roommates')->nullOnDelete();
            $table->foreignId('done_by_roommate_id')->nullable()->constrained('roommates')->nullOnDelete();
            $table->string('title');
            $table->boolean('done')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('urgent_todos');
    }
};
