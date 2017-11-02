<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name');

            $table->timestamps();
            $table->softDeletes();
        });

        $fieldTypes = array(
            array('id' => '1',  'code' => 'color',	    'name'  => 'Color',             'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',  'code' => 'icon',       'name'  => 'Icon',              'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',  'code' => 'pin',        'name'  => 'Pin',               'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '4',  'code' => 'max_value',  'name'  => 'Maximum Value',     'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '5',  'code' => 'min_value',  'name'  => 'Minimum Value',     'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
        );
        DB::table('field_types')->insert($fieldTypes);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('field_types');
    }
}
