<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->unsignedInteger('id');
			$table->unsignedInteger('version')->default(1);
			$table->string('topic_key');
            $table->integer('cb_id')->unsigned();
			$table->unsignedInteger('parent_topic_id')->default(0);
            $table->string('created_by');
			$table->text('created_on_behalf')->nullable();
            $table->string('title');
            $table->text('contents');
			$table->text('summary')->nullable();
			$table->text('description')->nullable();
			$table->string('language_code')->nullable();
			$table->string('tag')->nullable();
			$table->boolean('active')->default(0);
			$table->boolean('moderate')->default(0);
			$table->string('moderated_by')->nullable();
            $table->boolean('blocked');
            $table->string('q_key')->nullable();
			$table->integer('topic_number')->unsigned();
			 $table->date('start_date')->nullable();
			$table->date('end_date')->nullable();			 

            $table->timestamps();
            $table->softDeletes();	
			

            //defines the primary keys
            $table->primary(['id', 'version']);
			
			
			
			
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('topics');
    }
}
