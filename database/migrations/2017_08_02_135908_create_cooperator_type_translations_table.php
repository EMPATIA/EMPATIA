<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCooperatorTypeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cooperator_type_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cooperator_type_id')->unsigned();
            $table->string('language_code');
            $table->string('name');
            $table->longText('description');
            $table->timestamps();
            $table->softDeletes();
        });

        $cooperatorsTypesTranslations = array(
            array('id' => 1,    'cooperator_type_id' => 1,  'language_code' => 'pt',    'name' => 'Comentar',               'description' => 'Cooperador apenas pode comentar',     'created_at' => Carbon::now(), 'updated_at' => Carbon::now(), 'deleted_at' => NULL),
            array('id' => 2,    'cooperator_type_id' => 1,  'language_code' => 'en',    'name' => 'Comment',                'description' => 'Cooperator only can comment',         'created_at' => Carbon::now(), 'updated_at' => Carbon::now(), 'deleted_at' => NULL),
            array('id' => 3,    'cooperator_type_id' => 2,  'language_code' => 'pt',    'name' => 'Comentar e Editar',      'description' => 'Cooperador pode comentar e editar',   'created_at' => Carbon::now(), 'updated_at' => Carbon::now(), 'deleted_at' => NULL),
            array('id' => 4,    'cooperator_type_id' => 2,  'language_code' => 'en',    'name' => 'Comment and Edit',       'description' => 'Cooperator can comment and edit',     'created_at' => Carbon::now(), 'updated_at' => Carbon::now(), 'deleted_at' => NULL)
        );
        DB::table('cooperator_type_translations')->insert($cooperatorsTypesTranslations);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cooperator_type_translations');
    }
}
