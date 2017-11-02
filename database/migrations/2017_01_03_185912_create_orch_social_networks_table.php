<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrchSocialNetworksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orch_social_networks', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('site_id')->unsigned();
            $table->string('social_network_key');
            $table->string('code');
            $table->string('app_secret');
            $table->string('app_id');


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
        Schema::dropIfExists('orch_social_networks');
    }
}
