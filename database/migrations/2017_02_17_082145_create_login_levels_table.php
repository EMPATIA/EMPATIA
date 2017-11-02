<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoginLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('login_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('login_level_key')->unique();
            $table->unsignedInteger('entity_id');
            $table->boolean('manual_verification')->default(0);
            $table->boolean('sms_verification')->default(0);
            $table->string('name');
            $table->string('created_by');
            $table->string('updated_by');

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
        Schema::dropIfExists('login_levels');
    }
}
