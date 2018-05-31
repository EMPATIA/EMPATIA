<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropAllPermissionUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('all_permission_users');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('all_permission_users', function (Blueprint $table) {
            $table->integer('user_id')->index();
            $table->string('all_permission_code')->index();

            $table->primary(['user_id', 'all_permission_code']);
        });
    }
}
