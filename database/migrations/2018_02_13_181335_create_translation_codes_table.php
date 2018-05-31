<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranslationCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translation_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cb_id')->nullable();
            $table->unsignedInteger('site_id')->nullable();
            $table->string('code');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(array('cb_id', 'code'));
            $table->unique(array('site_id', 'code'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('translation_codes');
    }
}
