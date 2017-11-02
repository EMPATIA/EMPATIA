<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateContentTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_types', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('display_linkable');
            $table->boolean('linkable');
            $table->string('code');
            $table->timestamps();
            $table->softDeletes();
        });
        
        $content_types = array(
            array('id' => '1',	'display_linkable' => '1',	'linkable' => '0',	'code' => 'pages',			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',	'display_linkable' => '0',	'linkable' => '1',	'code' => 'news',			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',	'display_linkable' => '0',	'linkable' => '1',	'code' => 'events',			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '4',	'display_linkable' => '1',	'linkable' => '1',	'code' => 'articles',		'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '5',	'display_linkable' => '1',	'linkable' => '1',	'code' => 'faqs',			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '6',	'display_linkable' => '1',	'linkable' => '1',	'code' => 'municipal_faqs',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
            
        DB::table('content_types')->insert($content_types);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('content_types');
    }
}
