<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCodeToParameterUserTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parameter_user_types', function (Blueprint $table) {
            if (!Schema::hasColumn('parameter_user_types', 'code'))
            {
                $table->string('code')->after('parameter_user_type_key')->nullable();
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
            if (Schema::hasColumn('parameter_user_types', 'code'))
            {
                $table->dropColumn('code');
            }
        });
    }
}
