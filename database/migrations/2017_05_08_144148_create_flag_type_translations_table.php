<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlagTypeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flag_type_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('flag_type_id');
            $table->text('title');
            $table->text('description')->nullable();
            $table->string('language_code');
            $table->timestamps();
            $table->softDeletes();
        });

        $flagTypeTranslations = array(
            array('id' => '1',  'flag_type_id' => '1',  'title' => 'Topics',    'description' => 'Flag Types for Topics',   'language_code' => 'pt',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',  'flag_type_id' => '1',  'title' => 'Topics',    'description' => 'Flag Types for Topics',   'language_code' => 'cz',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',  'flag_type_id' => '1',  'title' => 'Topics',    'description' => 'Flag Types for Topics',   'language_code' => 'it',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '4',  'flag_type_id' => '1',  'title' => 'Topics',    'description' => 'Flag Types for Topics',   'language_code' => 'de',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '5',  'flag_type_id' => '1',  'title' => 'Topics',    'description' => 'Flag Types for Topics',   'language_code' => 'en',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '6',  'flag_type_id' => '1',  'title' => 'Topics',    'description' => 'Flag Types for Topics',   'language_code' => 'fr',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '7',  'flag_type_id' => '1',  'title' => 'Topics',    'description' => 'Flag Types for Topics',   'language_code' => 'es',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '8',  'flag_type_id' => '2',  'title' => 'Posts',     'description' => 'Flag Types for Posts',    'language_code' => 'pt',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '9',  'flag_type_id' => '2',  'title' => 'Posts',     'description' => 'Flag Types for Posts',    'language_code' => 'cz',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '10', 'flag_type_id' => '2',  'title' => 'Posts',     'description' => 'Flag Types for Posts',    'language_code' => 'it',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '11', 'flag_type_id' => '2',  'title' => 'Posts',     'description' => 'Flag Types for Posts',    'language_code' => 'de',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '12', 'flag_type_id' => '2',  'title' => 'Posts',     'description' => 'Flag Types for Posts',    'language_code' => 'en',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '13', 'flag_type_id' => '2',  'title' => 'Posts',     'description' => 'Flag Types for Posts',    'language_code' => 'fr',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '14', 'flag_type_id' => '2',  'title' => 'Posts',     'description' => 'Flag Types for Posts',    'language_code' => 'es',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
        );
        DB::table('flag_type_translations')->insert($flagTypeTranslations);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flag_type_translations');
    }
}
