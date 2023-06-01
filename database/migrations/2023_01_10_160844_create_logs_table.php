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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->dateTime('date');
            $table->string('severity');
            $table->integer('user_id')->nullable();
            $table->string('action');
            $table->boolean('result');
            $table->longText('context');
            $table->json('details')->nullable();
            $table->string('facility')->nullable();
            $table->string('ip');
            $table->text('url');
            $table->string('method');
            $table->string('session_id');
            $table->text('user_agent')->nullable();

            $table->index('code');
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
        Schema::dropIfExists('logs');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
