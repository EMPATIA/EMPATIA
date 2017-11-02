<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCbTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cb_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cb_translation_key');
            $table->unsignedInteger('cb_id');
            $table->string ('code');
            $table->string('status');
            $table->string('language_code');
            $table->string('translation')->nullable();
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
        Schema::dropIfExists('cb_translations');
    }
}
