<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVoteStatisticsToCbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cbs', function (Blueprint $table) {
            if (!Schema::hasColumn('cbs', '_vote_statistics'))  {
                $table->text('_vote_statistics')->nullable();
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
            if (Schema::hasColumn('cbs', '_vote_statistics')) {
                $table->dropColumn('_vote_statistics');
            }
        });
    }
}
