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
        Schema::create('login_levels', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->json('name');
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
        Schema::dropIfExists('login_levels');
    }
};
