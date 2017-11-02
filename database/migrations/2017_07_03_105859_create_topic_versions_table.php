<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_versions', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('topic_id')->unsigned();
            $table->unsignedInteger('version')->default(1);
            $table->boolean('active')->default(0);
            $table->string('title');
            $table->text('contents');
            $table->text('summary')->nullable();
            $table->text('description')->nullable();
            $table->string('created_by');
            $table->string('active_by')->nullable();

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
        Schema::dropIfExists('topic_versions');
    }
}
