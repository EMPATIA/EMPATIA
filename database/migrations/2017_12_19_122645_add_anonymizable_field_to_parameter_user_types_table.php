<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAnonymizableFieldToParameterUserTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parameter_user_types', function (Blueprint $table) {
            if (!Schema::hasColumn('parameter_user_types', 'anonymizable')) {
                $table->boolean('anonymizable')->after('parameter_unique')->default(false);
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
        Schema::table('parameter_user_types', function (Blueprint $table) {
            if (Schema::hasColumn('parameter_user_types', 'anonymizable')) {
                $table->dropColumn('anonymizable');
            }
        });
    }
}
