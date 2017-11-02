<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->integer('id');
			$table->string('post_key');
            $table->integer('version')->default(1)->index();                        
            $table->integer('parent_id')->default(0);
            $table->integer('topic_id')->unsigned();
			$table->unsignedInteger('post_comment_type_id');
            $table->string('created_by');
            $table->text('contents');
            $table->tinyinteger('status_id');            
            $table->timestamps();
            $table->softDeletes();
            $table->tinyInteger('enabled')->default(false)->index();    
			$table->tinyInteger('blocked')->default(false)->index(); 
			$table->tinyInteger('active')->default(false)->index();  			
            $keys = array('id','version');
            $table->primary($keys);            
        });
        
        DB::statement('ALTER TABLE `posts` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('posts');
    }
}
