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
        Schema::create('region_organizational_units', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            // $table->enum('type', [
            //     'deputy',      // معاونت
            //     'management',  // مدیریت
            //     'department',  // اداره
            //     'unit', // واحد
            //     'telecom_center', //مرکز مخابراتی
            //     'provisioned_lines', //واگذاری خطوط
            //     'MDF_maintenance', //نگهداری MDF,
            //     'agent',
            //     'کارگزار'
            // ]);
            $table->boolean('is_active')->default(true);
            $table->text("description")->nullable();
            $table->unsignedInteger("province_id");
            $table->unsignedInteger("city_id")->nullable();
            $table->unsignedBigInteger("parent_id")->nullable();
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("region_organizational_unit_type_id");
            $table->foreign("region_organizational_unit_type_id", 'region_org_unit_type_fk')->references("id")->on("region_organizational_unit_types")->onDelete("cascade");
            $table->foreign("province_id")->references("id")->on("provinces")->onDelete("cascade");
            $table->foreign("city_id")->references("id")->on("cities")->onDelete("cascade");
            $table->foreign("parent_id")->references("id")->on("region_organizational_units")->onDelete("cascade");
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('region_organizational_units');
    }
};
