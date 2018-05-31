<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perm_users', function (Blueprint $table) {
            $table->string('code')->index();
            $table->integer('user_id')->index();
            $table->integer('entity_id')->index();
            $table->integer('cb_id')->index();

            $table->primary(['code', 'user_id','entity_id','cb_id']);

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
        Schema::dropIfExists('perm_users');
    }
}
