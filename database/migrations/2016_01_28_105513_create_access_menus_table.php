<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccessMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_menus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entity_id');
            $table->integer('site_id');
            $table->string('name');
            $table->string('description');
            $table->boolean('active');
            $table->string('created_by');
            $table->string('updated_by');
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
        Schema::drop('access_menus');
    }
}
