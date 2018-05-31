<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEndTopicToCbs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cbs', function (Blueprint $table) {
            if (!Schema::hasColumn('cbs', 'end_topic')) {
                $table->dateTime('end_topic')->after('start_topic')->nullable();
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
        Schema::table('cbs', function (Blueprint $table) {
            if (Schema::hasColumn('cbs', 'end_topic')) {
                $table->dropColumn('end_topic');
            }
        });
    }
}
