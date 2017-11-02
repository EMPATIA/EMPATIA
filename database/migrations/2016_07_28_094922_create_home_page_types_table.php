<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateHomePageTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('home_page_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('home_page_type_key')->unique();
            $table->integer('entity_id')->unsigned();
            $table->string('name');
            $table->string('code');
			$table->integer('parent_id')->unsigned()->nullable();
			$table->string('type_code');
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
        Schema::drop('home_page_types');
    }
}
