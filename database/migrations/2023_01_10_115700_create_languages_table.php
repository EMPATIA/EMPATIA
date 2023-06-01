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
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 6)->unique();
            $table->string('name', 60)->unique();
            $table->boolean('default')->default(false);
            $table->boolean('backend')->default(false);
            $table->boolean('frontend')->default(false);
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
        Schema::dropIfExists('languages');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
