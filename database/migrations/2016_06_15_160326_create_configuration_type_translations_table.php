<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigurationTypeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuration_type_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('configuration_type_id')->unsigned();
            $table->string('language_code');
            $table->string('title');
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        $configurationTypeTranslations = array(
            array('id' => '1',	'configuration_type_id' => '1',		'language_code' => 'en',	'title' => 'General Configurations',			'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => Carbon::now()),
            array('id' => '2',	'configuration_type_id' => '2',		'language_code' => 'en',	'title' => 'Permissions',						'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => Carbon::now()),
            array('id' => '3',	'configuration_type_id' => '4',		'language_code' => 'pt',	'title' => 'Segurança',							'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '4',	'configuration_type_id' => '4',		'language_code' => 'cz',	'title' => 'Security',							'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '5',	'configuration_type_id' => '4',		'language_code' => 'it',	'title' => 'Security',							'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '6',	'configuration_type_id' => '4',		'language_code' => 'de',	'title' => 'Security',							'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '7',	'configuration_type_id' => '4',		'language_code' => 'en',	'title' => 'Security',							'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '8',	'configuration_type_id' => '5',		'language_code' => 'pt',	'title' => 'Opções dos Tópicos',				'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '9',	'configuration_type_id' => '5',		'language_code' => 'cz',	'title' => 'Topics Options',					'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '10',	'configuration_type_id' => '5',		'language_code' => 'it',	'title' => 'Topics Options',					'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '11',	'configuration_type_id' => '5',		'language_code' => 'de',	'title' => 'Topics Options',					'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '12',	'configuration_type_id' => '5',		'language_code' => 'en',	'title' => 'Topics Options',					'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '13',	'configuration_type_id' => '6',		'language_code' => 'en',	'title' => 'Topic Comments',					'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '14',	'configuration_type_id' => '6',		'language_code' => 'pt',	'title' => 'Comentários do tópico',				'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '15',	'configuration_type_id' => '7',		'language_code' => 'pt',	'title' => 'Configurações Gerais',				'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '16',	'configuration_type_id' => '7',		'language_code' => 'en',	'title' => 'General Configurations',			'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '17',	'configuration_type_id' => '8',		'language_code' => 'pt',	'title' => 'Notifications',						'description' => 'Send notifications when certain actions are performed',						'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '18',	'configuration_type_id' => '8',		'language_code' => 'cz',	'title' => 'Notifications',						'description' => 'Send notifications when certain actions are performed',						'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '19',	'configuration_type_id' => '8',		'language_code' => 'it',	'title' => 'Notifications',						'description' => 'Send notifications when certain actions are performed',						'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '20',	'configuration_type_id' => '8',		'language_code' => 'de',	'title' => 'Benachrichtigungen',				'description' => 'Senden Sie Benachrichtigungen, wenn bestimmte Aktionen durchgeführt werden',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '21',	'configuration_type_id' => '8',		'language_code' => 'en',	'title' => 'Notifications',						'description' => 'Send notifications when certain actions are performed',						'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '22',	'configuration_type_id' => '8',		'language_code' => 'fr',	'title' => 'Notifications',						'description' => 'Send notifications when certain actions are performed',						'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '23',	'configuration_type_id' => '8',		'language_code' => 'es',	'title' => 'Notifications',						'description' => 'Send notifications when certain actions are performed',						'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '24',	'configuration_type_id' => '9',		'language_code' => 'pt',	'title' => 'Owners Notifications',				'description' => 'Notifications to owner',														'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '25',	'configuration_type_id' => '9',		'language_code' => 'cz',	'title' => 'Owners Notifications',				'description' => 'Notifications to owner',														'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '26',	'configuration_type_id' => '9',		'language_code' => 'it',	'title' => 'Owners Notifications',				'description' => 'Notifications to owner',														'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '27',	'configuration_type_id' => '9',		'language_code' => 'de',	'title' => 'Owners Notifications',				'description' => 'Notifications to owner',														'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '28',	'configuration_type_id' => '9',		'language_code' => 'en',	'title' => 'Owners Notifications',				'description' => 'Notifications to owner',														'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '29',	'configuration_type_id' => '9',		'language_code' => 'fr',	'title' => 'Owners Notifications',				'description' => 'Notifications to owner',														'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '30',	'configuration_type_id' => '9',		'language_code' => 'es',	'title' => 'Owners Notifications',				'description' => 'Notifications to owner',														'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '31',	'configuration_type_id' => '10',	'language_code' => 'pt',	'title' => 'Topic Notifications',				'description' => 'Topic Notifications',															'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '32',	'configuration_type_id' => '10',	'language_code' => 'cz',	'title' => 'Topic Notifications',				'description' => 'Topic Notifications',															'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '33',	'configuration_type_id' => '10',	'language_code' => 'it',	'title' => 'Topic Notifications',				'description' => 'Topic Notifications',															'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '34',	'configuration_type_id' => '10',	'language_code' => 'de',	'title' => 'Topic Notifications',				'description' => 'Topic Notifications',															'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '35',	'configuration_type_id' => '10',	'language_code' => 'en',	'title' => 'Topic Notifications',				'description' => 'Topic Notifications',															'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '36',	'configuration_type_id' => '10',	'language_code' => 'fr',	'title' => 'Topic Notifications',				'description' => 'Topic Notifications',															'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '37',	'configuration_type_id' => '10',	'language_code' => 'es',	'title' => 'Topic Notifications',				'description' => 'Topic Notifications',															'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '38',	'configuration_type_id' => '11',	'language_code' => 'pt',	'title' => 'Notification Deadline',				'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '39',	'configuration_type_id' => '11',	'language_code' => 'cz',	'title' => 'Notification Deadline',				'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '40',	'configuration_type_id' => '11',	'language_code' => 'it',	'title' => 'Notification Deadline',				'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '41',	'configuration_type_id' => '11',	'language_code' => 'de',	'title' => 'Notification Deadline',				'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '42',	'configuration_type_id' => '11',	'language_code' => 'en',	'title' => 'Notification Deadline',				'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '43',	'configuration_type_id' => '11',	'language_code' => 'fr',	'title' => 'Notification Deadline',				'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '44',	'configuration_type_id' => '11',	'language_code' => 'es',	'title' => 'Notification Deadline',				'description' => NULL,																			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('configuration_type_translations')->insert($configurationTypeTranslations);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('configuration_type_translations');
    }
}
