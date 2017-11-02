<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManualToUserLoginLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_login_levels', function (Blueprint $table) {
            if (!Schema::hasColumn('user_login_levels', 'manual'))  {
                $table->boolean('manual')->after('updated_by')->default(0);
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
        Schema::table('user_login_levels', function (Blueprint $table) {
            if (Schema::hasColumn('user_login_levels', 'manual')) {
                $table->dropColumn('manual');
            }
        });
    }
}
