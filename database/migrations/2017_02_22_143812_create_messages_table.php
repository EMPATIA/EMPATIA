<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('message_key')->unique();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('entity_id');
            $table->text('value');
            $table->string('to')->nullable();
            $table->string('from')->nullable();
            $table->boolean('viewed')->default(0);
            $table->dateTime('viewed_at')->nullable();
            $table->string('viewed_by')->nullable();

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
        Schema::dropIfExists('messages');
    }
}
