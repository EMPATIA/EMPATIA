<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExternalValidationToParameterUserTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parameter_user_types', function (Blueprint $table) {
            if (!Schema::hasColumn('parameter_user_types', 'external_validation')) {
                $table->boolean('external_validation')->after('vote_in_person')->default(0);
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
        Schema::table('parameter_user_types', function (Blueprint $table) {
            if (Schema::hasColumn('parameter_user_types', 'vote_in_person')) {
                $table->dropColumn('vote_in_person');
            }
        });
    }
}
