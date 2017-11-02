<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSiteConfTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_conf_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description');
            $table->string('lang_code');

            $table->integer("site_conf_id");

            $table->timestamps();
            $table->softDeletes();
        });

        $siteConfTranslations = array(
            array('id' => '1',		'name' => 'Id do Facebook',						'description' => '',										'lang_code' => 'pt','site_conf_id' => '1',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',		'name' => 'Id from Facebook',					'description' => '',										'lang_code' => 'en','site_conf_id' => '1',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',		'name' => 'Código Secreto do Facebook',			'description' => '',										'lang_code' => 'pt','site_conf_id' => '2',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '4',		'name' => 'Secret code from Facebook',			'description' => '',										'lang_code' => 'en','site_conf_id' => '2',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '5',		'name' => 'Código do google analytics',			'description' => '',										'lang_code' => 'pt','site_conf_id' => '3',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '6',		'name' => 'Google Analytics code',				'description' => '',										'lang_code' => 'en','site_conf_id' => '3',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '7',		'name' => 'Secret Key',							'description' => '',										'lang_code' => 'pt','site_conf_id' => '4',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '8',		'name' => 'Secret Key',							'description' => '',										'lang_code' => 'en','site_conf_id' => '4',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '9',		'name' => 'Site Key',							'description' => '',										'lang_code' => 'pt','site_conf_id' => '5',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '10',		'name' => 'Site Key',							'description' => '',										'lang_code' => 'en','site_conf_id' => '5',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '11',		'name' => 'API do Google Maps',					'description' => '',										'lang_code' => 'pt','site_conf_id' => '6',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '12',		'name' => 'Google Maps API',					'description' => '',										'lang_code' => 'en','site_conf_id' => '6',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '19',		'name' => 'Recaptcha no registo',				'description' => 'Recapcha in register',					'lang_code' => 'pt','site_conf_id' => '7',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '20',		'name' => 'Recapcha in register',				'description' => '',										'lang_code' => 'cz','site_conf_id' => '7',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '21',		'name' => 'Recapcha in register',				'description' => '',										'lang_code' => 'it','site_conf_id' => '7',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '22',		'name' => 'Recapcha in register',				'description' => '',										'lang_code' => 'de','site_conf_id' => '7',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '23',		'name' => 'Recapcha in register',				'description' => '',										'lang_code' => 'en','site_conf_id' => '7',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '24',		'name' => 'Recapcha in register',				'description' => '',										'lang_code' => 'fr','site_conf_id' => '7',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '25',		'name' => 'Recapcha in register',				'description' => '',										'lang_code' => 'es','site_conf_id' => '7',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '37',		'name' => 'Google Maps Default Latitude',		'description' => 'Google Maps Default Latitude',			'lang_code' => 'pt','site_conf_id' => '8',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '38',		'name' => 'Google Maps Default Latitude',		'description' => 'Google Maps Default Latitude',			'lang_code' => 'cz','site_conf_id' => '8',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '39',		'name' => 'Google Maps Default Latitude',		'description' => 'Google Maps Default Latitude',			'lang_code' => 'it','site_conf_id' => '8',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '40',		'name' => 'Google Maps Default Latitude',		'description' => 'Google Maps Default Latitude',			'lang_code' => 'de','site_conf_id' => '8',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '41',		'name' => 'Google Maps Default Latitude',		'description' => 'Google Maps Default Latitude',			'lang_code' => 'en','site_conf_id' => '8',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '42',		'name' => 'Google Maps Default Latitude',		'description' => 'Google Maps Default Latitude',			'lang_code' => 'fr','site_conf_id' => '8',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '43',		'name' => 'Google Maps Default Latitude',		'description' => 'Google Maps Default Latitude',			'lang_code' => 'es','site_conf_id' => '8',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '44',		'name' => 'Google Maps Default Longitude',	    'description' => 'Google Maps Default Longitude',			'lang_code' => 'pt','site_conf_id' => '9',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '45',		'name' => 'Google Maps Default Longitude',	    'description' => 'Google Maps Default Longitude',			'lang_code' => 'cz','site_conf_id' => '9',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '46',		'name' => 'Google Maps Default Longitude',	    'description' => 'Google Maps Default Longitude',			'lang_code' => 'it','site_conf_id' => '9',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '47',		'name' => 'Google Maps Default Longitude',	    'description' => 'Google Maps Default Longitude',			'lang_code' => 'de','site_conf_id' => '9',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '48',		'name' => 'Google Maps Default Longitude',	    'description' => 'Google Maps Default Longitude',			'lang_code' => 'en','site_conf_id' => '9',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '49',		'name' => 'Google Maps Default Longitude',	    'description' => 'Google Maps Default Longitude',			'lang_code' => 'fr','site_conf_id' => '9',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '50',		'name' => 'Google Maps Default Longitude',	    'description' => 'Google Maps Default Longitude',			'lang_code' => 'es','site_conf_id' => '9',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '51',		'name' => 'Google Maps Marker',					'description' => 'Google Maps Marker',						'lang_code' => 'pt','site_conf_id' => '10',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '52',		'name' => 'Google Maps Marker',					'description' => 'Google Maps Marker',						'lang_code' => 'cz','site_conf_id' => '10',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '53',		'name' => 'Google Maps Marker',					'description' => 'Google Maps Marker',						'lang_code' => 'it','site_conf_id' => '10',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '54',		'name' => 'Google Maps Marker',					'description' => 'Google Maps Marker',						'lang_code' => 'de','site_conf_id' => '10',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '55',		'name' => 'Google Maps Marker',					'description' => 'Google Maps Marker',						'lang_code' => 'en','site_conf_id' => '10',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '56',		'name' => 'Google Maps Marker',					'description' => 'Google Maps Marker',						'lang_code' => 'fr','site_conf_id' => '10',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '57',		'name' => 'Google Maps Marker',					'description' => 'Google Maps Marker',						'lang_code' => 'es','site_conf_id' => '10',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '58',		'name' => 'Small Marker',						'description' => 'Small Marker',							'lang_code' => 'pt','site_conf_id' => '11',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '59',		'name' => 'Small Marker',						'description' => 'Small Marker',							'lang_code' => 'cz','site_conf_id' => '11',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '60',		'name' => 'Small Marker',						'description' => 'Small Marker',							'lang_code' => 'it','site_conf_id' => '11',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '61',		'name' => 'Small Marker',						'description' => 'Small Marker',							'lang_code' => 'de','site_conf_id' => '11',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '62',		'name' => 'Small Marker',						'description' => 'Small Marker',							'lang_code' => 'en','site_conf_id' => '11',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '63',		'name' => 'Small Marker',						'description' => 'Small Marker',							'lang_code' => 'fr','site_conf_id' => '11',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '64',		'name' => 'Small Marker',						'description' => 'Small Marker',							'lang_code' => 'es','site_conf_id' => '11',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '72',		'name' => 'Current Phase',						'description' => 'Current Phase',							'lang_code' => 'pt','site_conf_id' => '13',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '73',		'name' => 'Current Phase',						'description' => 'Current Phase',							'lang_code' => 'cz','site_conf_id' => '13',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '74',		'name' => 'Current Phase',						'description' => 'Current Phase',							'lang_code' => 'it','site_conf_id' => '13',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '75',		'name' => 'Current Phase',						'description' => 'Current Phase',							'lang_code' => 'de','site_conf_id' => '13',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '76',		'name' => 'Current Phase',						'description' => 'Current Phase',							'lang_code' => 'en','site_conf_id' => '13',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '77',		'name' => 'Current Phase',						'description' => 'Current Phase',							'lang_code' => 'fr','site_conf_id' => '13',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '78',		'name' => 'Current Phase',						'description' => 'Current Phase',							'lang_code' => 'es','site_conf_id' => '13',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '80',		'name' => 'Sms Service Code',					'description' => 'Sms Service Code',						'lang_code' => 'pt','site_conf_id' => '14',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '81',		'name' => 'Sms Service Code',					'description' => 'Sms Service Code',						'lang_code' => 'cz','site_conf_id' => '14',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '82',		'name' => 'Sms Service Code',					'description' => 'Sms Service Code',						'lang_code' => 'it','site_conf_id' => '14',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '83',		'name' => 'Sms Service Code',					'description' => 'Sms Service Code',						'lang_code' => 'de','site_conf_id' => '14',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '84',		'name' => 'Sms Service Code',					'description' => 'Sms Service Code',						'lang_code' => 'en','site_conf_id' => '14',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '85',		'name' => 'Sms Service Code',					'description' => 'Sms Service Code',						'lang_code' => 'fr','site_conf_id' => '14',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '86',		'name' => 'Sms Service Code',					'description' => 'Sms Service Code',						'lang_code' => 'es','site_conf_id' => '14',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '87',		'name' => 'Sms Service Username',				'description' => 'Sms Service Username',					'lang_code' => 'pt','site_conf_id' => '15',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '88',		'name' => 'Sms Service Username',				'description' => 'Sms Service Username',					'lang_code' => 'cz','site_conf_id' => '15',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '89',		'name' => 'Sms Service Username',				'description' => 'Sms Service Username',					'lang_code' => 'it','site_conf_id' => '15',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '90',		'name' => 'Sms Service Username',				'description' => 'Sms Service Username',					'lang_code' => 'de','site_conf_id' => '15',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '91',		'name' => 'Sms Service Username',				'description' => 'Sms Service Username',					'lang_code' => 'en','site_conf_id' => '15',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '92',		'name' => 'Sms Service Username',				'description' => 'Sms Service Username',					'lang_code' => 'fr','site_conf_id' => '15',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '93',		'name' => 'Sms Service Username',				'description' => 'Sms Service Username',					'lang_code' => 'es','site_conf_id' => '15',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '94',		'name' => 'Sms Service Password',				'description' => 'Sms Service Password',					'lang_code' => 'pt','site_conf_id' => '16',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '95',		'name' => 'Sms Service Password',				'description' => 'Sms Service Password',					'lang_code' => 'cz','site_conf_id' => '16',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '96',		'name' => 'Sms Service Password',				'description' => 'Sms Service Password',					'lang_code' => 'it','site_conf_id' => '16',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '97',		'name' => 'Sms Service Password',				'description' => 'Sms Service Password',					'lang_code' => 'de','site_conf_id' => '16',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '98',		'name' => 'Sms Service Password',				'description' => 'Sms Service Password',					'lang_code' => 'en','site_conf_id' => '16',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '99',		'name' => 'Sms Service Password',				'description' => 'Sms Service Password',					'lang_code' => 'fr','site_conf_id' => '16',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '100',	'name' => 'Sms Service Password',				'description' => 'Sms Service Password',					'lang_code' => 'es','site_conf_id' => '16',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '101',	'name' => 'Sms Service Sender name',			'description' => 'Sms Service Sender name',					'lang_code' => 'pt','site_conf_id' => '17',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '102',	'name' => 'Sms Service Sender name',			'description' => 'Sms Service Sender name',					'lang_code' => 'cz','site_conf_id' => '17',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '103',	'name' => 'Sms Service Sender name',			'description' => 'Sms Service Sender name',					'lang_code' => 'it','site_conf_id' => '17',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '104',	'name' => 'Sms Service Sender name',			'description' => 'Sms Service Sender name',					'lang_code' => 'de','site_conf_id' => '17',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '105',	'name' => 'Sms Service Sender name',			'description' => 'Sms Service Sender name',					'lang_code' => 'en','site_conf_id' => '17',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '106',	'name' => 'Sms Service Sender name',			'description' => 'Sms Service Sender name',					'lang_code' => 'fr','site_conf_id' => '17',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '107',	'name' => 'Sms Service Sender name',			'description' => 'Sms Service Sender name',					'lang_code' => 'es','site_conf_id' => '17',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '108',	'name' => 'SMS Token',							'description' => 'SMS Token',								'lang_code' => 'pt','site_conf_id' => '18',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '109',	'name' => 'SMS Token',							'description' => 'SMS Token',								'lang_code' => 'cz','site_conf_id' => '18',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '110',	'name' => 'SMS Token',							'description' => 'SMS Token',								'lang_code' => 'it','site_conf_id' => '18',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '111',	'name' => 'SMS Token',							'description' => 'SMS Token',								'lang_code' => 'de','site_conf_id' => '18',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '112',	'name' => 'SMS Token',							'description' => 'SMS Token',								'lang_code' => 'en','site_conf_id' => '18',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '113',	'name' => 'SMS Token',							'description' => 'SMS Token',								'lang_code' => 'fr','site_conf_id' => '18',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '114',	'name' => 'SMS Token',							'description' => 'SMS Token',								'lang_code' => 'es','site_conf_id' => '18',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '115',	'name' => 'Maximum number of Sms to send',	    'description' => 'Maximum number of Sms to send',			'lang_code' => 'pt','site_conf_id' => '19',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '116',	'name' => 'Maximum number of Sms to send',	    'description' => 'Maximum number of Sms to send',			'lang_code' => 'cz','site_conf_id' => '19',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '117',	'name' => 'Maximum number of Sms to send',	    'description' => 'Maximum number of Sms to send',			'lang_code' => 'it','site_conf_id' => '19',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '118',	'name' => 'Maximum number of Sms to send',	    'description' => 'Maximum number of Sms to send',			'lang_code' => 'de','site_conf_id' => '19',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '119',	'name' => 'Maximum number of Sms to send',	    'description' => 'Maximum number of Sms to send',			'lang_code' => 'en','site_conf_id' => '19',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '120',	'name' => 'Maximum number of Sms to send',	    'description' => 'Maximum number of Sms to send',			'lang_code' => 'fr','site_conf_id' => '19',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '121',	'name' => 'Maximum number of Sms to send',	    'description' => 'Maximum number of Sms to send',			'lang_code' => 'es','site_conf_id' => '19',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '122',	'name' => 'Sms Indicative',						'description' => 'Sms Indicative',							'lang_code' => 'pt','site_conf_id' => '20',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '123',	'name' => 'Sms Indicative',						'description' => 'Sms Indicative',							'lang_code' => 'cz','site_conf_id' => '20',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '124',	'name' => 'Sms Indicative',						'description' => 'Sms Indicative',							'lang_code' => 'it','site_conf_id' => '20',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '125',	'name' => 'Sms Indicative',						'description' => 'Sms Indicative',							'lang_code' => 'de','site_conf_id' => '20',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '126',	'name' => 'Sms Indicative',						'description' => 'Sms Indicative',							'lang_code' => 'en','site_conf_id' => '20',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '127',	'name' => 'Sms Indicative',						'description' => 'Sms Indicative',							'lang_code' => 'fr','site_conf_id' => '20',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '128',	'name' => 'Sms Indicative',						'description' => 'Sms Indicative',							'lang_code' => 'es','site_conf_id' => '20',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('site_conf_translations')->insert($siteConfTranslations);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('site_conf_translations');
    }
}
