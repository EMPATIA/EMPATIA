<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionEntityGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permission_entity_groups', function (Blueprint $table) {
            $table->integer('entity_group_id')->index();
            $table->string('all_permission_code')->index();

            $table->primary(['entity_group_id', 'all_permission_code'], 'pk_permission_entity_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permission_entity_groups');
    }
}
