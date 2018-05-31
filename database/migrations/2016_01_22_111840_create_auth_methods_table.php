<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateAuthMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_methods', function (Blueprint $table) {
            $table->increments('id');
			$table->string('auth_method_key')->unique();
            $table->string('name');
            $table->string('description');
			$table->string('code');
            $table->timestamps();

            $table->softDeletes();
        });

        Schema::create('auth_method_entity', function (Blueprint $table) {
            $table->integer('auth_method_id')->unsigned();
            $table->integer('entity_id')->unsigned();
            $table->timestamps();
            $table->primary(array('auth_method_id', 'entity_id'));

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('auth_methods');
        Schema::drop('auth_method_entity');
    }
}
