<?php

namespace App\Http\Controllers;

use App\CbType;
use App\EntityCb;
use Exception;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CbTypesController extends Controller
{
    /**
     * Request list of all CbTypes
     * Returns the list of all CbTypes
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function index(Request $request)
    {
        try{
            $cbTypes = CbType::all();
            return response()->json(['data' =>$cbTypes], 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Cb Types list'], 500);
        }
    }

    /**
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTypeByCb(Request $request, $cbKey)
    {
        try{
            $cbType = CbType::findOrFail(EntityCb::whereCbKey($cbKey)->firstOrFail()->cb_type_id);
            return response()->json($cbType, 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Cb Type'], 500);
        }
    }


    public function getTypesByCbKeys(Request $request)
    {
        try{
            $cbKeys = $request->cbKeys;
            $entityCbs = EntityCb::with("cbType")->whereIn("cb_key",$cbKeys)->get()->keyBy("cb_key")->pluck("cbType.code","cb_key");
            return response()->json($entityCbs, 200);
        }catch(Exception $e){
            dd($e->getMessage());
            return response()->json(['error' => 'Failed to retrieve the Cb Type'], 500);
        }
    }
}
