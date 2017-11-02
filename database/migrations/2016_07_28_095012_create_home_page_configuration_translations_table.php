<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHomePageConfigurationTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('home_page_configuration_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('language_code');
            $table->integer('home_page_configuration_id')->unsigned();
            $table->text('value');
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
        Schema::drop('home_page_configuration_translations');
    }
}
