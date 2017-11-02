<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountFieldsToTopics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('topics', function (Blueprint $table) {
            if (!Schema::hasColumn('topics', '_cached_data'))  {
                $table->mediumText('_cached_data')->nullable();
            }
            if (!Schema::hasColumn('topics', '_count_comments'))  {
                $table->integer('_count_comments')->nullable();
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
            if (Schema::hasColumn('topics', '_cached_data')) {
                $table->dropColumn('_cached_data');
            }
            if (Schema::hasColumn('topics', '_count_comments')) {
                $table->dropColumn('_count_comments');
            }
        });
    }
}
