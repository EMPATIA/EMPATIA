<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDecisionToTechnicalAnalysisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('technical_analyses', function (Blueprint $table) {
            if (!Schema::hasColumn('technical_analyses', 'decision'))
            {
                $table->boolean('decision')->after('active')->default(false);
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
            if (Schema::hasColumn('technical_analyses', 'decision'))
            {
                $table->dropColumn('decision');
            }
        });
    }
}
