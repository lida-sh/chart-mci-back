<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_processes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->tinyInteger("status")->default(1);
            $table->text('description')->nullable();
            $table->smallInteger('positions_count');
            $table->tinyInteger('occupied')->default(1);
            $table->smallInteger('occupied_positions_count');
            $table->smallInteger('conversion_positions_count');
            $table->smallInteger('contracting_positions_count');
            $table->smallInteger('below_expert_positions_count');
            $table->unsignedBigInteger('architecture_id');
            $table->unsignedBigInteger('process_id');
            $table->unsignedBigInteger("user_id");
            $table->foreign('architecture_id')->references('id')->on('architectures')->onDelete('cascade');
            $table->foreign('process_id')->references('id')->on('processes')->onDelete('cascade');
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
        Schema::dropIfExists('sub_processes');
    }
}
