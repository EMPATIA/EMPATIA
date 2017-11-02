<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntityNotificationTypeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_notification_type_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entity_notification_type_id');
            $table->string('language_code');
            $table->string('value');
            $table->timestamps();
            $table->softDeletes();
        });

        $entityNotificationTypeTranslations = array(
            array('id' => '1',  'entity_notification_type_id' => '1',   'language_code' => 'en',    'value' => 'New Messages',                  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',  'entity_notification_type_id' => '1',   'language_code' => 'pt',    'value' => 'Novas Mensagens',               'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',  'entity_notification_type_id' => '2',   'language_code' => 'en',    'value' => 'New User Registration',         'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '4',  'entity_notification_type_id' => '2',   'language_code' => 'pt',    'value' => 'Novo Registo de Utilizador',    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('entity_notification_type_translations')->insert($entityNotificationTypeTranslations);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entity_notification_type_translations');
    }
}
