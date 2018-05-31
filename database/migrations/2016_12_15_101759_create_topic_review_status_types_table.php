<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

class CreateTopicReviewStatusTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_review_status_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('topic_review_status_type_key')->unique();
            $table->string('code')->unique();
            $table->integer('position')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });

        $topicReviewStatusTypes = array(
            array('id' => '1',	 'topic_review_status_type_key' => 'U3OY1PuTXCmv7xZ0Vd5Pt5Un6N82TScR',	 'code' => 'open',			'position' => '1',	 		'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '2',	 'topic_review_status_type_key' => 'OFaA4bJmDVaB07Xg5WzwdqYtSAlyvVYG',	 'code' => 'rejected',	 	'position' => '2',	 		'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
            array('id' => '3',	 'topic_review_status_type_key' => 'p0gkFeYUtmi8WacY6NK4xNXkqxNH5UAq',	 'code' => 'approved',		'position' => '3',	 		'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),	'deleted_at' => NULL),
        );
        DB::table('topic_review_status_types')->insert($topicReviewStatusTypes);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('topic_review_status_types');
    }
}
