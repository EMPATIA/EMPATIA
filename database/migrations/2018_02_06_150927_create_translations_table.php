<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cb_id')->nullable();
            $table->unsignedInteger('site_id')->nullable();
            $table->string('code');
            $table->string('language_code')->nullable();
            $table->string('translation')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(array('cb_id', 'code', 'language_code'));
            $table->unique(array('site_id', 'code', 'language_code'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('translations');
    }
}
