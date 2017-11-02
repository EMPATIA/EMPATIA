<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAcceptedToTechnicalAnalysisQuestionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('technical_analysis_question_answers', function (Blueprint $table) {
            if (!Schema::hasColumn('technical_analysis_question_answers', 'accepted'))  {
                $table->boolean('accepted')->after('value')->default(0);
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
        Schema::table('technical_analysis_question_answers', function (Blueprint $table) {
            if (Schema::hasColumn('technical_analysis_question_answers', 'accepted')) {
                $table->dropColumn('accepted');
            }
        });
    }
}
