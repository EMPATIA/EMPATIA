<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParameterUserTypeKeyToEntityVatNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entity_vat_numbers', function (Blueprint $table) {
            if (!Schema::hasColumn('entity_vat_numbers', 'parameter_user_type_id')) {
                $table->integer('parameter_user_type_id')->after('entity_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entity_vat_numbers', function (Blueprint $table) {
            if (Schema::hasColumn('entity_vat_numbers', 'parameter_user_type_id')) {
                $table->integer('parameter_user_type_id');
            }
        });
    }
}
