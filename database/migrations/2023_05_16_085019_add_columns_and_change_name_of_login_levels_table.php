<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('login_levels', 'empatia_login_levels');
        
        Schema::table('empatia_login_levels', function (Blueprint $table) {
            $table->json('dependencies')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('empatia_login_levels', 'login_levels');

        Schema::table('empatia_login_levels', function (Blueprint $table) {
            $table->dropColumn('dependencies');
        });
    }
};
