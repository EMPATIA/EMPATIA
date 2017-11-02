<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentTypeTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_type_types', function (Blueprint $table) {
            $table->increments('id');
			$table->string('content_type_type_key')->unique();
            $table->unsignedInteger('content_type_id');
            $table->string('code');
			$table->string('entity_key');
			$table->string('color')->nullable();
			$table->string('text_color')->nullable();
			$table->text('file')->nullable();
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
        Schema::drop('content_type_types');
    }
}
