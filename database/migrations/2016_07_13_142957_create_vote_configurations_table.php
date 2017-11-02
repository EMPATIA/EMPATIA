<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateVoteConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vote_configurations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('vote_configuration_key')->unique();
            $table->string('code')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        $voteConfigurations = array(
            array('id' => '1',	'vote_configuration_key' => 'UbMN93hUiEMuOK0lrcq22637kGix2lVu',		'code' => 'show_total_votes',               'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',	'vote_configuration_key' => 'mmUjgKoB7yarps9MSk86ELRBdtq8Ry6m',		'code' => 'vote_in_list',                   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',	'vote_configuration_key' => 'pEJqOYegoKxEl159ysT0r9FY56I9fMtu',		'code' => 'boolean_requires_confirm',       'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '4',	'vote_configuration_key' => 'rMW18CgIStmHwZnL4m8gdJTEHcFFRCX0',		'code' => 'boolean_show_confirmation_view', 'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '5',	'vote_configuration_key' => 'uDVL438TPZS0qHVeivILPub0mUhybYAA',		'code' => 'allow_unsubmit_votes',           'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '6',	'vote_configuration_key' => 'FD1pPOlqgRY5rdbT1ChP0Ech8Uf8U5Df',		'code' => 'allow_in_person_registration',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '7',	'vote_configuration_key' => 'Pr8o5fDwvbiq0VpZO8QrKDLTIcCco9ZP',		'code' => 'allow_in_person_voting',         'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '8',	'vote_configuration_key' => 'xlSplwwC9tZms259oNyu7KRcEuFtKqoB',		'code' => 'show_vote_results',              'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('vote_configurations')->insert($voteConfigurations);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('vote_configurations');
    }
}
