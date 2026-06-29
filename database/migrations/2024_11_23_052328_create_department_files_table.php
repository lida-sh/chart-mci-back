<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('department_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("department_id");
            $table->foreign("department_id")->references("id")->on("departments")->onDelete("cascade");
            $table->string("fileName");
            $table->string("filePath");
            $table->unsignedTinyInteger("status")->default(1);
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
        Schema::dropIfExists('sub_process_files');
    }
}
