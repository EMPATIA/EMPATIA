<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParamAddFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('param_add_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('field_type_id')->unsigned();
			$table->integer('parameter_type_id');
            $table->string('code');
            $table->string('description')->nullable();
            $table->string('value');

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
        Schema::dropIfExists('param_add_fields');
    }
}
