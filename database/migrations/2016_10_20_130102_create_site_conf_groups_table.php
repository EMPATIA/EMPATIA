<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSiteConfGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_conf_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('site_conf_group_key');
            $table->string('code');

            $table->timestamps();
            $table->softDeletes();
        });

        $siteGroupsConfs = array(
            array('id' => '1',  'site_conf_group_key' => 'aEu1c9dUrAjh1IykyUuG0xnQgpaHPvnU',    'code' => 'facebook',               'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',  'site_conf_group_key' => 'WVBHDX2s1CxU7bFIDr6ncxnPMernLV8w',    'code' => 'google_analytics',       'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',  'site_conf_group_key' => 'mfLBXiIjPxEZW4xJOzovpgShVq71BNjh',    'code' => 'google_maps',            'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '4',  'site_conf_group_key' => 'Vq10pdF22EDme6DCWPnQrOEeidJt3HhG',    'code' => 'google_recaptcha',       'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '5',  'site_conf_group_key' => 'CgubIL3ZnyPAECQ6XAE0IKTtJKcYBcbj',    'code' => 'other_configurations',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '6',  'site_conf_group_key' => '7Zk0N3McTI5L8dd1hn4AePpF3MjSNGiy',    'code' => 'sms',                    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
        );

        DB::table('site_conf_groups')->insert($siteGroupsConfs);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('site_conf_groups');
    }
}
