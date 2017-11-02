<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateVoteConfigurationTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vote_configuration_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vote_configuration_id')->unsigned();
            $table->string('language_code');
            $table->string('name');
            $table->string('description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        $voteConfigurationTranslations = array(
            array('id' => '1',    'vote_configuration_id' => '1', 'language_code' => 'pt',  'name' => 'Mostrar total de votos',               'description' => NULL,                                    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',    'vote_configuration_id' => '1', 'language_code' => 'it',  'name' => 'Mostra voti totali',                   'description' => NULL,                                    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',    'vote_configuration_id' => '1', 'language_code' => 'de',  'name' => 'Zeigen Gesamtstimmen',                 'description' => NULL,                                    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '4',    'vote_configuration_id' => '1', 'language_code' => 'en',  'name' => 'Show total votes',                     'description' => NULL,                                    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '5',    'vote_configuration_id' => '2', 'language_code' => 'pt',  'name' => 'Voto em lista',                        'description' => NULL,                                    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '6',    'vote_configuration_id' => '2', 'language_code' => 'it',  'name' => ' voto in lista',                       'description' => NULL,                                    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '7',    'vote_configuration_id' => '2', 'language_code' => 'de',  'name' => 'Abstimmung in der Liste',              'description' => NULL,                                    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '8',    'vote_configuration_id' => '2', 'language_code' => 'en',  'name' => 'Vote in list',                         'description' => NULL,                                    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '9',    'vote_configuration_id' => '1', 'language_code' => 'cz',  'name' => 'Show total votes',                     'description' => NULL,                                    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '10',   'vote_configuration_id' => '2', 'language_code' => 'cz',  'name' => 'Vote in list',                         'description' => NULL,                                    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '11',   'vote_configuration_id' => '3', 'language_code' => 'pt',  'name' => 'Requires votes confirmation',          'description' => 'Requires votes confirmation',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '12',   'vote_configuration_id' => '3', 'language_code' => 'cz',  'name' => 'Requires votes confirmation',          'description' => 'Requires votes confirmation',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '13',   'vote_configuration_id' => '3', 'language_code' => 'it',  'name' => 'Requires votes confirmation',          'description' => 'Requires votes confirmation',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '14',   'vote_configuration_id' => '3', 'language_code' => 'de',  'name' => 'Requires votes confirmation',          'description' => 'Requires votes confirmation',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '15',   'vote_configuration_id' => '3', 'language_code' => 'en',  'name' => 'Requires votes confirmation',          'description' => 'Requires votes confirmation',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '16',   'vote_configuration_id' => '3', 'language_code' => 'fr',  'name' => 'Requires votes confirmation',          'description' => 'Requires votes confirmation',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '17',   'vote_configuration_id' => '3', 'language_code' => 'es',  'name' => 'Requires votes confirmation',          'description' => 'Requires votes confirmation',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '18',   'vote_configuration_id' => '4', 'language_code' => 'pt',  'name' => 'Show vote confirmation view',          'description' => 'Show vote confirmation view',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '19',   'vote_configuration_id' => '4', 'language_code' => 'cz',  'name' => 'Show vote confirmation view',          'description' => 'Show vote confirmation view',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '20',   'vote_configuration_id' => '4', 'language_code' => 'it',  'name' => 'Show vote confirmation view',          'description' => 'Show vote confirmation view',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '21',   'vote_configuration_id' => '4', 'language_code' => 'de',  'name' => 'Show vote confirmation view',          'description' => 'Show vote confirmation view',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '22',   'vote_configuration_id' => '4', 'language_code' => 'en',  'name' => 'Show vote confirmation view',          'description' => 'Show vote confirmation view',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '23',   'vote_configuration_id' => '4', 'language_code' => 'fr',  'name' => 'Show vote confirmation view',          'description' => 'Show vote confirmation view',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '24',   'vote_configuration_id' => '4', 'language_code' => 'es',  'name' => 'Show vote confirmation view',          'description' => 'Show vote confirmation view',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '25',   'vote_configuration_id' => '5', 'language_code' => 'pt',  'name' => 'Allow to re-open voting',              'description' => 'Allow to re-open voting',               'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '26',   'vote_configuration_id' => '5', 'language_code' => 'cz',  'name' => 'Allow to re-open voting',              'description' => 'Allow to re-open voting',               'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '27',   'vote_configuration_id' => '5', 'language_code' => 'it',  'name' => 'Allow to re-open voting',              'description' => 'Allow to re-open voting',               'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '28',   'vote_configuration_id' => '5', 'language_code' => 'de',  'name' => 'Allow to re-open voting',              'description' => 'Allow to re-open voting',               'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '29',   'vote_configuration_id' => '5', 'language_code' => 'en',  'name' => 'Allow to re-open voting',              'description' => 'Allow to re-open voting',               'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '30',   'vote_configuration_id' => '5', 'language_code' => 'fr',  'name' => 'Allow to re-open voting',              'description' => 'Allow to re-open voting',               'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '31',   'vote_configuration_id' => '5', 'language_code' => 'es',  'name' => 'Allow to re-open voting',              'description' => 'Allow to re-open voting',               'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '32',   'vote_configuration_id' => '6', 'language_code' => 'pt',  'name' => 'Allow in Person Registration',         'description' => 'Allow in Person Registration',          'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '33',   'vote_configuration_id' => '6', 'language_code' => 'cz',  'name' => 'Allow in Person Registration',         'description' => 'Allow in Person Registration',          'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '34',   'vote_configuration_id' => '6', 'language_code' => 'it',  'name' => 'Allow in Person Registration',         'description' => 'Allow in Person Registration',          'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '35',   'vote_configuration_id' => '6', 'language_code' => 'de',  'name' => 'Allow in Person Registration',         'description' => 'Allow in Person Registration',          'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '36',   'vote_configuration_id' => '6', 'language_code' => 'en',  'name' => 'Allow in Person Registration',         'description' => 'Allow in Person Registration',          'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '37',   'vote_configuration_id' => '6', 'language_code' => 'fr',  'name' => 'Allow in Person Registration',         'description' => 'Allow in Person Registration',          'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '38',   'vote_configuration_id' => '6', 'language_code' => 'es',  'name' => 'Allow in Person Registration',         'description' => 'Allow in Person Registration',          'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '39',   'vote_configuration_id' => '7', 'language_code' => 'pt',  'name' => 'Allow in Person Voting',               'description' => 'Allow in Person Voting',                'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '40',   'vote_configuration_id' => '7', 'language_code' => 'cz',  'name' => 'Allow in Person Voting',               'description' => 'Allow in Person Voting',                'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '41',   'vote_configuration_id' => '7', 'language_code' => 'it',  'name' => 'Allow in Person Voting',               'description' => 'Allow in Person Voting',                'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '42',   'vote_configuration_id' => '7', 'language_code' => 'de',  'name' => 'Allow in Person Voting',               'description' => 'Allow in Person Voting',                'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '43',   'vote_configuration_id' => '7', 'language_code' => 'en',  'name' => 'Allow in Person Voting',               'description' => 'Allow in Person Voting',                'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '44',   'vote_configuration_id' => '7', 'language_code' => 'fr',  'name' => 'Allow in Person Voting',               'description' => 'Allow in Person Voting',                'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '45',   'vote_configuration_id' => '7', 'language_code' => 'es',  'name' => 'Allow in Person Voting',               'description' => 'Allow in Person Voting',                'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '46',   'vote_configuration_id' => '8', 'language_code' => 'pt',  'name' => 'Show Vote Results (after vote end)',   'description' => 'Show Vote Results (after vote end)',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '47',   'vote_configuration_id' => '8', 'language_code' => 'cz',  'name' => 'Show Vote Results (after vote end)',   'description' => 'Show Vote Results (after vote end)',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '48',   'vote_configuration_id' => '8', 'language_code' => 'it',  'name' => 'Show Vote Results (after vote end)',   'description' => 'Show Vote Results (after vote end)',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '49',   'vote_configuration_id' => '8', 'language_code' => 'de',  'name' => 'Show Vote Results (after vote end)',   'description' => 'Show Vote Results (after vote end)',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '50',   'vote_configuration_id' => '8', 'language_code' => 'en',  'name' => 'Show Vote Results (after vote end)',   'description' => 'Show Vote Results (after vote end)',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '51',   'vote_configuration_id' => '8', 'language_code' => 'fr',  'name' => 'Show Vote Results (after vote end)',   'description' => 'Show Vote Results (after vote end)',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '52',   'vote_configuration_id' => '8', 'language_code' => 'es',  'name' => 'Show Vote Results (after vote end)',   'description' => 'Show Vote Results (after vote end)',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('vote_configuration_translations')->insert($voteConfigurationTranslations);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('vote_configuration_translations');
    }
}
