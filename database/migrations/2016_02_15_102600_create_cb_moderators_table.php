<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCbModeratorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cb_moderators', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cb_id');
            $table->string('user_key');
            $table->tinyinteger('type_id');
            $table->string('created_by');
            $table->string('updated_by');
            $table->timestamps();
            $table->unique(array('cb_id', 'user_key'));            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cb_moderators');
    }
}
