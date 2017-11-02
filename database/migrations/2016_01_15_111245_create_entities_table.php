<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('entity_key')->unique();
            $table->integer('country_id')->unsigned();
            $table->integer('timezone_id')->unsigned();
            $table->integer('currency_id')->unsigned();
            $table->string('name');
            $table->string('designation');
            $table->string('description');
            $table->string('url');
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
        Schema::drop('entities');
    }
}
