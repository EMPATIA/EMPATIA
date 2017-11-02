<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicAlliancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("topic_alliances",function (Blueprint $table) {
            $table->increments("id");
            $table->string("ally_key")->unique();

            $table->text("request_message");
            $table->text("response_message")->nullable();
            $table->boolean("accepted")->nullable();

            $table->integer("origin_topic_id");
            $table->integer("destiny_topic_id");

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
        Schema::dropIfExists("topic_alliances");
    }
}
