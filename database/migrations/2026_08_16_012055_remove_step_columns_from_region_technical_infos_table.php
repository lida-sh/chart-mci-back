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
            $table->dropColumn([
                'tower_count_step',
                'provisioned_lines_count_step',
            ]);
            $table->unsignedInteger("province_id");
            $table->foreign("province_id")->references("id")->on("provinces")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('region_technical_infos', function (Blueprint $table) {
            $table->unsignedSmallInteger('provisioned_lines_count_step');
            $table->unsignedSmallInteger('tower_count_step');
        });
    }
};
