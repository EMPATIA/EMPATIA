<?php

use Illuminate\Database\Seeder;
use App\AuthMethod;
use Illuminate\Support\Facades\DB;

class AuthMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        $this->authMethods();

        DB::commit();
    }

    private function authMethods() {
        $authMethods = array(
            array(
                "id" 					 	=> 1,
                "auth_method_key"           => "KEb2ubskrl8EYHT5UD0ADBuL7iZuOpdG",
                "name"                      => "Empatia Authentication Method",
                "description"               => "Authentication Method of Empatia",
                "code" 					 	=> "auth_empatia",
            ),array(
                "id" 					 	=> 2,
                "auth_method_key"           => "KSYBafkbC6jmi44OPwp9vD1gu7rlYciD",
                "name"                      => "Facebook Login",
                "description"               => "Login with Facebook ",
                "code" 					 	=> "facebook",
            )
        );

        foreach ($authMethods as $authMethod) {

            AuthMethod::firstOrCreate($authMethod);
        }
    }
}
