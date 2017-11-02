<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSiteConfSiteConfGroupTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_conf_group_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description');
            $table->string('lang_code');

            $table->integer("site_conf_group_id");

            $table->timestamps();
            $table->softDeletes();
        });

        $siteGroupsConfTranslations = array(
            array('id' => '1',  'name' => 'Facebook',             'description' => '',                      'lang_code' => 'pt',  'site_conf_group_id' => '1',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',  'name' => 'Facebook',             'description' => '',                      'lang_code' => 'en',  'site_conf_group_id' => '1',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',  'name' => 'Google Analytics',     'description' => '',                      'lang_code' => 'pt',  'site_conf_group_id' => '2',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '4',  'name' => 'Google Analytics',     'description' => '',                      'lang_code' => 'en',  'site_conf_group_id' => '2',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '5',  'name' => 'Mapas Google',         'description' => '',                      'lang_code' => 'pt',  'site_conf_group_id' => '3',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '6',  'name' => 'Google Maps',          'description' => '',                      'lang_code' => 'en',  'site_conf_group_id' => '3',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '7',  'name' => 'Google Recaptcha',     'description' => '',                      'lang_code' => 'pt',  'site_conf_group_id' => '4',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '8',  'name' => 'Google Recaptcha',     'description' => '',                      'lang_code' => 'en',  'site_conf_group_id' => '4',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '21', 'name' => 'Outras Configurações', 'description' => '',                      'lang_code' => 'pt',  'site_conf_group_id' => '5',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '22', 'name' => 'Other Configurations', 'description' => '',                      'lang_code' => 'cz',  'site_conf_group_id' => '5',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '23', 'name' => 'Other Configurations', 'description' => '',                      'lang_code' => 'it',  'site_conf_group_id' => '5',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '24', 'name' => 'Other Configurations', 'description' => '',                      'lang_code' => 'de',  'site_conf_group_id' => '5',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '25', 'name' => 'Other Configurations', 'description' => '',                      'lang_code' => 'en',  'site_conf_group_id' => '5',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '26', 'name' => 'Other Configurations', 'description' => '',                      'lang_code' => 'fr',  'site_conf_group_id' => '5',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '27', 'name' => 'Other Configurations', 'description' => '',                      'lang_code' => 'es',  'site_conf_group_id' => '5',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '29', 'name' => 'Sms Configurations',   'description' => 'Sms Configurations',    'lang_code' => 'pt',  'site_conf_group_id' => '6',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '30', 'name' => 'Sms Configurations',   'description' => 'Sms Configurations',    'lang_code' => 'cz',  'site_conf_group_id' => '6',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '31', 'name' => 'Sms Configurations',   'description' => 'Sms Configurations',    'lang_code' => 'it',  'site_conf_group_id' => '6',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '32', 'name' => 'Sms Configurations',   'description' => 'Sms Configurations',    'lang_code' => 'de',  'site_conf_group_id' => '6',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '33', 'name' => 'Sms Configurations',   'description' => 'Sms Configurations',    'lang_code' => 'en',  'site_conf_group_id' => '6',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '34', 'name' => 'Sms Configurations',   'description' => 'Sms Configurations',    'lang_code' => 'fr',  'site_conf_group_id' => '6',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '35', 'name' => 'Sms Configurations',   'description' => 'Sms Configurations',    'lang_code' => 'es',  'site_conf_group_id' => '6',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );

        DB::table('site_conf_group_translations')->insert($siteGroupsConfTranslations);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('site_conf_group_translations');
    }
}
