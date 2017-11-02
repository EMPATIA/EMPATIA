<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBEEntityMenuElementParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('be_entity_menu_element_parameters', function (Blueprint $table) {
            $table->increments('id');

            $table->text('value');

            $table->integer('be_entity_menu_element_id');
            $table->integer('be_menu_element_parameter_id');

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
        Schema::dropIfExists('be_entity_menu_element_parameters');
    }
}
