<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->unsignedTinyInteger("status")->default(1);
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('evaluated_expert_positions_count');
            $table->unsignedTinyInteger('occupied')->default(1);
            $table->unsignedSmallInteger('old_permanent_experts_count');
            $table->unsignedSmallInteger('old_contracting_experts_count');
            $table->unsignedSmallInteger('old_below_expert_count');
            $table->unsignedBigInteger('architecture_id');
            $table->unsignedBigInteger('directorate_id')->nullable();
            $table->unsignedBigInteger("user_id");
            $table->foreign('architecture_id')->references('id')->on('architectures')->onDelete('cascade');
            $table->foreign('directorate_id')->references('id')->on('directorates')->onDelete('cascade');
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
