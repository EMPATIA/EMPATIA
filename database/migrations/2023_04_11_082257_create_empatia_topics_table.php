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
        Schema::create('empatia_topics', function (Blueprint $table) {
            $table->id();
            $table->integer('number')->nullable();
            $table->foreignId('cb_id')->unsigned()->default(0);
            $table->integer('cb_version')->nullable();
            $table->json('slug')->nullable();
            $table->json('title')->nullable();
            $table->json('content')->nullable();
            $table->json('parameters')->nullable();
            $table->json('data')->nullable();
            $table->json('proponents')->nullable();
            $table->json('status')->nullable();
            $table->integer('state')->nullable();
            $table->integer('position')->unsigned();

            $table->versionable();
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
        Schema::dropIfExists('empatia_topics');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
