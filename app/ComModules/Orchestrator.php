<?php


namespace App\ComModules;

use App\One\One;
use Exception;

class Orchestrator
{

    /**
     * @param $parameterUserTypes
     * @return mixed
     */
    public static function verifyUniqueParameterUserType($parameterUserTypes)
    {
        $response = ONE::post([
            'component' => 'orchestrator',
            'api' => 'parameterUserType',
            'method' => 'verifyUniqueParameterUserTypes',
            'params' => [
                'parameter_user_type_keys' => $parameterUserTypes
                ]
        ]);
        if($response->statusCode()!= 200) {
            return false;
        }

        if(is_array($parameterUserTypes)){
            return $response->json()->data;
        }
        return $response->json();
    }

    public static function siteUsersToModerate($siteKey){

        $response = ONE::get([
            'component' => 'orchestrator',
            'api'       => 'level',
            'method'    => 'siteUsersToModerate',
            'params' => [
                'site_key' => $siteKey,
            ],
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetSiteUsersToModerate"));
        }
        return $response->json()->data;
    }

    /**
     *
     * Request Site Configurations to Orchestrator
     *
     * @param $siteKey
     * @return mixed
     * @throws Exception
     */
    public static function getSiteConfGroups($siteKey){

        $response = ONE::get([
            'component' => 'orchestrator',
            'api'       => 'siteConfGroup',
            'method'    => 'list',
            'params'    => [
                "siteKey" => $siteKey
            ],
        ]);
        if($response->statusCode() != 200) {
            return false;
        }
        return $response->json()->data;
    }

}
