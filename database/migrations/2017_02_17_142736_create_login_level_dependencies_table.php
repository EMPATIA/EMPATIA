<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoginLevelDependenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('login_level_dependencies', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('login_level_id');
            $table->unsignedInteger('dependency_login_level_id');
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
        Schema::dropIfExists('login_level_dependencies');
    }
}
