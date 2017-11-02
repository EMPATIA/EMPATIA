<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTopicVersionIdToTopicParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('topic_parameters', function (Blueprint $table) {
            if (!Schema::hasColumn('topic_parameters', 'topic_version_id'))  {
                $table->integer('topic_version_id')->unsigned()->nullable()->after('parameter_id');
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
        Schema::table('topic_parameters', function (Blueprint $table) {
            if (Schema::hasColumn('topic_parameters', 'topic_version_id')) {
                $table->dropColumn('topic_version_id');
            }
        });
    }
}
