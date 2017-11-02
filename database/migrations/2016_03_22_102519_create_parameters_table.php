<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parameters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parameter_type_id');
            $table->integer('cb_id');
            $table->string('code');
            $table->boolean('mandatory');
            $table->boolean('visible');
            $table->boolean('visible_in_list');
			$table->boolean('private')->nullable()->default(0);
            $table->string('value')->nullable();
            $table->string('currency')->nullable();
            $table->integer('position')->unsigned();
			$table->boolean('use_filter')->default(false);

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
        Schema::drop('parameters');
    }
}
