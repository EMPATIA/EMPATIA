<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCbQuestionnarieTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cb_questionnarie_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cb_questionnarie_id');
            $table->string('language_code');
            $table->text('content');
            $table->string('accept');
            $table->string('ignore');
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
        Schema::dropIfExists('cb_questionnarie_translations');
    }
}
