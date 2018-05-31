<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAnonymizationRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_anonymization_requests', function (Blueprint $table) {
            $table->increments('id');
            
            $table->unsignedSmallInteger("process_status")->default(0);
            $table->text("log")->nullable();
            
            $table->string('created_by');
            $table->string('entity_key');
            $table->longText("user_keys");

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
        Schema::dropIfExists('user_anonymization_requests');
    }
}
