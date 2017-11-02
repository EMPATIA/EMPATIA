<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicQuestionaryReplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_questionary_reply', function (Blueprint $table) {
            $table->string('topic_key');
            $table->string('form_reply_key');
            $table->timestamps();

            $table->primary(array('topic_key', 'form_reply_key'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('topic_questionary_reply');
    }
}
