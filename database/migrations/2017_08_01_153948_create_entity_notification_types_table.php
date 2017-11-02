<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntityNotificationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_notification_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->timestamps();
            $table->softDeletes();
        });

        $entityNotificationTypes = array(
            array('id' => '1',  'code' => 'new_messages',			'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',  'code' => 'new_user_registration',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('entity_notification_types')->insert($entityNotificationTypes);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entity_notification_types');
    }
}
