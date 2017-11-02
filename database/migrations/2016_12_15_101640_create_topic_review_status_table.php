<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicReviewStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_review_status', function (Blueprint $table) {
            $table->increments('id');
            $table->string('topic_review_status_key')->unique();
            $table->integer('topic_review_status_type_id')->unsigned();
            $table->integer('topic_review_id')->unsigned();
            $table->boolean('active')->default(0);
            $table->string('created_by');
            $table->timestamps();
            $table->SoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('topic_review_status');
    }
}
