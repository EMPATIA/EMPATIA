<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoteMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vote_methods', function (Blueprint $table) {
            $table->increments('id');
            $table->string('vote_method_id');
            $table->string('entity_id');
            $table->string('created_by');
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
        Schema::drop('vote_methods');
    }
}
