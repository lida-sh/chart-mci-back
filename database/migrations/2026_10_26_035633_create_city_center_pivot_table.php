<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('city_center_pivot', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('city_id');
            $table->unsignedBigInteger('center_pivot_id');
            
            $table->timestamps();
            $table->unique(['city_id', 'center_pivot_id']);

            $table->foreign('city_id')
                ->references('id')
                ->on('cities')
                ->onDelete('cascade');

            $table->foreign('center_pivot_id')
                ->references('id')
                ->on('center_pivots')
                ->onDelete('cascade');
            
        });
        $cities = DB::table('cities')
            ->whereNotNull('center_pivot_id')
            ->get(['id', 'center_pivot_id']);

        foreach ($cities as $city) {
            DB::table('city_center_pivot')->insert([
                'city_id' => $city->id,
                'center_pivot_id' => $city->center_pivot_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        Schema::table('cities', function (Blueprint $table) {
            $table->dropForeign(['center_pivot_id']);
            $table->dropColumn('center_pivot_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->unsignedBigInteger('center_pivot_id')
                ->nullable();

            $table->foreign('center_pivot_id')
                ->references('id')
                ->on('center_pivots')
                ->onDelete('cascade');
        });
        $relations = DB::table('city_center_pivot')
            ->orderBy('id')
            ->get(['city_id', 'center_pivot_id']);

        foreach ($relations as $relation) {
            DB::table('cities')
                ->where('id', $relation->city_id)
                ->whereNull('center_pivot_id')
                ->update([
                    'center_pivot_id' => $relation->center_pivot_id,
                ]);
        }

        Schema::dropIfExists('city_center_pivot');
    }
};
