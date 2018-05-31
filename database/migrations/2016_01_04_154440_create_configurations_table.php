<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->integer('configuration_type_id')->unsigned();
            $table->string('created_by');              
            $table->string('updated_by');   
            $table->timestamps();
            $table->softDeletes();
        });
                     
        Schema::create('cb_configurations', function (Blueprint $table) {
            $table->integer('configuration_id');
            $table->integer('cb_id');     
            $table->string('value')->nullable();
            $table->string('created_by');              
            $table->timestamps();
            $keys = array('configuration_id','cb_id');
            $table->primary($keys);               
        });
               
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {       
        Schema::drop('configurations');
        Schema::drop('cb_configurations');
    }
}
