<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSectionTypeParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_type_parameters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('section_type_parameter_key')->unique();
            $table->string('code');
			$table->string('type_code');

            $table->timestamps();
            $table->softDeletes();
        });

        $sectionTypeParameters = array(
            array('id' => '1',    'section_type_parameter_key' => 'ebOQbYmM5Zjci17aCtcoddZBqlXX0Xrp', 'code' => 'textParameter',        'type_code' => 'text',                'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',    'section_type_parameter_key' => '9pFe7216kSZCGMF1bJIZauCos2fwhKEr', 'code' => 'textAreaSection',      'type_code' => 'textarea',            'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',    'section_type_parameter_key' => 'S0evTLPWzGh6DuBC4ObGYzUyysSwi4Ru', 'code' => 'htmlTextArea',         'type_code' => 'html',                'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '4',    'section_type_parameter_key' => '85n8L7GGyJMnUCW3G8SSUkDc9iq696q3', 'code' => 'imagesSingleSection',  'type_code' => 'images_single',       'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '5',    'section_type_parameter_key' => 'uOzZnn3T6rgKo2zmBZSxeDNU3nM4doib', 'code' => 'multipleImages',       'type_code' => 'images_multiple',     'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '6',    'section_type_parameter_key' => 'HdQwEIlGUlr28ganr35ASD7Nxmf9sRHh', 'code' => 'singleFiles',          'type_code' => 'files_single',        'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '7',    'section_type_parameter_key' => 'FbQHXJC9V9tgBN9dCzOu2MY041PZ7LzV', 'code' => 'multipleFiles',        'type_code' => 'files_multiple',      'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '10',   'section_type_parameter_key' => 'ivDFPdSS6FdBMNykqJXxqvBmC0kJFAcO', 'code' => 'number',               'type_code' => 'number',              'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '11',   'section_type_parameter_key' => 'fogUVpUGBppSXaaXZJCfCMyTIwO9qZiN', 'code' => 'color',                'type_code' => 'color',               'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '12',   'section_type_parameter_key' => 'wQQ7nxfM6W0oK06xb9LvCmoiuwqS1lvG', 'code' => 'date',                 'type_code' => 'date',                'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '13',   'section_type_parameter_key' => 'cLqUTvMwoNcrJngIW6UafrgQ9JN3QbCP', 'code' => 'time',                 'type_code' => 'time',                'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '14',   'section_type_parameter_key' => '9Ek7NtrNYKnukHTvfguv3WNhwDOobGc2', 'code' => 'url',                  'type_code' => 'url',                 'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '15',   'section_type_parameter_key' => 'rFXL6xfO8DJi6hrvdFRv1ASTLNtWnte6', 'code' => 'video',                'type_code' => 'video',               'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '16',   'section_type_parameter_key' => 'iiiFpBBSxt4lWjLkGYsiBHRLUrCLP7uU', 'code' => 'cbKey',                'type_code' => 'select_cb_key',       'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '17',   'section_type_parameter_key' => 'QtOLtB0og8q331uhnJVFe6L0ueB3Je41', 'code' => 'numberOfTopics',       'type_code' => 'numberOfTopics',      'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '18',   'section_type_parameter_key' => 'e5JMcaBaavExTsofzcgvrEHGDcNGYnir', 'code' => 'topicsSortOrder',      'type_code' => 'topicsSortOrder',     'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '19',   'section_type_parameter_key' => 'dGxrHAdlgFtx9NLdRXLCa4N1ZAvAPjzp', 'code' => 'cbType',               'type_code' => 'cbType',              'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '20',   'section_type_parameter_key' => '1zv0v2NelUsqaVoXvMN0whBSiAHpFoNT', 'code' => 'headingNumber',        'type_code' => 'heading_number',      'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '27',   'section_type_parameter_key' => '6LIvuJcZzD2htghyZS50kNkxcpg1p1XY', 'code' => 'alignment',            'type_code' => 'alignment',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '28',   'section_type_parameter_key' => 'C8Fy11XLujytndzXTly657uHpQZUr2mo', 'code' => 'contentType',          'type_code' => 'contentType',         'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '29',   'section_type_parameter_key' => 'VMPHVPRWCC80m3GKT1X0bYhHObN5Wdlg', 'code' => 'heading1',             'type_code' => 'heading_1',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '30',   'section_type_parameter_key' => 'FDFy5AEXL7A4KiYOoatQBBHOm0U9bLMe', 'code' => 'heading2',             'type_code' => 'heading_2',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '31',   'section_type_parameter_key' => 'fDK5RfOqJ3opF8b9tv6gr7kyBoFjthOG', 'code' => 'heading3',             'type_code' => 'heading_3',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '32',   'section_type_parameter_key' => 'YKdat4aPCjyQ6MhRh4TF5WSfXm9M8kZh', 'code' => 'heading4',             'type_code' => 'heading_4',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '33',   'section_type_parameter_key' => '6SqZlbGr4FRRmkArmiyEnf72Bgxl0rKB', 'code' => 'heading5',             'type_code' => 'heading_5',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '34',   'section_type_parameter_key' => 'nFab5OlxsedZ8JtIzQNeEIxuglkX8GE0', 'code' => 'heading6',             'type_code' => 'heading_6',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '35',   'section_type_parameter_key' => 'a0cArxzRCRqX4OAysS5eSa7N5taJF3LJ', 'code' => 'buttonText',           'type_code' => 'text',                'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '36',   'section_type_parameter_key' => 'p1VD1uiQPTdJFfaPvRe61EU7xFH805wX', 'code' => 'buttonColor',          'type_code' => 'color',               'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '37',   'section_type_parameter_key' => '8301i3C4RKGwkfRUqId3BAQQeeG6R7QT', 'code' => 'video',                'type_code' => 'video',               'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '38',   'section_type_parameter_key' => 'MfUVJyW9AGuiRYPV21ggMG6K7yO56xBv', 'code' => 'video_title',          'type_code' => 'text',                'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('section_type_parameters')->insert($sectionTypeParameters);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('section_type_parameters');
    }
}
