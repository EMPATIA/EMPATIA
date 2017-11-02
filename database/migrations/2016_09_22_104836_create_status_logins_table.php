<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateStatusLoginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_logins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->timestamps();
            $table->softDeletes();
        });

        $statusLogins = array(
            array('id' => '1',	'code' => 'registered',	    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',	'code' => 'complete',       'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',	'code' => 'authorized',	    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('status_logins')->insert($statusLogins);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('status_logins');
    }
}
