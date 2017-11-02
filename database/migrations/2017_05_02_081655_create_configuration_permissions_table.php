<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigurationPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuration_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->integer('configuration_permission_type_id')->unsigned();
            $table->string('created_by');
            $table->string('updated_by');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cb_configuration_permission', function (Blueprint $table) {
           $table->integer('config_permission_id');
           $table->integer('cb_id');
           $table->string('value');
           $table->string('created_by');
           $table->timestamps();
           $keys = array('config_permission_id','cb_id');
           $table->primary($keys);
       });

       $configurationsPermission = array(
           array(
               "configuration_permission_type_id" => 1,
               "code"  => 'create_topic',
               "created_by" => "defaultUSERprojectEMPATIA2016JAN",
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ),array(
               "configuration_permission_type_id" => 2,
               "code"  => 'comment',
               "created_by" => "defaultUSERprojectEMPATIA2016JAN",
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           ),array(
               "configuration_permission_type_id" => 3,
               "code"  => 'create_vote_like',
               "created_by" => "defaultUSERprojectEMPATIA2016JAN",
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           )
           ,array(
               "configuration_permission_type_id" => 3,
               "code"  => 'create_vote_multi',
               "created_by" => "defaultUSERprojectEMPATIA2016JAN",
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           )
           ,array(
               "configuration_permission_type_id" => 3,
               "code"  => 'create_vote_negative',
               "created_by" => "defaultUSERprojectEMPATIA2016JAN",
               'created_at' => Carbon::now(),
               'updated_at' => Carbon::now()
           )
       );
       DB::table('configuration_permissions')->insert($configurationsPermission);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configuration_permissions');
        Schema::dropIfExists('cb_configuration_permission');
    }
}
