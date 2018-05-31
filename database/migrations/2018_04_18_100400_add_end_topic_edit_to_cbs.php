<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEndTopicEditToCbs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cbs', function (Blueprint $table) {
            if (!Schema::hasColumn('cbs', 'end_topic_edit')) {
                $table->dateTime('end_topic_edit')->after('start_topic_edit')->nullable();
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
            if (Schema::hasColumn('cbs', 'end_topic_edit')) {
                $table->dropColumn('end_topic_edit');
            }
        });
    }
}
