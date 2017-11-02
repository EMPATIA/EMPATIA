<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatisticsToCbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cbs', function (Blueprint $table) {
            if (!Schema::hasColumn('cbs', '_statistics'))  {
                $table->text('_statistics')->nullable();
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
        Schema::table('cbs', function (Blueprint $table) {
            if (Schema::hasColumn('cbs', '_statistics')) {
                $table->dropColumn('_statistics');
            }
        });
    }
}
