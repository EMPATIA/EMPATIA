<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCodeFieldsToCbOperationSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cb_operation_schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('cb_operation_schedules', 'type_code'))
            {
                $table->string('type_code')->after('operation_action_id')->nullable();
            }
            if (!Schema::hasColumn('cb_operation_schedules', 'action_code'))
            {
                $table->string('action_code')->after('type_code')->nullable();
            }

            //  Change columns to nullable
            if (Schema::hasColumn('cb_operation_schedules', 'operation_type_id'))
            {
                $table->unsignedInteger('operation_type_id')->nullable()->change();
            }

            if (Schema::hasColumn('cb_operation_schedules', 'operation_action_id'))
            {
                $table->unsignedInteger('operation_action_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cb_operation_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('cb_operation_schedules', 'type_code'))
            {
                $table->dropColumn('type_code');
            }
            if (Schema::hasColumn('cb_operation_schedules', 'action_code'))
            {
                $table->dropColumn('action_code');
            }
        });
    }
}
