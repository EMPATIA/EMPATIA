<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCbQuestionnariesUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cb_questionnaries_user', function (Blueprint $table) {

            $table->unsignedInteger('cb_questionnarie_id');
            $table->unsignedInteger('user_id');
            $table->dateTime('date_ignore')->nullable();
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
        Schema::dropIfExists('cb_questionnaries_user');
    }
}
