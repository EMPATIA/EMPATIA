<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDashboardElementConfigurationTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dashboard_element_configuration_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dashboard_element_configuration_id');
            $table->text('title');
            $table->text('description')->nullable();
            $table->string('language_code');
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
        Schema::dropIfExists('dashboard_element_configuration_translations');
    }
}
