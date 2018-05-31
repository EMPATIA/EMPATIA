<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMinimumUsersFieldToParameterUserTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parameter_user_types', function (Blueprint $table) {
            if (!Schema::hasColumn('parameter_user_types', 'minimum_users')) {
                $table->unsignedInteger('minimum_users')->after('anonymizable')->default(0);
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
            if (Schema::hasColumn('parameter_user_types', 'minimum_users')) {
                $table->dropColumn('minimum_users');
            }
        });
    }
}
