<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTokenToCooperatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cooperators', function (Blueprint $table) {
            if (!Schema::hasColumn('cooperators', 'token'))
            {
                $table->string('token')->after('updated_by')->nullable();
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
        Schema::table('cooperators', function (Blueprint $table) {
            if (Schema::hasColumn('cooperators', 'token'))
            {
                $table->dropColumn('token');
            }
        });
    }
}
