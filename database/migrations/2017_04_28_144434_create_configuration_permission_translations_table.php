<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigurationPermissionTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuration_permission_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('configuration_permission_id')->unsigned();
            $table->string('language_code');
            $table->string('title');
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        $configurationPermissionTranslations = array(
            array('id' => '1',  'configuration_permission_id' => '1',   'language_code' => 'en',    'title' => 'Create Topic',          'description' => NULL,  'created_at' => Carbon::now(),   'updated_at' => Carbon::now(),   'deleted_at' => NULL),
            array('id' => '2',  'configuration_permission_id' => '2',   'language_code' => 'en',    'title' => 'Create Comment',        'description' => NULL,  'created_at' => Carbon::now(),   'updated_at' => Carbon::now(),   'deleted_at' => NULL),
            array('id' => '3',  'configuration_permission_id' => '3',   'language_code' => 'en',    'title' => 'Create Vote Like',      'description' => NULL,  'created_at' => Carbon::now(),   'updated_at' => Carbon::now(),   'deleted_at' => NULL),
            array('id' => '4',  'configuration_permission_id' => '4',   'language_code' => 'en',    'title' => 'Create Vote Multi',     'description' => NULL,  'created_at' => Carbon::now(),   'updated_at' => Carbon::now(),   'deleted_at' => NULL),
            array('id' => '5',  'configuration_permission_id' => '5',   'language_code' => 'en',    'title' => 'Create Vote Negative',  'description' => NULL,  'created_at' => Carbon::now(),   'updated_at' => Carbon::now(),   'deleted_at' => NULL),
            array('id' => '6',  'configuration_permission_id' => '1',   'language_code' => 'pt',    'title' => 'Criar Tópico',          'description' => NULL,  'created_at' => Carbon::now(),   'updated_at' => Carbon::now(),   'deleted_at' => NULL),
            array('id' => '7',  'configuration_permission_id' => '2',   'language_code' => 'pt',    'title' => 'Criar Comentário',      'description' => NULL,  'created_at' => Carbon::now(),   'updated_at' => Carbon::now(),   'deleted_at' => NULL),
            array('id' => '8',  'configuration_permission_id' => '3',   'language_code' => 'pt',    'title' => 'Criar Voto Like',       'description' => NULL,  'created_at' => Carbon::now(),   'updated_at' => Carbon::now(),   'deleted_at' => NULL),
            array('id' => '9',  'configuration_permission_id' => '4',   'language_code' => 'pt',    'title' => 'Criar Voto Multi',      'description' => NULL,  'created_at' => Carbon::now(),   'updated_at' => Carbon::now(),   'deleted_at' => NULL),
            array('id' => '10', 'configuration_permission_id' => '5',   'language_code' => 'pt',    'title' => 'Criar Voto Negativo',   'description' => NULL,  'created_at' => Carbon::now(),   'updated_at' => Carbon::now(),   'deleted_at' => NULL)
        );
        DB::table('configuration_permission_translations')->insert($configurationPermissionTranslations);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configuration_permission_translations');
    }
}
