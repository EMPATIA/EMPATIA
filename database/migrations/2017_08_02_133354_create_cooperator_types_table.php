<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

class CreateCooperatorTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cooperator_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cooperator_type_key')->unique();
            $table->string('code');
            $table->timestamps();
            $table->softDeletes();
        });

        $cooperatorsTypes = array(
            array('id' => 1, 'cooperator_type_key' => 'fxxEjNw8vxr2hqsDaOtholB5X6m3qP93',   'code' => 'comment_only',   'created_at' => Carbon::now(), 'updated_at' => Carbon::now(), 'deleted_at' => NULL),
            array('id' => 2, 'cooperator_type_key' => '3ozQtclhgLRWj8fbHLQvChcTWW3sHSi6',   'code' => 'comment_edit',   'created_at' => Carbon::now(), 'updated_at' => Carbon::now(), 'deleted_at' => NULL)
        );
        DB::table('cooperator_types')->insert($cooperatorsTypes);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cooperator_types');
    }
}
