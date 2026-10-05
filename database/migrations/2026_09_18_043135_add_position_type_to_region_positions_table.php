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
        Schema::table('region_positions', function (Blueprint $table) {
            $table->enum('position_type', ['deputy', 'manager', 'department_head', 'expert', 'senior_expert', 'office_manager', 'agent', 'center_head', 'unit_head'])
                ->default('expert')
                ->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('region_positions', function (Blueprint $table) {
            $table->dropColumn('position_type');
        });
    }
};
