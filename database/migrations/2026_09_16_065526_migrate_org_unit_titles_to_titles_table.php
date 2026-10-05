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
        $units = DB::table('region_organizational_units')
            ->select(
                'region_organizational_unit_type_id',
                'title'
            )
            ->whereNotNull('title')
            ->where('title', '!=', '')
            ->distinct()
            ->get();

        foreach ($units as $unit) {
            DB::table('region_organizational_unit_titles')->insertOrIgnore([
                'region_organizational_unit_type_id' => $unit->region_organizational_unit_type_id,
                'title' => trim($unit->title),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $units = DB::table('region_organizational_units')
            ->select(
                'id',
                'region_organizational_unit_type_id',
                'title'
            )
            ->whereNotNull('title')
            ->where('title', '!=', '')
            ->get();

        foreach ($units as $unit) {
            $titleId = DB::table('region_organizational_unit_titles')
                ->where(
                    'region_organizational_unit_type_id',
                    $unit->region_organizational_unit_type_id
                )
                ->where('title', trim($unit->title))
                ->value('id');

            if ($titleId) {
                DB::table('region_organizational_units')
                    ->where('id', $unit->id)
                    ->update([
                        'title_id' => $titleId,
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $units = DB::table('region_organizational_units')
            ->select('id', 'title_id')
            ->whereNotNull('title_id')
            ->get();

        foreach ($units as $unit) {
            $title = DB::table('region_organizational_unit_titles')
                ->where('id', $unit->title_id)
                ->value('title');

            if ($title !== null) {
                DB::table('region_organizational_units')
                    ->where('id', $unit->id)
                    ->update([
                        'title' => $title,
                    ]);
            }
        }
    }
};
