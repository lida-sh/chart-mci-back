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
        Schema::table('region_technical_infos', function (Blueprint $table) {
            $table->decimal('access_fiber_length_km', 12, 2)
                ->nullable()
                ->after('id');

            $table->decimal('transport_fiber_length_km', 12, 2)
                ->nullable()
                ->after('access_fiber_length_km');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('region_technical_infos', function (Blueprint $table) {
            Schema::table('region_technical_infos', function (Blueprint $table) {
                $table->dropColumn([
                    'access_fiber_length_km',
                    'transport_fiber_length_km',
                ]);
            });
        });
    }
};
