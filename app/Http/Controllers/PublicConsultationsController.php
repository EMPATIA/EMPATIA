<?php

namespace App\Http\Controllers;

use App\Entity;
use App\One\One;
use App\PublicConsultation;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PublicConsultationsController extends Controller
{
    protected $keysRequired = [
        'cb_key'
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try{
            if(!empty($request->header('X-ENTITY-KEY'))){
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first();
                $publicConsultations = $entity->publicConsultations()->get();
            }
            else{
                $publicConsultations = PublicConsultation::all();
            }
            return response()->json(['data' => $publicConsultations], 200);

        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Public Consultations'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param $publicConsultationKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($publicConsultationKey)
    {
        try{
            $publicConsultation = PublicConsultation::wherePublicConsultationKey($publicConsultationKey)->firstOrFail();
            return response()->json($publicConsultation, 200);

        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Public Consultation not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Public Consultation'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try{
            do {
                $rand = str_random(32);

                if (!($exists = PublicConsultation::wherePublicConsultationKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            $publicConsultation = $entity->publicConsultations()->create(
                [
                    'public_consultation_key'   =>  $key,
                    'cb_key'                    =>  $request->json('cb_key')
                ]
            );

            return response()->json($publicConsultation, 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to store new Public Consultation'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param Request $request
     * @param $public_consultation_key
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $publicConsultationKey)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);
        try{
            $publicConsultation = PublicConsultation::wherePublicConsultationKey($publicConsultationKey)->firstOrFail();

            $publicConsultation->cb_key    = $request->json('cb_key');
            $publicConsultation->save();

            return response()->json($publicConsultation, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Public Consultation not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Public Consultation'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param Request $request
     * @param $publicConsultationKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $publicConsultationKey)
    {
        ONE::verifyToken($request);
        try{
            $publicConsultation = PublicConsultation::wherePublicConsultationKey($publicConsultationKey)->firstOrFail();

            $publicConsultation->delete();

            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Public Consultation not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Public Consultation'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
