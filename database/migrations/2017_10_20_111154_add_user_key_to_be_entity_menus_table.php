<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserKeyToBeEntityMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('be_entity_menus', function (Blueprint $table) {
            if (!Schema::hasColumn('be_entity_menus', 'user_key'))
            {
                $table->string('user_key')->after('entity_id')->nullable();
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
        Schema::table('be_entity_menus', function (Blueprint $table) {
            if (Schema::hasColumn('be_entity_menus', 'user_key'))
            {
                $table->dropColumn('user_key');
            }
        });
    }
}
