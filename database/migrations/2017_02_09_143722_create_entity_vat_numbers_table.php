<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntityVatNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_vat_numbers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entity_id')->unsigned();
            $table->string('vat_number');
			$table->string('name')->nullable();
			$table->string('surname')->nullable();
			$table->date('birthdate')->nullable();
			$table->string('birthplace')->nullable();
			$table->string('residential_address')->nullable();
			$table->string('gender')->nullable();

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
        Schema::dropIfExists('entity_vat_numbers');
    }
}
