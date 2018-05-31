<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptionToParameterUserTypeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parameter_user_type_translations', function (Blueprint $table) {
            if (!Schema::hasColumn('parameter_user_type_translations', 'description'))
            {
                $table->text('description')->after('name')->nullable();
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
        Schema::table('parameter_user_type_translations', function (Blueprint $table) {
            if (Schema::hasColumn('parameter_user_type_translations', 'description'))
            {
                $table->dropColumn('description');
            }
        });
    }
}
