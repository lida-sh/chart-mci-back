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
         Schema::table('senior_experts', function (Blueprint $table) {
        $table->dropUnique('senior_experts_slug_unique');

        $table->unique(['slug', 'architecture_id']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('senior_experts', function (Blueprint $table) {
        $table->dropUnique(['slug', 'architecture_id']);

        $table->unique('title');
    });
    }
};
