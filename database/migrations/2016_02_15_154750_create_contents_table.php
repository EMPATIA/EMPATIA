<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('content_key')->unique();
            $table->integer('type_id');  
			$table->unsignedInteger('content_type_type_id')->nullable();			
            $table->tinyInteger('fixed')->default(0)->index();
            $table->tinyInteger('clean')->default(0)->index();
            $table->boolean('published')->default(false);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('publish_date')->nullable();         
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
        Schema::drop('contents');
    }
}
