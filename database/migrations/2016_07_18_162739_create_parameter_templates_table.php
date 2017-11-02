<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParameterTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parameter_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('parameter_template_key');
            $table->integer('parameter_type_id');
            $table->string('parameter');
            $table->string('description')->nullable();
            $table->string('code');
            $table->boolean('mandatory');
			$table->boolean('visible');
			$table->boolean('visible_in_list');
            $table->string('value')->nullable();
            $table->string('currency')->nullable();
            $table->integer('position')->unsigned();

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
        Schema::drop('parameter_templates');
    }
}
