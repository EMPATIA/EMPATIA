<?php
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigurationPermissionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuration_permission_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('created_by');
            $table->string('updated_by');
            $table->timestamps();
            $table->softDeletes();
        });

        $configurationPermissionTypes = array(
            array(
                "code" => 'user_Level_Permission_Topic',
                "created_by" => "defaultUSERprojectEMPATIA2016JAN",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            array(
                "code" => 'user_Level_Permission_Vote',
                "created_by" => "defaultUSERprojectEMPATIA2016JAN",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            array(
                "code" => 'user_Level_Permission_Comments',
                "created_by" => "defaultUSERprojectEMPATIA2016JAN",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            )
        );
        DB::table('configuration_permission_types')->insert($configurationPermissionTypes);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configuration_permission_types');
    }
}
