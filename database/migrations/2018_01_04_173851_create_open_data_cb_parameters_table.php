<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOpenDataCbParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('open_data_cb_parameters', function (Blueprint $table) {
            $table->increments('id');
            
            $table->unsignedInteger("parameter_id");
            $table->unsignedInteger("open_data_entity_id");
            
            $table->string('created_by');

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
        Schema::dropIfExists('open_data_cb_parameters');
    }
}
