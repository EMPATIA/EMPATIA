<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key');
            $table->string('cm_key');
            $table->string('entity_id');
			$table->string('layout_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('link');
            $table->boolean('partial_link');
            $table->boolean('active');
            $table->string('no_reply_email');
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            
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
        Schema::drop('sites');
    }
}
