<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntityPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('entity_permission_key')->unique();
            $table->integer('entity_group_id')->unsigned();
            $table->integer('entity_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('module_id')->unsigned();
            $table->integer('module_type_id')->unsigned();
            $table->boolean('permission_show');
            $table->boolean('permission_create');
            $table->boolean('permission_update');
            $table->boolean('permission_delete');
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
        Schema::drop('entity_permissions');
    }
}
