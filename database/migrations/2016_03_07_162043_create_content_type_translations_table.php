<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateContentTypeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_type_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('content_type_id');
            $table->string('language_code');
            $table->string('title');
            
            $table->timestamps();
            $table->softDeletes();
        });
        
        $content_type_translations = array(
            array('id' => '1',  'content_type_id' => '1', 'language_code' => 'en',  'title' => 'Pages',     'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',  'content_type_id' => '2', 'language_code' => 'en',  'title' => 'News',      'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',  'content_type_id' => '3', 'language_code' => 'en',  'title' => 'Events',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '4',  'content_type_id' => '1', 'language_code' => 'cz',  'title' => 'Pages',     'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '5',  'content_type_id' => '2', 'language_code' => 'cz',  'title' => 'News',      'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '6',  'content_type_id' => '3', 'language_code' => 'cz',  'title' => 'Events',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '7',  'content_type_id' => '1', 'language_code' => 'pt',  'title' => 'Pagina',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '8',  'content_type_id' => '2', 'language_code' => 'pt',  'title' => 'Noticias',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '9',  'content_type_id' => '3', 'language_code' => 'pt',  'title' => 'Eventos',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '10', 'content_type_id' => '3', 'language_code' => 'pt',  'title' => 'Eventos',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '11', 'content_type_id' => '0', 'language_code' => 'en',  'title' => 'Articles',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '12', 'content_type_id' => '0', 'language_code' => 'de',  'title' => 'Articles',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '13', 'content_type_id' => '4', 'language_code' => 'en',  'title' => 'Articles',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '14', 'content_type_id' => '4', 'language_code' => 'de',  'title' => 'Articles',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '15', 'content_type_id' => '4', 'language_code' => 'pt',  'title' => 'Artigos',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('content_type_translations')->insert($content_type_translations);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('content_type_translations');
    }
}
