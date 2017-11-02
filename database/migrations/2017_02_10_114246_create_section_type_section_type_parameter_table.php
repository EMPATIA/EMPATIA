<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSectionTypeSectionTypeParameterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_type_section_type_parameter', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('section_type_id');
            $table->unsignedInteger('section_type_parameter_id');
            $table->timestamps();
        });

        $sectionTypeSectionTypeParameter = array(
            array('id' => '2',    'section_type_id' => '2',   'section_type_parameter_id' => '3',     'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '3',    'section_type_id' => '3',   'section_type_parameter_id' => '4',     'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '4',    'section_type_id' => '4',   'section_type_parameter_id' => '5',     'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '5',    'section_type_id' => '5',   'section_type_parameter_id' => '5',     'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '6',    'section_type_id' => '6',   'section_type_parameter_id' => '6',     'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '7',    'section_type_id' => '7',   'section_type_parameter_id' => '7',     'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '8',    'section_type_id' => '8',   'section_type_parameter_id' => '14',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '9',    'section_type_id' => '9',   'section_type_parameter_id' => '15',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '17',   'section_type_id' => '10',  'section_type_parameter_id' => '16',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '18',   'section_type_id' => '10',  'section_type_parameter_id' => '17',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '19',   'section_type_id' => '10',  'section_type_parameter_id' => '18',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '20',   'section_type_id' => '10',  'section_type_parameter_id' => '19',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '21',   'section_type_id' => '1',   'section_type_parameter_id' => '1',     'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '22',   'section_type_id' => '11',  'section_type_parameter_id' => '20',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '23',   'section_type_id' => '11',  'section_type_parameter_id' => '1',     'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '25',   'section_type_id' => '12',  'section_type_parameter_id' => '11',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '27',   'section_type_id' => '12',  'section_type_parameter_id' => '27',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '28',   'section_type_id' => '11',  'section_type_parameter_id' => '11',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '29',   'section_type_id' => '13',  'section_type_parameter_id' => '28',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '30',   'section_type_id' => '13',  'section_type_parameter_id' => '17',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '31',   'section_type_id' => '12',  'section_type_parameter_id' => '29',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '32',   'section_type_id' => '12',  'section_type_parameter_id' => '32',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '33',   'section_type_id' => '14',  'section_type_parameter_id' => '11',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '34',   'section_type_id' => '14',  'section_type_parameter_id' => '16',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '35',   'section_type_id' => '14',  'section_type_parameter_id' => '19',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '36',   'section_type_id' => '14',  'section_type_parameter_id' => '29',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '37',   'section_type_id' => '14',  'section_type_parameter_id' => '35',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '38',   'section_type_id' => '14',  'section_type_parameter_id' => '36',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '39',   'section_type_id' => '14',  'section_type_parameter_id' => '14',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '40',   'section_type_id' => '15',  'section_type_parameter_id' => '14',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '41',   'section_type_id' => '15',  'section_type_parameter_id' => '27',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '42',   'section_type_id' => '15',  'section_type_parameter_id' => '35',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '43',   'section_type_id' => '15',  'section_type_parameter_id' => '36',    'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '44',   'section_type_id' => '8',   'section_type_parameter_id' => '38',    'created_at' => Carbon::now(),'updated_at' => Carbon::now())
        );
        DB::table('section_type_section_type_parameter')->insert($sectionTypeSectionTypeParameter);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('section_type_section_type_parameter');
    }
}
