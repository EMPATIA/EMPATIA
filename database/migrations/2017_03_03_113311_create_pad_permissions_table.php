<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePadPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pad_permissions', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('cb_id')->unsigned();
            $table->string('group_key')->nullable();
            $table->string('user_key')->nullable();
            $table->integer('permission_show');
            $table->integer('permission_create');
            $table->integer('permission_update');
            $table->integer('permission_delete');
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
        Schema::dropIfExists('pad_permissions');
    }
}
