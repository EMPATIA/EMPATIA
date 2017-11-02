<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateOrchParameterTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orch_parameter_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('code');
			$table->boolean('user_parameter')->default('1');

            $table->timestamps();
            $table->softDeletes();
        });

        $parameterTypes = array(
            array('id' => '1',    'name' => 'Text',             'code' => 'text',           'user_parameter' => '1',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',    'name' => 'Radio Buttons',    'code' => 'radio_buttons',  'user_parameter' => '1',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',    'name' => 'Check Box',        'code' => 'check_box',      'user_parameter' => '1',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '4',    'name' => 'Text Area',        'code' => 'text_area',      'user_parameter' => '1',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '5',    'name' => 'Numeric',          'code' => 'numeric',        'user_parameter' => '1',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '6',    'name' => 'Google Maps',      'code' => 'google_maps',    'user_parameter' => '1',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '7',    'name' => 'Dropdown',         'code' => 'dropdown',       'user_parameter' => '1',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '8',    'name' => 'Image Map',        'code' => 'image_map',      'user_parameter' => '1',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '9',    'name' => 'Coin',             'code' => 'coin',           'user_parameter' => '1',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '10',   'name' => 'Category',         'code' => 'category',       'user_parameter' => '1',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '11',   'name' => 'Budget',           'code' => 'budget',         'user_parameter' => '1',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '12',   'name' => 'Birthday',         'code' => 'birthday',       'user_parameter' => '1',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '13',   'name' => 'File',             'code' => 'file',           'user_parameter' => '1',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '14',   'name' => 'Mobile Number',    'code' => 'mobile',         'user_parameter' => '1',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('orch_parameter_types')->insert($parameterTypes);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('orch_parameter_types');
    }
}
