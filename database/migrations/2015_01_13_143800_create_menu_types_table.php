<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateMenuTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('module')->nullable();
            $table->string('title');
            $table->timestamps();
            $table->softDeletes();
        });

        $menuTypes = array(
            array('id' => '1',    'type' => 'url',                    'module' => NULL,       'title' => 'Web Adress',                'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',    'type' => 'pages',                  'module' => 'cm',       'title' => 'Pages',                     'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',    'type' => 'news',                   'module' => 'cm',       'title' => 'News',                      'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '4',    'type' => 'events',                 'module' => 'cm',       'title' => 'Events',                    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '5',    'type' => 'forum',                  'module' => 'cb',       'title' => 'Forum',                     'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '6',    'type' => 'discussion',             'module' => 'cb',       'title' => 'Discussion',                'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '7',    'type' => 'proposals',              'module' => 'cb',       'title' => 'Proposals',                 'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '8',    'type' => 'questionnaires',         'module' => 'q',        'title' => 'Questionnaires',            'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '9',    'type' => 'pools',                  'module' => 'q',        'title' => 'Pools',                     'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '10',   'type' => 'events',                 'module' => 'events',   'title' => 'Conference Events',         'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '11',   'type' => 'ideas',                  'module' => 'cb',       'title' => 'Ideas',                     'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '12',   'type' => 'publicConsultation',     'module' => 'cb',       'title' => 'Public Consultation',       'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '13',   'type' => 'tematicConsultation',    'module' => 'cb',       'title' => 'Tematic Consultation',      'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '14',   'type' => 'survey',                 'module' => 'cb',       'title' => 'Survey',                    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '15',   'type' => 'phase1',                 'module' => 'cb',       'title' => 'Phase 1',                   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '16',   'type' => 'phase2',                 'module' => 'cb',       'title' => 'Phase 2',                   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '17',   'type' => 'phase3',                 'module' => 'cb',       'title' => 'Phase 3',                   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '18',   'type' => 'pages_new',              'module' => 'cm',       'title' => 'Pages New',                 'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('menu_types')->insert($menuTypes);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('menu_types');
    }
}
