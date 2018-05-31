<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perm_groups', function (Blueprint $table) {
            $table->string('code')->index();
            $table->integer('entity_group_id')->index();
            $table->integer('entity_id')->index();
            $table->integer('cb_id')->index();

            $table->primary(['code', 'entity_group_id','entity_id','cb_id']);

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
        Schema::dropIfExists('perm_groups');
    }
}
