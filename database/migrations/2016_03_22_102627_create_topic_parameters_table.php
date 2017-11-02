<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_parameters', function (Blueprint $table) {
            $table->integer('topic_id')->unsigned();
            $table->integer('parameter_id')->unsigned();
			$table->unsignedInteger('version')->default(1);
			$table->longText('value');

            $table->timestamps();
            $table->primary(['topic_id', 'parameter_id', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('topic_parameters');
    }
}
