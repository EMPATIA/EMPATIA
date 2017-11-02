<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSectionTypeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_type_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('section_type_id');
            $table->string('language_code');
            $table->text('value')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        $sectionTypeTranslations = array(
            array('id' => '1',    'section_type_id' => '1',     'language_code' => 'pt',  'value' => 'Título',              'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',    'section_type_id' => '1',     'language_code' => 'en',  'value' => 'Title',               'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',    'section_type_id' => '2',     'language_code' => 'pt',  'value' => 'Conteúdo',            'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '4',    'section_type_id' => '2',     'language_code' => 'en',  'value' => 'Content',             'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '5',    'section_type_id' => '3',     'language_code' => 'pt',  'value' => 'Imagem Única',        'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '6',    'section_type_id' => '3',     'language_code' => 'en',  'value' => 'Single Image',        'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '7',    'section_type_id' => '4',     'language_code' => 'pt',  'value' => 'Imagens',             'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '8',    'section_type_id' => '4',     'language_code' => 'en',  'value' => 'Images',              'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '9',    'section_type_id' => '5',     'language_code' => 'pt',  'value' => 'Slideshow',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '10',   'section_type_id' => '5',     'language_code' => 'en',  'value' => 'Slideshow',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '11',   'section_type_id' => '6',     'language_code' => 'pt',  'value' => 'Ficheiro',            'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '12',   'section_type_id' => '6',     'language_code' => 'en',  'value' => 'File',                'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '13',   'section_type_id' => '7',     'language_code' => 'pt',  'value' => 'Ficheiros',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '14',   'section_type_id' => '7',     'language_code' => 'en',  'value' => 'Files',               'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '15',   'section_type_id' => '8',     'language_code' => 'pt',  'value' => 'Vídeo Externo',       'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '16',   'section_type_id' => '8',     'language_code' => 'en',  'value' => 'External Video',      'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '17',   'section_type_id' => '9',     'language_code' => 'pt',  'value' => 'Vídeo',               'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '18',   'section_type_id' => '9',     'language_code' => 'en',  'value' => 'Video',               'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '19',   'section_type_id' => '10',    'language_code' => 'pt',  'value' => 'Lista de Pads',       'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '20',   'section_type_id' => '10',    'language_code' => 'en',  'value' => 'Pads List',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '21',   'section_type_id' => '11',    'language_code' => 'pt',  'value' => 'Título',              'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '22',   'section_type_id' => '11',    'language_code' => 'en',  'value' => 'Heading',             'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '23',   'section_type_id' => '12',    'language_code' => 'pt',  'value' => 'Banner',              'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '24',   'section_type_id' => '12',    'language_code' => 'en',  'value' => 'Banner',              'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '25',   'section_type_id' => '13',    'language_code' => 'pt',  'value' => 'Lista de Conteudos',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '26',   'section_type_id' => '13',    'language_code' => 'en',  'value' => 'Contents List',       'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '27',   'section_type_id' => '14',    'language_code' => 'pt',  'value' => 'Banner com Ligação',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '28',   'section_type_id' => '14',    'language_code' => 'it',  'value' => 'Linked Banner',       'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '29',   'section_type_id' => '14',    'language_code' => 'en',  'value' => 'Linked Banner',       'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '30',   'section_type_id' => '15',    'language_code' => 'pt',  'value' => 'Botão',               'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '31',   'section_type_id' => '15',    'language_code' => 'de',  'value' => 'Button',              'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '32',   'section_type_id' => '15',    'language_code' => 'en',  'value' => 'Button',              'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('section_type_translations')->insert($sectionTypeTranslations);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('section_type_translations');
    }
}
