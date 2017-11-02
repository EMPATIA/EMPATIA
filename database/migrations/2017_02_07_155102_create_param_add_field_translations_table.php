<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParamAddFieldTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('param_add_field_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('param_add_field_id')->unsigned();
            $table->string('language_code');
            $table->string('name');
            $table->string('description')->nullable();

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
        Schema::dropIfExists('param_add_field_translations');
    }
}
