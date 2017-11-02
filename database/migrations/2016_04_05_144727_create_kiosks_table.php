<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKiosksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kiosks', function (Blueprint $table) {
            $table->increments('id');        
            $table->string('kiosk_key');
			$table->integer('entity_id')->unsigned();
            $table->string('entity_cb_id');
            $table->string('event_key')->nullable();                         
            $table->integer('kiosk_type_id'); 
            $table->string('title');                
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
        Schema::drop('kiosks');
    }
}
