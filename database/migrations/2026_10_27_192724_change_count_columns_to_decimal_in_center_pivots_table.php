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
        Schema::table('center_pivots', function (Blueprint $table) {
            $table->decimal('fault_count', 12, 3)->change();

            $table->decimal('installed_and_displacement_count', 12, 3)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('center_pivots', function (Blueprint $table) {
            $table->integer('fault_count')->change();

            $table->integer('installed_and_displacement_count')->change();
        });
    }
};
