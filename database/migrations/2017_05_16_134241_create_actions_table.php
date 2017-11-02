<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('created_by');
            $table->string('updated_by');
            $table->timestamps();
            $table->softDeletes();
        });

        $action = array(
            array('id' => '1',	'code' => 'create_topic',			'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '2',	'code' => 'comment',				'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '3',	'code' => 'create_vote_like',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '4',	'code' => 'create_vote_multi',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '5',	'code' => 'create_vote_negative',	'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '6',	'code' => 'vote_event',				'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null)
        );
        DB::table('actions')->insert($action);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actions');
    }
}
