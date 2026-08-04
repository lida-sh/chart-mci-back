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
        Schema::create('region_outsourced_activity_pivots', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger("outsourced_activity_id");
            $table->unsignedInteger("outsourced_activity_count");
            $table->unsignedSmallInteger("province_id");
            $table->foreign("province_id")->references("id")->on("provinces")->onDelete("cascade");
            $table->foreign("outsourced_activity_id")->references("id")->on("outsourced_activities")->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('region_outsourced_activity_pivots');
    }
};
