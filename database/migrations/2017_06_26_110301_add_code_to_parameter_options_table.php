<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCodeToParameterOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parameter_options', function (Blueprint $table) {
            if (!Schema::hasColumn('parameter_options', 'code'))  {
                $table->string('code')->after('parameter_id')->nullable();
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
        Schema::table('parameter_options', function (Blueprint $table) {
            if (Schema::hasColumn('parameter_options', 'code')) {
                $table->dropColumn('code');
            }
        });
    }
}
