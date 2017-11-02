<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->integer('configuration_type_id')->unsigned();
            $table->string('created_by');              
            $table->string('updated_by');   
            $table->timestamps();
            $table->softDeletes();
        });
                     
        Schema::create('cb_configurations', function (Blueprint $table) {
            $table->integer('configuration_id');
            $table->integer('cb_id');     
            $table->string('value')->nullable();
            $table->string('created_by');              
            $table->timestamps();
            $keys = array('configuration_id','cb_id');
            $table->primary($keys);               
        });
        
        $configurations = array(
            array('id' => '1',	'code' => 'allow_likes',								'configuration_type_id' => '1',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => Carbon::now()),
            array('id' => '2',	'code' => 'allow_moderation',							'configuration_type_id' => '1',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '3',	'code' => 'allow_comments',								'configuration_type_id' => '1',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '4',	'code' => 'allow_report_abuse',							'configuration_type_id' => '1',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '5',	'code' => 'allow_users_to_be_moderators',				'configuration_type_id' => '1',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '6',	'code' => 'read',										'configuration_type_id' => '2',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '7',	'code' => 'write',										'configuration_type_id' => '2',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '8',	'code' => 'security_public_access',						'configuration_type_id' => '4',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '9',	'code' => 'security_anonymous_comments',				'configuration_type_id' => '4',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '10',	'code' => 'security_create_topics',						'configuration_type_id' => '4',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '11',	'code' => 'security_create_topics_anonymous',			'configuration_type_id' => '4',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '12',	'code' => 'security_comment_authorization',				'configuration_type_id' => '4',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '13',	'code' => 'security_allow_report_abuses',				'configuration_type_id' => '4',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '14',	'code' => 'topic_options_allow_files',					'configuration_type_id' => '5',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '15',	'code' => 'topic_options_allow_pictures',				'configuration_type_id' => '5',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '16',	'code' => 'topic_options_allow_co_op',					'configuration_type_id' => '5',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '17',	'code' => 'topic_options_allow_video_link',				'configuration_type_id' => '5',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '18',	'code' => 'topic_options_allow_share',					'configuration_type_id' => '5',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '19',	'code' => 'topic_options_allow_user_count',				'configuration_type_id' => '5',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '20',	'code' => 'topic_options_allow_follow',					'configuration_type_id' => '5',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '21',	'code' => 'topic_comments_allow_comments',				'configuration_type_id' => '6',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '22',	'code' => 'topic_need_moderation',						'configuration_type_id' => '5',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '23',	'code' => 'topic_comments_normal',						'configuration_type_id' => '6',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '24',	'code' => 'topic_comments_positive_negative',			'configuration_type_id' => '6',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '25',	'code' => 'topic_comments_positive_neutral_negative',	'configuration_type_id' => '6',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '26',	'code' => 'only_one_topic',								'configuration_type_id' => '4',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '27',	'code' => 'topic_as_public_questionnaire',				'configuration_type_id' => '5',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '28',	'code' => 'topic_as_private_questionnaire',				'configuration_type_id' => '5',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '29',	'code' => 'allow_alliance',								'configuration_type_id' => '5',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '30',	'code' => 'tab_random',									'configuration_type_id' => '7',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '31',	'code' => 'tab_recent',									'configuration_type_id' => '7',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '32',	'code' => 'tab_popular',								'configuration_type_id' => '7',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '33',	'code' => 'tab_comments',								'configuration_type_id' => '7',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '34',	'code' => 'security_anonymous_create_topic_access',		'configuration_type_id' => '4',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '35',	'code' => 'notification_content_change',				'configuration_type_id' => '8',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '36',	'code' => 'notification_new_comments',					'configuration_type_id' => '8',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '37',	'code' => 'notification_status_change',					'configuration_type_id' => '8',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '38',	'code' => 'notification_delete',						'configuration_type_id' => '8',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '39',	'code' => 'notification_create_topic',					'configuration_type_id' => '10',	'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '40',	'code' => 'notification_delete_topic',					'configuration_type_id' => '10',	'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '41',	'code' => 'notification_edit_topic',					'configuration_type_id' => '10',	'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '42',	'code' => 'notification_topic_status_change',			'configuration_type_id' => '10',	'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '43',	'code' => 'notification_owner_create_topic',			'configuration_type_id' => '9',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '44',	'code' => 'notification_owner_delete_topic',			'configuration_type_id' => '9',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '45',	'code' => 'notification_owner_edit_topic',				'configuration_type_id' => '9',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '46',	'code' => 'notification_owner_new_comments',			'configuration_type_id' => '9',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '47',	'code' => 'notification_owner_change_status',			'configuration_type_id' => '9',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '48',	'code' => 'disable_comments_functionality',				'configuration_type_id' => '6',		'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '49',	'code' => 'notification_deadline',						'configuration_type_id' => '11',	'created_by' => 'defaultUSERprojectEMPATIA2016JAN',	'updated_by' => '',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL)
        );
        DB::table('configurations')->insert($configurations);           
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {       
        Schema::drop('configurations');
        Schema::drop('cb_configurations');
    }
}
