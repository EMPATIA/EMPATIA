<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCooperatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cooperators', function (Blueprint $table) {
            if (Schema::hasColumn('cooperators', 'type_id')) {
                $table->dropColumn('type_id');
            }
        });

        Schema::table('cooperators', function (Blueprint $table) {
            if(!Schema::hasColumn('cooperators', 'type_id')){
                $table->unsignedInteger('type_id')->nullable()->after('user_key');
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
        //

    }
}
