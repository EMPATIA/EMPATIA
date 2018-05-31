<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCodeToAccessMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('access_menus', function (Blueprint $table) {
            if (!Schema::hasColumn('access_menus', 'code')) {
                $table->string('code')->after('active')->nullable();
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
        Schema::table('access_menus', function (Blueprint $table) {
            if (Schema::hasColumn('access_menus', 'code')) {
                $table->dropColumn('code');
            }
        });
    }
}
