<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntityNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('entity_notification_key');
            $table->unsignedInteger('entity_id');
            $table->unsignedInteger('entity_notification_type_id');
            $table->string('template_key')->nullable();
            $table->text('groups')->nullable();
            $table->boolean('active')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entity_notifications');
    }
}
