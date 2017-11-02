<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTechnicalAnalysesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('technical_analyses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('technical_analysis_key')->unique();
            $table->unsignedInteger('topic_id');

            $table->text('impact')->nullable();
            $table->text('budget')->nullable();
            $table->text('execution')->nullable();
            $table->text('sustainability')->nullable();
            $table->string('created_by');
            $table->string('updated_by');

            $table->softDeletes();
            $table->timestamps();
        });
    }



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('technical_analyses');
    }
}
