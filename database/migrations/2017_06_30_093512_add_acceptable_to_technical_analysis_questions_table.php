<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAcceptableToTechnicalAnalysisQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('technical_analysis_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('technical_analysis_questions', 'acceptable'))  {
                $table->boolean('acceptable')->after('cb_id')->default(0);
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
        Schema::table('technical_analysis_questions', function (Blueprint $table) {
            if (Schema::hasColumn('technical_analysis_questions', 'acceptable')) {
                $table->dropColumn('acceptable');
            }
        });
    }
}
