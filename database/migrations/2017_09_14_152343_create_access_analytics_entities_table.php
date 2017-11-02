<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccessAnalyticsEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_analytics_entities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('total_users');
            $table->integer('total_page_access');
            $table->integer('entity_id');
            $table->integer('access_analytics_id')->unsigned()->index();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_analytics_entities');
    }
}
