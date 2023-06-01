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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('type');
            $table->json('slug');
            $table->json('title');
            $table->string('status')->default('init');
            $table->json('sections')->nullable();
            $table->json('seo')->nullable();
            $table->json('options')->nullable();
            $table->string('tags')->nullable();;

            $table->versionable();
            $table->blamestamps();

            $table->index('type');
            $table->index('slug');
            $table->index('status');
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
        Schema::dropIfExists('contents');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
