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
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->nullable();
            $table->json('title');
            $table->json('link')->nullable();
            $table->string('menu_type_code');
            $table->integer('parent_id')->unsigned()->default(0);
            $table->integer('position')->unsigned()->default(0);
            $table->json('options')->nullable();
            $table->json('roles')->nullable();

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
        Schema::dropIfExists('menus');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
