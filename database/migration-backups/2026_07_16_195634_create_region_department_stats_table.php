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
        Schema::create('region_department_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger("status")->default(1);
            $table->unsignedTinyInteger('occupied')->default(1);
            $table->unsignedInteger("positions_count");
            $table->unsignedBigInteger("user_id");
            $table->unsignedInteger("province_id");
            $table->unsignedBigInteger('region_department_id');
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->foreign("province_id")->references("id")->on("provinces")->onDelete("cascade");
            $table->foreign('region_department_id')->references('id')->on('region_departments')->onDelete('cascade');
            $table->unique(['province_id', 'region_department_id'], 'rds_province_department_unique');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('region_department_stats');
    }
};
