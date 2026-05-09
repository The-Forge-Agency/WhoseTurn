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
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('frequency')->default('weekly')->after('enabled');
            $table->string('icon_url')->nullable()->after('icon_slug');
            $table->string('icon_slug')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['frequency', 'icon_url']);
            $table->string('icon_slug')->nullable(false)->change();
        });
    }
};
