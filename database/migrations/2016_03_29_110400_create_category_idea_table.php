<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryIdeaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_idea', function (Blueprint $table) {
            $table->string('category_id')->index();
            $table->string('idea_id')->index();
            $table->timestamps();

            $table->primary(array('category_id', 'idea_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('category_idea');
    }
}
