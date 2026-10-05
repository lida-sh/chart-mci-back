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
        Schema::create('region_department_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("region_department_id");
            $table->foreign("region_department_id")->references("id")->on("region_departments")->onDelete("cascade");
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
        Schema::dropIfExists('region_department_files');
    }
};
