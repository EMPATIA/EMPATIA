<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCachedDataToCbs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cbs', function (Blueprint $table) {
            if (!Schema::hasColumn('cbs', '_cached_data'))  {
                $table->mediumText('_cached_data')->nullable();
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
            if (Schema::hasColumn('cbs', '_cached_data')) {
                $table->dropColumn('_cached_data');
            }
        });
    }
}
