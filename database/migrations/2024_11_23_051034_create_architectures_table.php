<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchitecturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('architectures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->unsignedTinyInteger("status")->default(1);
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('office_manager_count');
            $table->unsignedSmallInteger('old_positions_count');
            $table->unsignedSmallInteger('old_expert_positions_count');
            $table->unsignedSmallInteger('old_directorates_count');
            $table->unsignedSmallInteger('old_departments_count');
            $table->enum('type',['deputy','directorate']);
            $table->unsignedBigInteger("user_id");
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('architectures');
    }
}
