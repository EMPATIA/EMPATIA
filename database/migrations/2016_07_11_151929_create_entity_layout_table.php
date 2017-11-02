<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntityLayoutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_layout', function (Blueprint $table) {
            $table->integer('entity_id')->index();
            $table->integer('layout_id')->index();
            $table->timestamps();

            $table->primary(array('entity_id', 'layout_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('entity_layout');
    }
}
