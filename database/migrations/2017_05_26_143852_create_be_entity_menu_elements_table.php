<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBEEntityMenuElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('be_entity_menu_elements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('menu_key');

            $table->integer("position");


            $table->integer('be_menu_element_id');
            $table->integer('be_entity_menu_id');
            $table->integer('parent_id')->unsigned()->nullable();

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
        Schema::dropIfExists('be_entity_menu_elements');
    }
}
