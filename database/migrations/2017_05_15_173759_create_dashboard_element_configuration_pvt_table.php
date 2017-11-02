<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDashboardElementConfigurationPvtTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dashboard_element_configuration_pvt', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dashboard_element_id')->unsigned();
            $table->integer('dashboard_element_configuration_id')->unsigned();
            $table->text('default_value')->nullable();
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
        Schema::dropIfExists('dashboard_element_configuration_pvt');
    }
}
