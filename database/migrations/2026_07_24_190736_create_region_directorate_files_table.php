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
        Schema::create('region_directorate_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("region_directorate_id");
            $table->foreign("region_directorate_id")->references("id")->on("region_directorates")->onDelete("cascade");
            $table->string("fileName");
            $table->string("filePath");
            $table->unsignedTinyInteger("status")->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('region_directorate_files');
    }
};
