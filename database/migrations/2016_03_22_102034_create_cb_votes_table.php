<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCbVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cb_votes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cb_id');
            $table->string('vote_key')->unique();
            $table->string('created_by');
            $table->string('vote_method');
            $table->string('name');
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
        Schema::drop('cb_votes');
    }
}
