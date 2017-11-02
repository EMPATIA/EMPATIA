<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailTextTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_text_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('text_email_id')->unsigned()->index();
            $table->string('language_code')->index();
            $table->string('subject');
            $table->longText('body');
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
        Schema::drop('email_text_translations');
    }
}


