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
        Schema::create('region_organizational_unit_titles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger("region_organizational_unit_type_id");
            $table->foreign(
                'region_organizational_unit_type_id',
                'rout_title_type_fk'
            )
                ->references('id')
                ->on('region_organizational_unit_types')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->unique(
                ['region_organizational_unit_type_id', 'title'],
                'org_unit_title_type_unique'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('region_organizational_unit_titles');
    }
};
