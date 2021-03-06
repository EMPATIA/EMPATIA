<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCodeToTechnicalAnalysisQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('technical_analysis_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('technical_analysis_questions', 'code'))
            {
                $table->string('code')->after('tech_analysis_question_key')->nullable();
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
            if (Schema::hasColumn('technical_analysis_questions', 'code'))
            {
                $table->dropColumn('code');
            }
        });
    }
}
