<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTechnicalAnalysisQuestionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('technical_analysis_question_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tec_a_q_ans_key')->unique();
            $table->unsignedInteger('technical_analysis_id');
            $table->unsignedInteger('technical_analysis_question_id');

            $table->text('value')->nullable();
            $table->string('created_by');
            $table->string('updated_by');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('technical_analysis_question_answers');
    }
}
