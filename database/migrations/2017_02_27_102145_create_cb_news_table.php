<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCbNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cb_news', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cb_id');
            $table->string('news_key');
			$table->string('tag')->nullable();
            $table->string('created_by');
            $table->string('updated_by');

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
        Schema::dropIfExists('cb_news');
    }
}
