<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSiteConfsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_confs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('site_conf_key');
            $table->string('code');
            
            $table->integer("site_conf_group_id");
                
            $table->timestamps();
            $table->softDeletes();
        });

        $siteConfs = array(
            array('id' => '1',	'site_conf_key' => 'lIXqtUM3EguCVK7dPcwdhp81tV5pK223',	'code' => 'facebook_id',				'site_conf_group_id' => '1','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',	'site_conf_key' => 'NnnXsWUNEqHzJteLN748UM9awZZJNyJD',	'code' => 'facebook_secret',			'site_conf_group_id' => '1','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',	'site_conf_key' => 'BX6kuwRonvtbeRQ6adMZfJCL3q8Lsfq9',	'code' => 'google_analytics',			'site_conf_group_id' => '2','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '4',	'site_conf_key' => 'XZ6zndbbscWIiW4PhxWNN0P5hVFsEy0Z',	'code' => 'recaptcha_secret_key',		'site_conf_group_id' => '4','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '5',	'site_conf_key' => 'aJamlf3tb6qZq4sI1iNMYFWH3a5y4q39',	'code' => 'recaptcha_site_key',			'site_conf_group_id' => '4','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '6',	'site_conf_key' => 'MqkuCGddlxX6RHIhzPt2ESZbm63uy0KM',	'code' => 'maps_api_key',				'site_conf_group_id' => '3','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '7',	'site_conf_key' => '74ZbAup6EmSu77atsyuUzBFh5qFMkuKW',	'code' => 'boolean_recaptcha_register',	'site_conf_group_id' => '4','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '8',	'site_conf_key' => '2Zkg1HMansJE2PUWD2qG7y9MzXriV9z2',	'code' => 'maps_default_latitude',		'site_conf_group_id' => '3','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '9',	'site_conf_key' => 'L7lANOMe74eHGgdV9WoHq6r4Er2K4A7U',	'code' => 'maps_default_longitude',		'site_conf_group_id' => '3','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '10',	'site_conf_key' => 'gcH3NAiPXaxAVzDS8HyfHiRWsAV5OFrx',	'code' => 'file_marker_icon',			'site_conf_group_id' => '3','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '11',	'site_conf_key' => '975kW7fIAooP64Hwf12H2PWxC86D7vhd',	'code' => 'file_marker_icon_small',		'site_conf_group_id' => '3','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '13',	'site_conf_key' => 'fL86g0KwbXMaih4GlB8HUZDRClxUuImp',	'code' => 'current_phase',				'site_conf_group_id' => '5','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '14',	'site_conf_key' => 'm4WC6lecZ8MZ9c6GTuZGZ3WrRyGDeKi0',	'code' => 'sms_service_code',			'site_conf_group_id' => '6','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '15',	'site_conf_key' => 'NKzbjjzIR5gID3POJ6l7r2Os3gIfeNzr',	'code' => 'sms_service_username',		'site_conf_group_id' => '6','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '16',	'site_conf_key' => 'UIuOFzszyti79cIv2WSu5P3wSrfSq964',	'code' => 'sms_service_password',		'site_conf_group_id' => '6','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '17',	'site_conf_key' => 'wyGOOQDsPmqr1vjG0K7wH9xH8X1i8q2b',	'code' => 'sms_service_sender_name',	'site_conf_group_id' => '6','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '18',	'site_conf_key' => 'gj8yKn9WmwYC9UstfhlwZszKqTiqRfaP',	'code' => 'sms_token_text',				'site_conf_group_id' => '6','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '19',	'site_conf_key' => 'rXHLcenGdPJIdKBwII6Nq6KWiGugX3cB',	'code' => 'sms_max_send',				'site_conf_group_id' => '6','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '20',	'site_conf_key' => 'mE0lDp2zCu2jYwnRzIT5qVt0xbJKEW7N',	'code' => 'sms_indicative_number',		'site_conf_group_id' => '6','created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('site_confs')->insert($siteConfs);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('site_confs');
    }
}
