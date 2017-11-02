<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccessTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name');
            $table->timestamps();

            $table->softDeletes();
        });

        /*
         * Insert default Values
         */
        $access_types = array(
            array(
                "code" => "public",
                "name" => "Public",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            array(
                "code" => "public_private",
                "name" => "Private",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            array(
                "code" => "private_manager",
                "name" => "Private Manager",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ),
            array(
                "code" => "private_admin",
                "name" => "Private Admin",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            )
        );
        DB::table('access_types')->insert($access_types);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('access_types');
    }
}
