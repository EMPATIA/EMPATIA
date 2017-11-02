<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParameterTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parameter_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parameter_id')->unsigned();
            $table->string('language_code');
            $table->string('parameter');
            $table->string('description')->nullable();
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
        Schema::drop('parameter_translations');
    }
}
