<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHomePageConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('home_page_configurations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('home_page_configuration_key')->unique();
            $table->integer('home_page_type_id')->unsigned();
            $table->integer('site_id')->unsigned();
			$table->string('value')->nullable();
			$table->string('group_name');
			$table->string('group_key');
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
        Schema::drop('home_page_configurations');
    }
}
