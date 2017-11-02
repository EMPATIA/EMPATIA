<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCbOperationSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cb_operation_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cb_operation_schedule_key');
            $table->unsignedInteger('cb_id');
            $table->unsignedInteger('operation_type_id');
            $table->unsignedInteger('operation_action_id');
            $table->boolean('active');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
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
        Schema::dropIfExists('cb_operation_schedules');
    }
}
