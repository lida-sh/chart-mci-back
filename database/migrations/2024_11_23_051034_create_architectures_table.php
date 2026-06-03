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
            $table->tinyInteger("status")->default(1);
            $table->text('description')->nullable();
            $table->smallInteger('occupied_expert_positions_count');
            $table->smallInteger('old_occupied_expert_positions_count');
            $table->smallInteger('old_positions_count');
            $table->smallInteger('old_processes_count');
            $table->smallInteger('old_subProcesses_count');
            $table->enum('type',['administration','assistance']);
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
