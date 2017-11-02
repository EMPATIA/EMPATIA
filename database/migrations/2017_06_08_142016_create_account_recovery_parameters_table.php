<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountRecoveryParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_recovery_parameters', function (Blueprint $table) {
            $table->increments('id');

            $table->string('table_key')->unique();

            $table->boolean("send_token")->default(0);

            $table->string('entity_key');
            $table->string('parameter_user_type_key');

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
        Schema::dropIfExists('account_recovery_parameters');
    }
}
