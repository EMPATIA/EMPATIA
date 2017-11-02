<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('content_id')->unsigned()->index();
            $table->string('language_code')->index();
            $table->integer('version')->unsigned()->index();
            $table->string('title');
            $table->text('summary');
            $table->longText('content');
            $table->string('link');
            $table->tinyInteger('enabled')->default(false)->index();
            $table->string('created_by');
            $table->boolean('docs_main')->default(false)->index();
            $table->boolean('docs_side')->default(false)->index();
            $table->tinyInteger('highlight')->default(false)->index();
            $table->tinyInteger('slideshow')->default(false)->index();            
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
        Schema::drop('content_translations');
    }
}
