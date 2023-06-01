<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('phone_number');
            $table->string('template')->nullable();
            $table->text('content');
            $table->boolean('sent')->default(false);
            $table->dateTime('sent_at')->nullable();
            $table->string('message_id')->nullable();
            $table->json('data')->nullable();

            $table->blamestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('sms');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
