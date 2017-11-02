<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntityLanguageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_language', function (Blueprint $table) {
            $table->string('language_id')->index();
            $table->string('entity_id')->index();
            $table->boolean('default');
            $table->timestamps();

            $table->primary(array('language_id', 'entity_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('entity_language');
    }
}
