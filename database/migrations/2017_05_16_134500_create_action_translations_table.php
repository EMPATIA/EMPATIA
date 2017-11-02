<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('action_id')->unsigned();
            $table->string('language_code');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $actionTranslations = array(
            array('id' => '1',	'action_id' => '1',	'language_code' => 'pt',	'title' => 'Criar Tópico',			'description' => NULL,	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '2',	'action_id' => '2',	'language_code' => 'pt',	'title' => 'Criar Comentário',		'description' => NULL,	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '3',	'action_id' => '3',	'language_code' => 'pt',	'title' => 'Criar Voto Like',		'description' => NULL,	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '4',	'action_id' => '4',	'language_code' => 'pt',	'title' => 'Criar Voto Multi',		'description' => NULL,	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '5',	'action_id' => '5',	'language_code' => 'pt',	'title' => 'Criar Voto Negativo',	'description' => NULL,	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '6',	'action_id' => '6',	'language_code' => 'pt',	'title' => 'Votar',					'description' => NULL,	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '7',	'action_id' => '1',	'language_code' => 'en',	'title' => 'Create Topic',			'description' => NULL,	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '8',	'action_id' => '2',	'language_code' => 'en',	'title' => 'Create Comment',		'description' => NULL,	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '9',	'action_id' => '3',	'language_code' => 'en',	'title' => 'Create Like Vote',		'description' => NULL,	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '10',	'action_id' => '4',	'language_code' => 'en',	'title' => 'Create Multi Vote',		'description' => NULL,	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '11',	'action_id' => '5',	'language_code' => 'en',	'title' => 'Create Negative Vote',	'description' => NULL,	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '12',	'action_id' => '6',	'language_code' => 'en',	'title' => 'Vote',					'description' => NULL,	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(),	'deleted_at' => NULL),
          );
        DB::table('action_translations')->insert($actionTranslations);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('action_translations');
    }
}
