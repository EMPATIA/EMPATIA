<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmailVerificationToLoginLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('login_levels', function (Blueprint $table) {
            if (!Schema::hasColumn('login_levels', 'email_verification'))
            {
                $table->boolean('email_verification')->after('sms_verification')->default(0);
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
        Schema::table('login_levels', function (Blueprint $table) {
            if (Schema::hasColumn('login_levels', 'email_verification'))
            {
                $table->dropColumn('email_verification');
            }
        });
    }
}
