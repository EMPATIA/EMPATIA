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


        $authMethods = array(
            array('id' => '1',	'auth_method_key' => 'KEb2ubskrl8EYHT5UD0ADBuL7iZuOpdG',	'name' => 'Empatia Authentication Method',	'description' => 'Authentication Method of Empatia',	'code' => 'auth_empatia',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',	'auth_method_key' => 'KSYBafkbC6jmi44OPwp9vD1gu7rlYciD',	'name' => 'Facebook Login',					'description' => 'Login with Facebook',					'code' => 'facebook',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('auth_methods')->insert($authMethods);
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
