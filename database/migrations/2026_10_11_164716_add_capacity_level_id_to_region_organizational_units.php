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
        Schema::table('region_organizational_units', function (Blueprint $table) {
            $table->foreignId('capacity_level_id')
                ->nullable()
                ->constrained('telecom_capacity_levels')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('region_organizational_units', function (Blueprint $table) {
            $table->dropForeign(['capacity_level_id']);
            $table->dropColumn('capacity_level_id');
        });
    }
};
