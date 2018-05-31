<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeFieldsToParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parameters', function (Blueprint $table) {
            if (!Schema::hasColumn('parameters', 'topic_image'))  {
                $table->boolean('topic_image')->after('side');
            }
            if (!Schema::hasColumn('parameters', 'max_number_files'))  {
                $table->integer('max_number_files')->after('side')->unsigned();
            }
            if (!Schema::hasColumn('parameters', 'max_number_files_flag'))  {
                $table->boolean('max_number_files_flag')->after('side');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('parameters', function (Blueprint $table) {
            if (Schema::hasColumn('parameters', 'topic_image'))
            {
                $table->dropColumn('topic_image');
            }
            if (Schema::hasColumn('parameters', 'max_number_files'))
            {
                $table->dropColumn('max_number_files');
            }
            if (Schema::hasColumn('parameters', 'max_number_files_flag'))
            {
                $table->dropColumn('max_number_files_flag');
            }
        });
    }
}
