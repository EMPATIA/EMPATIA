<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicReviewStatusTypesTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_review_status_type_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('language_code');
            $table->integer('topic_review_status_type_id')->unsigned();
            $table->string('name');
            $table->string('description')->nullable();

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
        Schema::drop('topic_review_status_type_translations');
    }
}
