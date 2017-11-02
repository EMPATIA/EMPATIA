<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostCommentTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_comment_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('post_comment_type_key')->unique();
            $table->string('code');

            $table->timestamps();
            $table->softDeletes();
        });

        $postCommentTypes = array(
            array('id' => '1',	'post_comment_type_key' => '29rYpHbQAn22a21wbN4Z9y8L677877CW',	    "code" => "positive",   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',	'post_comment_type_key' => 'eVW52S9qCe05Uh7a64R227zmmVOcMGoa',		"code" => "neutral",    'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',	'post_comment_type_key' => 'dN2QmiO6kN8IaIWSsv9ETVgH87L7O1H5',		"code" => "negative",   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('post_comment_types')->insert($postCommentTypes);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_comment_types');
    }
}
