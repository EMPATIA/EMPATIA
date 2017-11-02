<?php

namespace App\ComModules;
use App\One\One;
use Exception;

class Auth
{
    /**
     * get users list
     *
     * @param $userKeys
     * @return mixed
     * @throws Exception
     */
    public static function listUser($userKeys) {
        $response = ONE::post([
            'component' => 'auth',
            'api'       => 'auth',
            'method'    => 'listUser',
            'params'    => [
                'userList' => $userKeys
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception('Error retrieving user information');
        }
        return $response->json();
    }
}