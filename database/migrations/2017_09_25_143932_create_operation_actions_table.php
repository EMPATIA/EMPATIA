<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperationActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operation_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $operationActions = array(
            array('code' => 'create',   'name' => 'Create', 'description' => NULL,  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('code' => 'update',   'name' => 'Update', 'description' => NULL,  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('operation_actions')->insert($operationActions);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operation_actions');
    }
}
