<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('module_key')->unique();
            $table->string('name');
			$table->string('code');
            $table->string('token')->unique();

            $table->timestamps();

            $table->softDeletes();
        });

        $modules = array(
            array('id' => '1',	'module_key' => 'IU7tPOott0noF3SgCyLWLpIr2bp1U3',	'name' => 'Analytics',		'code' => 'analytics',		'token' => '18EsZwtuqKJ3F16XcnNhFgprdoNdKX',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '2',	'module_key' => 'Dkt1hUfWzuBYXAMCysTyi10vJNeHTa',	'name' => 'Auth',			'code' => 'auth',			'token' => 'Cm7tXirnvwPNJ3grAghc0HDT7MVAQe',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '3',	'module_key' => 'R3RDk1iqiAzEAD79yA0xYrWir4h8UJ',	'name' => 'CB',				'code' => 'cb',				'token' => 'M1mcNZe7XOkZdX7UTt5CSxm2Bhqwv1',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '4',	'module_key' => 'LpSe2EBeEYZb96J994ZQqbq1RrYabM',	'name' => 'CM',				'code' => 'cm',				'token' => 'eG0ZGydamWOvXAkKh7NTsQLkJw6Pd6',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '5',	'module_key' => 'fQBMQIdN76dJKzk9du8EOzxvuerpUR',	'name' => 'Files',			'code' => 'files',			'token' => 'HpE0oxY74Qla3p8WveSZ2HvOn4QAex',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '6',	'module_key' => 'ydyth1imsTod3C7Y3i41qako4pGQcC',	'name' => 'Logs',			'code' => 'logs',			'token' => 'Q9AipIxnIHKy7HFU7DsEiixEzHPJEg',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '7',	'module_key' => '6FZOB5C2RlYlTvuYXzavsRvl6cr4xp',	'name' => 'MP',				'code' => 'mp',				'token' => '2FK2MOtLvLOdtSN358rkNa3lcdBvs6',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '8',	'module_key' => 'pahrcQW7bqNSmzqUfeAoAXNfKTMDok',	'name' => 'Notify',			'code' => 'notify',			'token' => '4L0ITcy0A6j1EIPPRw4Zt2IVGy0kC9',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '9',	'module_key' => 'izpU5d99sjgNIYuXigUPoh54LAwDdr',	'name' => 'Orchestrator',	'code' => 'orchestrator',	'token' => 'k5BHG8ChQLjIw35WOW8CK768hHqKzi',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '10',	'module_key' => 'B8NLUW2s0H6wuljgmlHgnjgjN27bTU',	'name' => 'Q',				'code' => 'q',				'token' => 'JJeKqI6amnSHxQ25UtXrd9sJyursft',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '11',	'module_key' => 'SSnDv6kBSAjj8Ng0kwr7lhf52o3Gza',	'name' => 'Vote',			'code' => 'vote',			'token' => 'gW2NgAEmQY4EsYNIuzfVApDd3xKPH7',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '12',	'module_key' => 'poSAZprANHQiYq0KxyGuxXTq6N6Hgn',	'name' => 'WUI',			'code' => 'wui',			'token' => 'xx4DCBqsXBxsHNnO2IYEEGm3s5g5BW',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '13',	'module_key' => 'yuSAZprANHQiYq0KxyGuxXTq6N6Hgn',	'name' => 'KIOSK',			'code' => 'kiosk',			'token' => 'cc4DCBqsXBxsHNnO2IYEEGm3s5g545',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null),
            array('id' => '14',	'module_key' => 'trSAZprANHQiYq0KxyGuxXTq6N6Hgn',	'name' => 'Events',			'code' => 'events',			'token' => 'aa4DCBqsFGxsHNnO2IYEEGm3s5g5BW',	'created_at' => Carbon::now(),	'updated_at' => Carbon::now(), 'deleted_at' => null)
        );
        DB::table('modules')->insert($modules);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('modules');
    }
}
