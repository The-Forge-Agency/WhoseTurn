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
        Schema::create('roommates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coloc_id')->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('avatar_slug');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roommates');
    }
};
