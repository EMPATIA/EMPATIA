<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateParameterTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parameter_types', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('param_add_fields_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('code');
			$table->integer('options')->default('0');

            $table->timestamps();
            $table->softDeletes();
        });

        $parameterTypes = array(
            array('id' => '1',    'param_add_fields_id' => '0',   'name' => 'Text',                     'code' => 'text',                       'options' => '0',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',    'param_add_fields_id' => '0',   'name' => 'Radio Buttons',            'code' => 'radio_buttons',              'options' => '1',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',    'param_add_fields_id' => '0',   'name' => 'Check Box',                'code' => 'check_box',                  'options' => '1',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '4',    'param_add_fields_id' => '0',   'name' => 'Text Area',                'code' => 'text_area',                  'options' => '0',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '5',    'param_add_fields_id' => '0',   'name' => 'Numeric',                  'code' => 'numeric',                    'options' => '0',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '6',    'param_add_fields_id' => '0',   'name' => 'Google Maps',              'code' => 'google_maps',                'options' => '0',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '7',    'param_add_fields_id' => '0',   'name' => 'Dropdown',                 'code' => 'dropdown',                   'options' => '1',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '8',    'param_add_fields_id' => '0',   'name' => 'Image Map',                'code' => 'image_map',                  'options' => '0',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '9',    'param_add_fields_id' => '0',   'name' => 'Coin',                     'code' => 'coin',                       'options' => '0',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '10',   'param_add_fields_id' => '0',   'name' => 'Email',                    'code' => 'email',                      'options' => '0',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '11',   'param_add_fields_id' => '0',   'name' => 'Category',                 'code' => 'category',                   'options' => '1',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '12',   'param_add_fields_id' => '0',   'name' => 'Topic passed phases',      'code' => 'topic_checkpoints',          'options' => '1',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '13',   'param_add_fields_id' => '0',   'name' => 'Topic Chekpoints Decider', 'code' => 'topic_checkpoints_boolean',  'options' => '0',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '14',   'param_add_fields_id' => '0',   'name' => 'Going to Pass the phase',  'code' => 'going_to_pass',              'options' => '0',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '15',   'param_add_fields_id' => '0',   'name' => 'Topic Chekpoint Phase',    'code' => 'topic_checkpoint_phase',     'options' => '0',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '16',   'param_add_fields_id' => '0',   'name' => 'Mobile Number',            'code' => 'mobile',                     'options' => '0',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('parameter_types')->insert($parameterTypes);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('parameter_types');
    }
}
