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
        Schema::create('region_organizational_unit_positions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("region_position_id");
            $table->foreign("region_position_id")->references("id")->on("region_positions")->onDelete("cascade");
            $table->unsignedBigInteger("region_organizational_unit_id");
            $table->foreign("region_organizational_unit_id", 'region_org_unit_fk')->references("id")->on("region_organizational_units")->onDelete("cascade");
            $table->unsignedInteger('approved_count')->default(1);
            $table->unique(
                ['region_organizational_unit_id', 'region_position_id'],
                'region_org_unit_position_unique'
            );
            $table->unsignedBigInteger("user_id");
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('region_organizational_unit_positions');
    }
};
