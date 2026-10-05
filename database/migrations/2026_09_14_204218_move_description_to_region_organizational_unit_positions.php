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
        Schema::table('region_organizational_unit_positions', function (Blueprint $table) {
            $table->text('description')->nullable()->after('approved_count');
        });
        Schema::table('region_positions', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('region_organizational_unit_positions', function (Blueprint $table) {
            $table->dropColumn('description');
        });
        Schema::table('region_positions', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
        });
    }
};
