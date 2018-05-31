<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCachedVotesToTopics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('topics', function (Blueprint $table) {
            if (!Schema::hasColumn('topics', '_cached_votes'))  {
                $table->mediumText('_cached_votes')->nullable();
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
        Schema::table('topics', function (Blueprint $table) {
            if (Schema::hasColumn('cbs', '_cached_votes')) {
                $table->dropColumn('_cached_votes');
            }
        });
    }
}
