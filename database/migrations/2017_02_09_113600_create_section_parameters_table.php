<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSectionParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_parameters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('section_parameter_key')->unique();
            $table->unsignedInteger('section_id');
            $table->string('section_type_parameter_id');
            $table->string('code');
            $table->text('value')->nullable();

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
        Schema::dropIfExists('section_parameters');
    }
}
