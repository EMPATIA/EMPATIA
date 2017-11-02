<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlagAttachmentTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flag_attachment_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('relation_id');
            $table->string('relation_type_code')->nullable();
            $table->string('language_code');
            $table->text('description')->nullable();
            $table->integer('version')->nullable();
            $table->boolean('active')->nullable();
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
        Schema::dropIfExists('flag_attachment_translations');
    }
}
