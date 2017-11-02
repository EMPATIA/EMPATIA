<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigurationOptionsTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuration_options_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('configuration_option_id')->unsigned();
            $table->string('language_code');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('value');

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
        Schema::drop('configuration_options_translations');
    }
}
