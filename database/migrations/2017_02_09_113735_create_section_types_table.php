<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSectionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('section_type_key')->unique();
            $table->string('code');
			$table->boolean('translatable')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });

        $sectionTypes = array(
            array('id' => '1',    'section_type_key' => 'Y8t2acz5guyEAnDrKHAO7Wy1ADNcyr73', 'code' => 'titleSectionDEPRECATED', 'translatable' => '1',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => Carbon::now()),
            array('id' => '2',    'section_type_key' => '3tHJ5TJ369bLBfeUHvfOj65SggZOLxZF', 'code' => 'contentSection',         'translatable' => '1',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',    'section_type_key' => 'V85jrSv1tgwN1zue5dVbHL9XiDlKsIIm', 'code' => 'singleImageSection',     'translatable' => '1',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '4',    'section_type_key' => 'MxjHVy3pqjWaeVzmSGwyfGMQhNhc287u', 'code' => 'multipleImagesSection',  'translatable' => '1',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '5',    'section_type_key' => 'H6Jw4cz1nJpziHqG7hW1qejXftBitsSB', 'code' => 'slideShowSection',       'translatable' => '1',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '6',    'section_type_key' => 'SPPtlhEaGZc8WMJ9vqhhgP8m91NIRCVB', 'code' => 'singleFileSection',      'translatable' => '1',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '7',    'section_type_key' => '2Qli2FadzHRVNeHrnYYdycffWSb24pfN', 'code' => 'multipleFilesSection',   'translatable' => '1',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '8',    'section_type_key' => 'HgvvJBSlbxgMO58UZR4dcfpD8yuSOLgq', 'code' => 'externalVideoSection',   'translatable' => '1',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '9',    'section_type_key' => 'wGcFtUfu6SkXbryaFifsqMRXESvvFi2K', 'code' => 'internalVideoSection',   'translatable' => '1',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '10',   'section_type_key' => 'a9q1buDVfUs8zGaBGTSPdYqxWPdSgW2s', 'code' => 'padsList',               'translatable' => '0',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '11',   'section_type_key' => 'iJoPZRWvnSKBf2LrQWHLaKQEYmzKxrlL', 'code' => 'headingSection',         'translatable' => '1',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '12',   'section_type_key' => 'FhASZJLwi1w1EFP88zHpUrYETcAxhfMB', 'code' => 'bannerSection',          'translatable' => '1',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '13',   'section_type_key' => 'tqGf8D11LRRRxITmcoxaCiWUtQYgXkRa', 'code' => 'contentsList',           'translatable' => '0',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '14',   'section_type_key' => 'j8XNsTgK8t0BtCSIJ89mRrgE0tAEpGOA', 'code' => 'linkedBanner',           'translatable' => '1',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '15',   'section_type_key' => 'AHHiW6ZsE46iHQsSvShMV2jS9q0Z6yFO', 'code' => 'buttonSection',          'translatable' => '1',  'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('section_types')->insert($sectionTypes);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('section_types');
    }
}
