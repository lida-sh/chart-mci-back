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
        Schema::table('region_organizational_unit_titles', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->foreign('user_id', 'rout_titles_user_id_fk')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();
        });
        DB::table('region_organizational_unit_titles')
            ->update(['user_id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('region_organizational_unit_titles', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
