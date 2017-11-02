<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDecisionTypeInTechnicalAnalysesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('technical_analyses', function (Blueprint $table) {
            if (Schema::hasColumn('technical_analyses', 'decision')) {
                $table->smallInteger('decision')->default(0)->change();
            } else {
                $table->smallInteger('decision')->after('active')->default(0);
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
        Schema::table('technical_analyses', function (Blueprint $table) {
            if (Schema::hasColumn('technical_analyses', 'decision')) {
                $table->boolean('decision')->default(false)->change();
            } else {
                $table->boolean('decision')->after('active')->default(false);
            }
        });
    }
}
