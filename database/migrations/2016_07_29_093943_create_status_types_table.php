<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateStatusTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status_type_key')->unique();
            $table->string('code')->unique();
            $table->integer('position')->unsigned();

            $table->timestamps();
            $table->softDeletes();
        });


        $statusTypes = array(
            array('id' => '1',	'status_type_key' => 'P8BFNPjpmoHhPyiQrE032RQ7ZrdSDe',  'code' => 'not_accepted',   'position' => '2',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',	'status_type_key' => 'gQZEq0G4V3bdAHnlQAfWljvrJea9bw',	'code' => 'accepted',       'position' => '3',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',	'status_type_key' => 'A816W0xD93snwrWwFmj9gruLR90Zvx',	'code' => 'in_execution',   'position' => '4',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '4',	'status_type_key' => 'meH1qMDLkXECVE6IGxJKleWRBOBdzF',	'code' => 'concluded',      'position' => '5',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '5',	'status_type_key' => 'I84EnQfczTnCccRA9yzpiuaZLvDYvX',	'code' => 'closed',         'position' => '1',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '6',	'status_type_key' => 'k8YfxWHJXHGqXAfeJynNn7LRo4ezup',	'code' => 'moderated',      'position' => '6',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('status_types')->insert($statusTypes);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('status_types');
    }
}
