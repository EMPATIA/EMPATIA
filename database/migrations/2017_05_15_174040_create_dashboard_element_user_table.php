<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDashboardElementUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dashboard_element_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dashboard_element_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('entity_id')->unsigned();
            $table->integer('position')->nullable();
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
        Schema::dropIfExists('dashboard_element_user');
    }
}
