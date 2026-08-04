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
        Schema::create('region_technical_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedFloat("region_coefficient")->default(1);
            $table->unsignedInteger("provisioned_lines_count");
            $table->unsignedSmallInteger("provisioned_lines_count_step");
            $table->unsignedSmallInteger("tower_count");
            $table->unsignedSmallInteger("tower_count_step");
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('region_technical_infos');
    }
};
