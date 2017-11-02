<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParameterTemplateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parameter_template_options', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parameter_template_id');
            $table->string('label');

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
        Schema::drop('parameter_template_options');
    }
}
