<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteEthicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_ethics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('site_ethic_key');
            $table->unsignedInteger('site_id');
            $table->unsignedInteger('site_ethic_type_id');
            $table->integer('version')->default(1);
            $table->boolean('active')->default(0);
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
        Schema::dropIfExists('site_ethics');
    }
}
