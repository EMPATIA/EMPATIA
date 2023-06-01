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
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->string('from_email')->nullable();
            $table->string('from_name')->nullable();
            $table->text('user_email');
            $table->integer('user_id')->nullable();
            $table->integer('template')->nullable();
            $table->string('subject');
            $table->text('content');
            $table->json('data')->nullable();
            $table->string('sent')->default(false);
            $table->dateTime('sent_at')->nullable();
            $table->unsignedInteger('errors')->default(0);

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
        Schema::dropIfExists('emails');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
