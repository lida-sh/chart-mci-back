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
        Schema::create('region_facility_pivots', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger("facility_id");
            $table->unsignedSmallInteger("facility_count");
            $table->unsignedSmallInteger("province_id");
            $table->foreign("province_id")->references("id")->on("provinces")->onDelete("cascade");
            $table->foreign("facility_id")->references("id")->on("facilities")->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('region_facility_pivots');
    }
};
