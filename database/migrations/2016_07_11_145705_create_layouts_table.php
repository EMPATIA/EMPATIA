<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateLayoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('layouts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('layout_key')->unique();
            $table->string('name');
            $table->string('reference');
            
            $table->timestamps();
            $table->softDeletes();
        });


        $layouts = array(
            array('id' => '1',	'layout_key' => str_random(32), 'name' => 'EMPATIA Default',    'reference' => 'default',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('layouts')->insert($layouts);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('layouts');
    }
}
