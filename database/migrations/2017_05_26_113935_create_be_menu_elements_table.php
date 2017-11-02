<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBEMenuElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('be_menu_elements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->unique();

            $table->string('code')->unique()->nullable();
            $table->string('module_code')->nullable();
            $table->string('module_type_code')->nullable();
            $table->string('permission')->nullable();
            $table->string('controller')->nullable();
            $table->string('method')->nullable();

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
        Schema::dropIfExists('be_menu_elements');
    }
}
