<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

class CreateTopicReviewStatusTypesTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_review_status_type_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('language_code');
            $table->integer('topic_review_status_type_id')->unsigned();
            $table->string('name');
            $table->string('description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
        
        $topicReviewStatusTypesTranslations = array(
            array('id' => '1',	 'language_code' => 'en',	 'topic_review_status_type_id' => '1',		'name' => 'Open',               'description' => 'open topic review status translation',	 			    'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '2',	 'language_code' => 'pt',	 'topic_review_status_type_id' => '1',      'name' => 'Aberto',	 			'description' => NULL,		                                                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '3',	 'language_code' => 'en',	 'topic_review_status_type_id' => '2',		'name' => 'Rejected',	 		'description' => NULL,		                                                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '4',	 'language_code' => 'pt',	 'topic_review_status_type_id' => '2',		'name' => 'Rejeitado',	 		'description' => NULL,			                                            'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '5',	 'language_code' => 'en',	 'topic_review_status_type_id' => '3',	    'name' => 'Approved',	 		'description' => NULL,			                                            'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '6',	 'language_code' => 'pt',	 'topic_review_status_type_id' => '3',		'name' => 'Aprovado',	 		'description' => NULL,		                                                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
        );
        DB::table('topic_review_status_type_translations')->insert($topicReviewStatusTypesTranslations);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('topic_review_status_type_translations');
    }
}
