<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrchUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orch_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_key')->unique();
			$table->boolean('admin');
            $table->string('geographic_area_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        /*
         * Insert default Admin user
         */
        $users = array(
            array(
                "user_key"              => "defaultUSERprojectEMPATIA2016JAN",
                "admin"                  => "1",
                "geographic_area_id"    => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            )
        );
        DB::table('orch_users')->insert($users);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('orch_users');
    }
}
