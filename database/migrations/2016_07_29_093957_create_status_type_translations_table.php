<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateStatusTypeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_type_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('language_code');
            $table->integer('status_type_id')->unsigned();
            $table->string('name');
            $table->string('description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });


        $statusTypes = array(
            array('id' => '1',  'language_code' => 'en',  'status_type_id' => '1',  'name' => 'Not accepted',       'description' => 'Not accepted status translation',     'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',  'language_code' => 'en',  'status_type_id' => '2',  'name' => 'Accepted',           'description' => 'Not accepted status translation',     'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',  'language_code' => 'en',  'status_type_id' => '3',  'name' => 'In execution',       'description' => 'Not accepted status translation',     'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '4',  'language_code' => 'en',  'status_type_id' => '4',  'name' => 'Concluded',          'description' => 'Not accepted status translation',     'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '6',  'language_code' => 'pt',  'status_type_id' => '1',  'name' => 'Rejeitado',          'description' => 'Não passou avaliação técnica',        'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '7',  'language_code' => 'pt',  'status_type_id' => '2',  'name' => 'Avaliação técnica',  'description' => 'Em avaliação técnica',                'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '8',  'language_code' => 'pt',  'status_type_id' => '3',  'name' => 'Em execução',        'description' => 'Em execução',                         'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '9',  'language_code' => 'pt',  'status_type_id' => '4',  'name' => 'Concluída',          'description' => 'Concluída',                           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '10', 'language_code' => 'cz',  'status_type_id' => '1',  'name' => 'Nepřijato',          'description' => 'Not accepted status translation',     'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '11', 'language_code' => 'cz',  'status_type_id' => '2',  'name' => 'Přijat',             'description' => 'Not accepted status translation',     'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '12', 'language_code' => 'cz',  'status_type_id' => '3',  'name' => 'Při spuštění',       'description' => 'Not accepted status translation',     'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '13', 'language_code' => 'cz',  'status_type_id' => '4',  'name' => 'Uzavřít',            'description' => 'Not accepted status translation',     'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '14', 'language_code' => 'pt',  'status_type_id' => '5',  'name' => 'Fechada',            'description' => 'Fechada',                             'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '15', 'language_code' => 'en',  'status_type_id' => '5',  'name' => 'Closed',             'description' => 'Closed',                              'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '16', 'language_code' => 'cz',  'status_type_id' => '5',  'name' => 'Uzavřít',            'description' => 'Not accepted status translation',     'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '17', 'language_code' => 'pt',  'status_type_id' => '6',  'name' => 'Moderada',           'description' => 'Moderada',                            'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '18', 'language_code' => 'en',  'status_type_id' => '6',  'name' => 'Moderated',          'description' => 'Moderated',                           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '19', 'language_code' => 'de',  'status_type_id' => '1',  'name' => 'Nicht Angenommen',   'description' => 'Not accepted status translation',     'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '20', 'language_code' => 'de',  'status_type_id' => '2',  'name' => 'Angenommen',         'description' => 'Not accepted status translation',     'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '21', 'language_code' => 'de',  'status_type_id' => '3',  'name' => 'Wird Ausgeführt',    'description' => 'in execution status de translation',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '22', 'language_code' => 'de',  'status_type_id' => '4',  'name' => 'Abgeschlossen',      'description' => 'concluded status de translation',     'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '23', 'language_code' => 'de',  'status_type_id' => '5',  'name' => 'Geschlossen',        'description' => 'Closed de translation',               'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '24', 'language_code' => 'de',  'status_type_id' => '6',  'name' => 'Moderiert',          'description' => 'Moderated de translation',            'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('status_type_translations')->insert($statusTypes);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('status_type_translations');
    }
}
