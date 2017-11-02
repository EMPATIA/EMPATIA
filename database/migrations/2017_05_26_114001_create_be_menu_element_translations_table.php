<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBEMenuElementTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('be_menu_element_translations', function (Blueprint $table) {
            $table->increments('id');

            $table->string('language_code');
            $table->string('name');
            $table->text('description');
            
            $table->integer('be_menu_element_id')->unsigned();

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
        Schema::dropIfExists('be_menu_element_translations');
    }
}
