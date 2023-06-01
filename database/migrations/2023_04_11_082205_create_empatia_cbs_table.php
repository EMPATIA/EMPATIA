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
        Schema::create('empatia_cbs', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->json('title');
            $table->json('content')->nullable();
            $table->json('slug');
            $table->string('type');
            $table->string('template')->nullable();
            $table->datetime('start_date');
            $table->datetime('end_date')->nullable();
            $table->json('parameters')->nullable();
            $table->json('data')->nullable();

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
        Schema::dropIfExists('empatia_cbs');
    }
};
