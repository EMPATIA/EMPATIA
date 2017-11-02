<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParameterCodeToParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parameters', function (Blueprint $table) {
            if (!Schema::hasColumn('parameters', 'parameter_code'))  {
                $table->string('parameter_code')->after('code')->nullable();
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
        Schema::table('parameters', function (Blueprint $table) {
            if (Schema::hasColumn('parameters', 'parameter_code')) {
                $table->dropColumn('parameter_code');
            }
        });
    }
}
