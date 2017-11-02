<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ComponentsController extends Controller
{
    /**
     * Return the informations of
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        try {
            //NEED VERIFY COMPONENTS THAT ENTITY CAN USE
            $components =[
                'ANALYTICS' => env('COMPONENT-ANALYTICS'),
                'AUTH' => env('COMPONENT-AUTH'),
                'CB' => env('COMPONENT-CB'),
                'CM' => env('COMPONENT-CM'),
                'FILES' => env('COMPONENT-FILES'),
                'LOGS' => env('COMPONENT-LOGS'),
                'MP' => env('COMPONENT-MP'),
                'NOTIFY' => env('COMPONENT-NOTIFY'),
                'ORCHESTRATOR' => env('COMPONENT-ORCHESTRATOR'),
                'Q' => env('COMPONENT-Q'),
                'VOTE' => env('COMPONENT-VOTE'),
                'WUI' => env('COMPONENT-WUI'),
                'KIOSK' => env('COMPONENT-KIOSK'),
                'EVENTS' => env('COMPONENT-EVENTS'),
                'EMPATIA' => env('COMPONENT-EMPATIA'),

            ] ;
            return response()->json(["data" => $components], 200);
        }catch (Exception $e) {
            return response()->json(["Error" => 'Failed to get Components data'], 500);
        }
    }
}

