<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('post_id');
            $table->string('file_id');
            $table->string('file_code');
            $table->integer('type_id')->unsigned();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->integer('position')->unsigned();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('post_files');
    }
}
