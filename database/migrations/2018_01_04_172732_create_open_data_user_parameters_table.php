<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOpenDataUserParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('open_data_user_parameters', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string("parameter_user_type_key");
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
        Schema::dropIfExists('open_data_user_parameters');
    }
}
