<?php

namespace App\Http\Controllers;

use App\Entity;
use App\One\One;
use App\TematicConsultation;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class TematicConsultationsController extends Controller
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
        try {
            if (!empty($request->header('X-ENTITY-KEY'))) {
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first();
                $tematicConsultations = $entity->tematicConsultations()->get();
            } else {
                $tematicConsultations = TematicConsultation::all();
            }

            return response()->json(['data' => $tematicConsultations], 200);

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Tematic Consultations'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param $tematicConsultationKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($tematicConsultationKey)
    {
        try {
            $tematicConsultation = TematicConsultation::whereTematicConsultationKey($tematicConsultationKey)->firstOrFail();
            return response()->json($tematicConsultation, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Tematic Consultation not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Tematic Consultation'], 500);
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

        try {
            do {
                $rand = str_random(32);
                $key = "";

                if (!($exists = TematicConsultation::whereTematicConsultationKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            $tematicConsultation = $entity->tematicConsultations()->create(
                [
                    'tematic_consultation_key' => $key,
                    'cb_key' => $request->json('cb_key')
                ]
            );
            return response()->json($tematicConsultation, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Tematic Consultation'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $tematicConsultationKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $tematicConsultationKey)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try {
            $tematicConsultation = TematicConsultation::whereTematicConsultationKey($tematicConsultationKey)->firstOrFail();

            $tematicConsultation->cb_key = $request->json('cb_key');
            $tematicConsultation->save();

            return response()->json($tematicConsultation, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Tematic Consultation not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Tematic Consultation'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $tematicConsultationKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $tematicConsultationKey)
    {
        ONE::verifyToken($request);

        try {
            $tematicConsultation = TematicConsultation::whereTematicConsultationKey($tematicConsultationKey)->firstOrFail();
            $tematicConsultation->delete();

            return response()->json('Ok', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Tematic Consultation not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Tematic Consultation'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
