<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParameterUserTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parameter_user_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('parameter_user_type_key')->unique();
            $table->integer('parameter_type_id')->unsigned();
            $table->integer('entity_id')->unsigned();
            $table->boolean('mandatory')->default('0');
			$table->boolean('parameter_unique')->default(0);
            $table->unsignedInteger('level_parameter_id')->nullable();

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
        Schema::drop('parameter_user_types');
    }
}
