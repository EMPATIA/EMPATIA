<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeographicAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geographic_areas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('geo_key')->unique();
            $table->integer('entity_id');
            $table->string('name');
            $table->string('created_by');
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
        Schema::drop('geographic_areas');
    }
}
