<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBEMenuElementParameterRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('be_menu_element_parameter_relation', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('position');
            $table->string('code')->nullable();

            $table->integer('be_menu_element_id')->unsigned();
            $table->integer('be_menu_parameter_id')->unsigned();

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
        Schema::dropIfExists('be_menu_element_parameter_relation');
    }
}
