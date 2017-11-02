<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTextTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('text_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('text_id')->unsigned()->index();
            $table->string('language_code')->index();
            $table->string('title');
            $table->longText('content');
            $table->string('tag');
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by');
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
        Schema::drop('text_translations');
    }
}