<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTypeFormVarcharToLongtextInTranslationLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('translation_languages', function (Blueprint $table) {
            if (Schema::hasColumn('translation_languages', 'translation')) {
                $table->longText('translation')->change();
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
        Schema::table('translation_languages', function (Blueprint $table) {
            if (Schema::hasColumn('translation_languages', 'translation')) {
                $table->string('translation');
            }
        });
    }
}
