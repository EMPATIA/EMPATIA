<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteEthicTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_ethic_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('site_ethic_type_key')->unique();
            $table->string('code')->unique();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });


        $types = [
            [
                'site_ethic_type_key' => 'Zb96J994ZQqbLpSe2EBeEYq1rtgb55',
                'code' => 'use_terms',
                'name'=> 'Use Terms',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'site_ethic_type_key' => 'Zb96J994ZQqbLpSe2EBeEYquytre55',
                'code' => 'privacy_policy',
                'name'=> 'Privacy Policy',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

        ];

        DB::table('site_ethic_types')->insert($types);


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_ethic_types');
    }
}
