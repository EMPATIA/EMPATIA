<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCooperatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cooperators', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('topic_id');
            $table->string('user_key');
            $table->tinyinteger('type_id');
            $table->string('created_by');
            $table->string('updated_by');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(array('topic_id', 'user_key'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cooperators');
    }
}
