<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('content_key');
            $table->string('entity_key');
            $table->unsignedInteger('content_type_id');
            $table->unsignedInteger('content_type_type_id')->nullable();
            $table->unsignedInteger('version');
            $table->string('code')->nullable();
            $table->boolean('active');
			$table->string('name')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->dateTime('publish_date')->nullable();
            $table->boolean('highlight');


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
        Schema::dropIfExists('new_contents');
    }
}
