<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSectionTypeParameterTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_type_parameter_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('section_type_parameter_id');
            $table->string('language_code');
            $table->string('name')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        $sectionTypeParameterTranslations = array(
            array('id' => '1',    'section_type_parameter_id' => '1',   'language_code' => 'pt',  'name' => 'Texto',                            'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',    'section_type_parameter_id' => '1',   'language_code' => 'en',  'name' => 'Text',                             'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',    'section_type_parameter_id' => '2',   'language_code' => 'pt',  'name' => 'Área de Texto',                    'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '4',    'section_type_parameter_id' => '2',   'language_code' => 'en',  'name' => 'Text Area',                        'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '5',    'section_type_parameter_id' => '3',   'language_code' => 'pt',  'name' => 'Área de Texto HTML',               'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '6',    'section_type_parameter_id' => '3',   'language_code' => 'en',  'name' => 'HTML Text Area',                   'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '7',    'section_type_parameter_id' => '4',   'language_code' => 'pt',  'name' => 'Imagem',                           'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '8',    'section_type_parameter_id' => '4',   'language_code' => 'en',  'name' => 'Image',                            'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '9',    'section_type_parameter_id' => '5',   'language_code' => 'pt',  'name' => 'Imagens',                          'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '10',   'section_type_parameter_id' => '5',   'language_code' => 'en',  'name' => 'Images',                           'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '11',   'section_type_parameter_id' => '6',   'language_code' => 'pt',  'name' => 'Ficheiro',                         'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '12',   'section_type_parameter_id' => '6',   'language_code' => 'en',  'name' => 'File',                             'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '13',   'section_type_parameter_id' => '7',   'language_code' => 'pt',  'name' => 'Ficheiros',                        'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '14',   'section_type_parameter_id' => '7',   'language_code' => 'en',  'name' => 'Files',                            'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '15',   'section_type_parameter_id' => '10',  'language_code' => 'pt',  'name' => 'Número',                           'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '16',   'section_type_parameter_id' => '10',  'language_code' => 'en',  'name' => 'Number',                           'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '17',   'section_type_parameter_id' => '11',  'language_code' => 'pt',  'name' => 'Código de Cor',                    'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '18',   'section_type_parameter_id' => '11',  'language_code' => 'en',  'name' => 'Color Code',                       'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '19',   'section_type_parameter_id' => '12',  'language_code' => 'pt',  'name' => 'Data',                             'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '20',   'section_type_parameter_id' => '12',  'language_code' => 'en',  'name' => 'Date',                             'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '21',   'section_type_parameter_id' => '13',  'language_code' => 'pt',  'name' => 'Tempo',                            'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '22',   'section_type_parameter_id' => '13',  'language_code' => 'en',  'name' => 'Time',                             'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '23',   'section_type_parameter_id' => '14',  'language_code' => 'pt',  'name' => 'Ligação',                          'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '24',   'section_type_parameter_id' => '14',  'language_code' => 'en',  'name' => 'Link',                             'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '25',   'section_type_parameter_id' => '15',  'language_code' => 'pt',  'name' => 'Vídeo',                            'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '26',   'section_type_parameter_id' => '15',  'language_code' => 'en',  'name' => 'Video',                            'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '27',   'section_type_parameter_id' => '16',  'language_code' => 'pt',  'name' => 'Nome PAD',                         'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '28',   'section_type_parameter_id' => '16',  'language_code' => 'en',  'name' => 'PAD\'s name',                      'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '29',   'section_type_parameter_id' => '17',  'language_code' => 'pt',  'name' => 'Número de tópicos para listar',    'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '30',   'section_type_parameter_id' => '17',  'language_code' => 'en',  'name' => 'Number of topics to list',         'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '31',   'section_type_parameter_id' => '18',  'language_code' => 'pt',  'name' => 'Ordem da listagem dos tópicos',    'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '32',   'section_type_parameter_id' => '18',  'language_code' => 'en',  'name' => 'List sort order',                  'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '33',   'section_type_parameter_id' => '19',  'language_code' => 'pt',  'name' => 'Tipo de PAD',                      'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '34',   'section_type_parameter_id' => '19',  'language_code' => 'en',  'name' => 'PAD Type',                         'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '35',   'section_type_parameter_id' => '20',  'language_code' => 'pt',  'name' => 'Tamanho do Título',                'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '36',   'section_type_parameter_id' => '20',  'language_code' => 'en',  'name' => 'Title Size',                       'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '37',   'section_type_parameter_id' => '21',  'language_code' => 'pt',  'name' => 'Título 1',                         'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '38',   'section_type_parameter_id' => '21',  'language_code' => 'en',  'name' => 'Heading 1',                        'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '39',   'section_type_parameter_id' => '22',  'language_code' => 'pt',  'name' => 'Título 2',                         'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '40',   'section_type_parameter_id' => '22',  'language_code' => 'en',  'name' => 'Heading 2',                        'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '41',   'section_type_parameter_id' => '23',  'language_code' => 'pt',  'name' => 'Título 3',                         'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '42',   'section_type_parameter_id' => '23',  'language_code' => 'en',  'name' => 'Heading 3',                        'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '43',   'section_type_parameter_id' => '24',  'language_code' => 'pt',  'name' => 'Título 4',                         'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '44',   'section_type_parameter_id' => '24',  'language_code' => 'en',  'name' => 'Heading 4',                        'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '45',   'section_type_parameter_id' => '25',  'language_code' => 'pt',  'name' => 'Título 5',                         'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '46',   'section_type_parameter_id' => '25',  'language_code' => 'en',  'name' => 'Heading 5',                        'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '47',   'section_type_parameter_id' => '26',  'language_code' => 'pt',  'name' => 'Título 6',                         'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '48',   'section_type_parameter_id' => '26',  'language_code' => 'en',  'name' => 'Heading 6',                        'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '49',   'section_type_parameter_id' => '27',  'language_code' => 'pt',  'name' => 'Alinhamento',                      'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '50',   'section_type_parameter_id' => '27',  'language_code' => 'en',  'name' => 'Alignment',                        'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '51',   'section_type_parameter_id' => '28',  'language_code' => 'pt',  'name' => 'Tipo de conteudo',                 'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '52',   'section_type_parameter_id' => '28',  'language_code' => 'en',  'name' => 'Content Type',                     'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '53',   'section_type_parameter_id' => '29',  'language_code' => 'pt',  'name' => 'Título 1',                         'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '54',   'section_type_parameter_id' => '29',  'language_code' => 'en',  'name' => 'Heading 1',                        'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '55',   'section_type_parameter_id' => '30',  'language_code' => 'pt',  'name' => 'Título 2',                         'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '56',   'section_type_parameter_id' => '30',  'language_code' => 'en',  'name' => 'Heading 2',                        'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '57',   'section_type_parameter_id' => '31',  'language_code' => 'pt',  'name' => 'Título 3',                         'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '58',   'section_type_parameter_id' => '31',  'language_code' => 'en',  'name' => 'Heading 3',                        'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '59',   'section_type_parameter_id' => '32',  'language_code' => 'pt',  'name' => 'Título 4',                         'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '60',   'section_type_parameter_id' => '32',  'language_code' => 'en',  'name' => 'Heading 4',                        'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '61',   'section_type_parameter_id' => '33',  'language_code' => 'pt',  'name' => 'Título 5',                         'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '62',   'section_type_parameter_id' => '33',  'language_code' => 'en',  'name' => 'Heading 5',                        'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '63',   'section_type_parameter_id' => '34',  'language_code' => 'pt',  'name' => 'Título 6',                         'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '64',   'section_type_parameter_id' => '34',  'language_code' => 'en',  'name' => 'Heading 6',                        'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '65',   'section_type_parameter_id' => '35',  'language_code' => 'pt',  'name' => 'Texto do botão',                   'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '66',   'section_type_parameter_id' => '35',  'language_code' => 'it',  'name' => 'Button\'s Text',                   'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '67',   'section_type_parameter_id' => '35',  'language_code' => 'en',  'name' => 'Button\'s Text',                   'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '68',   'section_type_parameter_id' => '36',  'language_code' => 'pt',  'name' => 'Cor do botão',                     'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '69',   'section_type_parameter_id' => '36',  'language_code' => 'it',  'name' => 'Button\'s Color',                  'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '70',   'section_type_parameter_id' => '36',  'language_code' => 'en',  'name' => 'Button\'s Color',                  'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '71',   'section_type_parameter_id' => '37',  'language_code' => 'pt',  'name' => 'Vídeo',                            'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '72',   'section_type_parameter_id' => '37',  'language_code' => 'de',  'name' => 'Video',                            'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '73',   'section_type_parameter_id' => '37',  'language_code' => 'en',  'name' => 'Video',                            'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '74',   'section_type_parameter_id' => '38',  'language_code' => 'pt',  'name' => 'Titulo do vídeo',                  'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '75',   'section_type_parameter_id' => '38',  'language_code' => 'de',  'name' => 'Video title',                      'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '76',   'section_type_parameter_id' => '38',  'language_code' => 'en',  'name' => 'Video title',                      'description' => '',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('section_type_parameter_translations')->insert($sectionTypeParameterTranslations);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('section_type_parameter_translations');
    }
}
