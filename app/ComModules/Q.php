<?php
/**
 * Created by PhpStorm.
 * User: nelson
 * Date: 31/05/2017
 * Time: 12:40
 */

namespace App\ComModules;
use App\One\One;
use Exception;
use Illuminate\Http\Request;


class Q
{
    public static function getQuestionnaires(Request $request, $formKeys) {
        $response = ONE::post([
            'component' => 'q',
            'api'       => 'form',
            'method'    => 'getQuestionnaires',
            'headers'   => ['LANG-CODE: '.$request->header('LANG-CODE'), 'LANG-CODE-DEFAULT: '.$request->header('LANG-CODE-DEFAULT')],
            'params'    => [
                'forms_keys' => $formKeys
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception('Error retrieving questionnaires information');
        }
        return $response->json()->data;
    }
}
