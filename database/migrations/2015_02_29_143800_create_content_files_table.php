<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_files', function (Blueprint $table) {
            $table->increments('id');            
            $table->integer('file_id')->unsigned();            
            $table->integer('content_id')->unsigned();                 
            $table->integer('type_id')->unsigned();                                    
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->integer('position')->unsigned();
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
        Schema::drop('content_files');
    }
}
