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
        Schema::create('center_pivots', function (Blueprint $table) {
            $table->id();
            $table->string("number_pivot");
            $table->string("title");
            $table->unsignedInteger("active_access_count");
            $table->unsignedInteger("active_fttx_count");
            $table->unsignedInteger("fault_count");
            $table->unsignedInteger("installed_and_displacement_count");
            $table->unsignedInteger("access_fiber_length_km");
            $table->unsignedInteger("transport_fiber_length_km");
            $table->unsignedSmallInteger("province_id");
            $table->foreign("province_id")->references("id")->on("provinces")->onDelete("cascade");
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('center_pivots');
    }
};
