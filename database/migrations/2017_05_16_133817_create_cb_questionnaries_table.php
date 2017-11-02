<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCbQuestionnariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cb_questionnaries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cb_questionnarie_key');
            $table->integer('cb_id');
            $table->string('questionnarie_key');
            $table->integer('action_id');
            $table->boolean('notify_email');
            $table->boolean('ignore');
            $table->integer('days_to_ignore');
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
        Schema::dropIfExists('cb_questionnaries');
    }
}
