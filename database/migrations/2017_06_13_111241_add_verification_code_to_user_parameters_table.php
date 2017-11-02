<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVerificationCodeToUserParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_parameters', function (Blueprint $table) {
            if (!Schema::hasColumn('user_parameters', 'confirmation_code'))  {
                $table->string('confirmation_code')->after('value')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('user_parameters', function (Blueprint $table) {
            if (Schema::hasColumn('user_parameters', 'confirmation_code'))
            {
                $table->dropColumn('confirmation_code');
            }
        });
    }
}
