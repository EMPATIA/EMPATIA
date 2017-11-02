<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateGroupTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('group_type_key')->unique();
            $table->string('code')->unique();
			$table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        $groupTypes = array(
            array('id' => '1',  'group_type_key' => 'XmtKO9DIWgZI5X8U5ovYvM3TAXHJCSor', 'code' => 'departments',    'name' => 'Departments',   'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('group_types')->insert($groupTypes);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('group_types');
    }
}
