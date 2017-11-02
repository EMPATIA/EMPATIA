<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('currency', 255);
            $table->string('symbol_left', 12);
            $table->string('symbol_right', 12);
            $table->string('code', 3);
            $table->integer('decimal_place');
            $table->string('decimal_point', 3);
            $table->string('thousand_point', 3);
            $table->timestamps();

            $table->softDeletes();
        });

        $currencies = array(
            array('id' => '1',  'currency' => 'U.S. Dollar',			'symbol_left' => '$',	'symbol_right' => '',	'code' => 'USD',	'decimal_place' => '2',	'decimal_point' => '.',	'thousand_point' => ',',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '2',  'currency' => 'Euro',					'symbol_left' => '€',	'symbol_right' => '',	'code' => 'EUR',	'decimal_place' => '2',	'decimal_point' => '.',	'thousand_point' => ',',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '3',  'currency' => 'Pound Sterling',		    'symbol_left' => '£',	'symbol_right' => '',	'code' => 'GBP',	'decimal_place' => '2',	'decimal_point' => '.',	'thousand_point' => ',',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '4',  'currency' => 'Australian Dollar',	    'symbol_left' => '$',	'symbol_right' => '',	'code' => 'AUD',	'decimal_place' => '2',	'decimal_point' => '.',	'thousand_point' => ',',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '5',  'currency' => 'Canadian Dollar',		'symbol_left' => '$',	'symbol_right' => '',	'code' => 'CAD',	'decimal_place' => '2',	'decimal_point' => '.',	'thousand_point' => ',',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '6',  'currency' => 'Israeli New Sheqel',	    'symbol_left' => '?',	'symbol_right' => '',	'code' => 'ILS',	'decimal_place' => '2',	'decimal_point' => '.',	'thousand_point' => ',',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '7',  'currency' => 'Japanese Yen',			'symbol_left' => '¥',	'symbol_right' => '',	'code' => 'JPY',	'decimal_place' => '2',	'decimal_point' => '.',	'thousand_point' => ',',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '8',  'currency' => 'Mexican Peso',			'symbol_left' => '$',	'symbol_right' => '',	'code' => 'MXN',	'decimal_place' => '2',	'decimal_point' => '.',	'thousand_point' => ',',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '9',  'currency' => 'Norwegian Krone',		'symbol_left' => 'kr',	'symbol_right' => '',	'code' => 'NOK',	'decimal_place' => '2',	'decimal_point' => '.',	'thousand_point' => ',',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '10', 'currency' => 'New Zealand Dollar',	    'symbol_left' => '$',	'symbol_right' => '',	'code' => 'NZD',	'decimal_place' => '2',	'decimal_point' => '.',	'thousand_point' => ',',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '11', 'currency' => 'Swedish Krona',		    'symbol_left' => 'kr',	'symbol_right' => '',	'code' => 'SEK',	'decimal_place' => '2',	'decimal_point' => '.',	'thousand_point' => ',',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL),
            array('id' => '12', 'currency' => 'Swiss Franc',			'symbol_left' => 'CHF',	'symbol_right' => '',	'code' => 'CHF',	'decimal_place' => '2',	'decimal_point' => '.',	'thousand_point' => ',',	'created_at' => Carbon::now(),'updated_at' => Carbon::now(),'deleted_at' => NULL)
        );
        DB::table('currencies')->insert($currencies);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('currencies');
    }
}
