<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateCbVoteConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cb_vote_configurations', function (Blueprint $table) {
            $table->integer('cb_vote_id');
            $table->integer('vote_configuration_id');
            $table->string('value')->nullable();
            $table->timestamps();

            $keys = array('cb_vote_id','vote_configuration_id');
            $table->primary($keys);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cb_vote_configurations');
    }
}
