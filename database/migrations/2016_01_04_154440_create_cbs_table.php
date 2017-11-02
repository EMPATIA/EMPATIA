<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cbs', function (Blueprint $table) {
            $table->increments('id');
			$table->string('cb_key')->unique();
            $table->integer('parent_cb_id')->nullable();
            $table->integer('status_id')->unsigned();
            $table->string('title');
            $table->text('contents');
			$table->string('tag')->nullable();
            $table->boolean('blocked');
            $table->string('created_by');
            $table->string('layout_code')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cbs');        
    }
}
