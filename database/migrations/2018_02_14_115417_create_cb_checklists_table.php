<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCbChecklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cb_checklists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('checklist_key');
            $table->string('title');
            $table->text('comments');
            $table->integer('position');
            $table->string('state');
            $table->string('entity_key')->nullable();
            $table->string('cb_key')->nullable();
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
        Schema::dropIfExists('cb_checklists');
    }
}
