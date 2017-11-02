<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLevelParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('level_parameters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('level_parameter_key')->unique();
            $table->unsignedInteger('site_id');
            $table->string('name');
            $table->unsignedInteger('position');
            $table->boolean('mandatory')->default(0);
			$table->boolean('show_in_registration');
			$table->boolean('sms_verification');
            $table->boolean('manual_verification')->default(0);

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
        Schema::dropIfExists('level_parameters');
    }
}
