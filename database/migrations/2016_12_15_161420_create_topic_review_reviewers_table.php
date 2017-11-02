<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicReviewReviewersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('topic_review_reviewers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('topic_review_id')->unsigned();
            $table->string('reviewer_key');
            $table->boolean('is_group')->default(0);
            $table->timestamps();

            $table->unique(['topic_review_id', 'reviewer_key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('topic_review_reviewers');
    }
}
