<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParameterFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parameter_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parameter_add_field_id')->unsigned();
            $table->integer('parameter_id')->unsigned();
            $table->string('value')->nullable;
            $table->string('code');

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
        Schema::dropIfExists('parameter_fields');
    }
}
