<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCbTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cb_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->string('cb_type_key')->unique;

            $table->timestamps();
            $table->softDeletes();
        });


        $cbTypes = array(
            [
                'code' => 'survey',
                'cb_type_key' => 'Zb96J994ZQqbLpSe2EBeEYq1RrYabM',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],[
                'code' => 'idea',
                'cb_type_key' => 'Zb96J994fsdfvfgnheEYq1RrYabM',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],[
                'code' => 'proposal',
                'cb_type_key' => 'Zb96J994f2435tghq1RrYabM',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],[
                'code' => 'forum',
                'cb_type_key' => 'Zb96J994ghuimq1RrYabM',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],[
                'code' => 'discussion',
                'cb_type_key' => 'Zb96J994ghui5y78rYabM',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],[
                'code' => 'tematicConsultation',
                'cb_type_key' => 'Zb96J994ghui444bM',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],[
                'code' => 'publicConsultation',
                'cb_type_key' => 'Zb9611111hui444bM',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],[
                'code' => 'project',
                'cb_type_key' => "dassaddaAAAqbLpSe2EBeEYq1RrYabM",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],[
                'code' => 'phase1',
                'cb_type_key' => "EYq1RrYabMdassaddaAAAqbLpSe2EBe",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],[
                'code' => 'phase2',
                'cb_type_key' => "",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],[
                'code' => 'phase3',
                'cb_type_key' => "EYq1RrYabMdassaddaAAAqbLpXXXSS",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],[
                'code' => 'qa',
                'cb_type_key' => "EYq1RrYabMdassOLSLSbLpXXXSS",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],[
                'code' => 'project_2c',
                'cb_type_key' => "1232399wsrverwFESxdfvfhgfssASDD",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        );
        DB::table('cb_types')->insert($cbTypes);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cb_types');
    }
}
