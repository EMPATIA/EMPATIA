<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigurationPermissionTypeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuration_permission_type_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('configuration_permission_type_id')->unsigned();
            $table->string('language_code');
            $table->string('title');
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        $configurationPermissionTypeTranslations = array(
            array(
                'configuration_permission_type_id'  => 1,
                'language_code'     => 'en',
                'title'             => 'User Level Permission Topic'
            ),
            array(
                'configuration_permission_type_id'  => 2,
                'language_code'     => 'en',
                'title'             => 'User Level Permission Vote'
            ),
            array(
                'configuration_permission_type_id'  => 3,
                'language_code'     => 'en',
                'title'             => 'User Level Permission Comment'
            )
        );
        DB::table('configuration_permission_type_translations')->insert($configurationPermissionTypeTranslations);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configuration_permission_type_translations');
    }
}
