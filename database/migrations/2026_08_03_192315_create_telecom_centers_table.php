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
        Schema::create('telecom_centers', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->unsignedInteger("in_service_count");
            $table->unsignedTinyInteger("capacity");
            $table->unsignedBigInteger("center_pivot_id");
            $table->foreign("center_pivot_id")->references("id")->on("center_pivots")->onDelete("cascade");
            $table->unsignedBigInteger("user_id");
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telecom_centers');
    }
};
