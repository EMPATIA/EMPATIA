<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigurationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuration_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('created_by');
            $table->string('updated_by');
            $table->timestamps();
            $table->softDeletes();
        });

        $configurationTypes = array(
            array('id' => '1',	'code' => 'general_configuration',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => Carbon::now()),
            array('id' => '2',	'code' => 'permissions',				'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => Carbon::now()),
            array('id' => '4',	'code' => 'security',					'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '5',	'code' => 'topic_options',				'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '6',	'code' => 'topic_comments',				'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '7',	'code' => 'general_configurations',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '8',	'code' => 'notifications',				'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '9',	'code' => 'notifications_owners',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '10',	'code' => 'notifications_topic',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '11',	'code' => 'notification_deadline',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('configuration_types')->insert($configurationTypes);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('configuration_types');
    }
}
