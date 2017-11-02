<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActiveAndVersionToTechnicalAnalysesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('technical_analyses', function (Blueprint $table) {
            if (!Schema::hasColumn('technical_analyses', 'version')) {
                $table->integer('version')->after('sustainability');
            }
            if (!Schema::hasColumn('technical_analyses', 'active')) {
                $table->boolean('active')->after('version');
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
        Schema::table('technical_analyses', function (Blueprint $table) {
            if (Schema::hasColumn('technical_analyses', 'active')){
                $table->dropColumn('active');
            }
            if (Schema::hasColumn('technical_analyses', 'version')){
                $table->dropColumn('version');
            }
        });
    }
}
