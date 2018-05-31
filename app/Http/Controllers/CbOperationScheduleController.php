<?php

namespace App\Http\Controllers;

use App\Cb;
use App\CbOperationSchedule;
use App\Entity;
use App\EntityCb;
use App\OperationAction;
use App\OperationType;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ONE;

class CbOperationScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        try{
            //get Entity
            $entityKey = $request->header('X-ENTITY-KEY');
            $entity = Entity::whereEntityKey($entityKey)->firstOrFail();

            //get CB Key
            $cbKey = $request->json('cb_key');

            if (EntityCb::whereCbKey($cbKey)->whereEntityId($entity->id)->exists()) {

                //get CB
                $cb = Cb::whereCbKey($cbKey)->firstOrFail();

                //get Operation Type
                $operationTypeCode = $request->json('operation_type_code');
                $operationType = OperationType::whereCode($operationTypeCode)->first();

                //get Operation Action
                $operationActionCode = $request->json('operation_action_code');
                $operationAction = OperationAction::whereCode($operationActionCode)->first();

                //generate key
                do {
                    $rand = str_random(32);
                    if (!($exists = CbOperationSchedule::whereCbOperationScheduleKey($rand)->exists())) {
                        $key = $rand;
                    }
                } while ($exists);

                //create Operation Schedule
                $cbOperationSchedule = $cb->operationSchedules()->create([
                    'cb_operation_schedule_key' => $key,
                    'operation_type_id'         => $operationType->id ?? null,
                    'operation_action_id'       => $operationAction->id ?? null,
                    'type_code'                 => $request->json('operation_type_code') ?? null,
                    'action_code'               => $request->json('operation_action_code') ?? null,
                    'active'                    => $request->json('active') ?? 0,
                    'start_date'                => $request->json('start_date'), //may need to use Carbon string formatting
                    'end_date'                  => $request->json('end_date') //may need to use Carbon string formatting
                ]);

                return response()->json($cbOperationSchedule, 201);
            }
        } catch(ModelNotFoundException $e){
            return response()->json(['error' => 'Model not found'], 404);
        } catch(QueryException $e){
            return response()->json(['error' => 'Failed to store new Cb Operation Schedule', $e->getSql()], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $cbOperationScheduleKey
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $cbOperationScheduleKey)
    {
        $userKey = ONE::verifyToken($request);
        try{
            $cbOperationSchedule = CbOperationSchedule::whereCbOperationScheduleKey($cbOperationScheduleKey)->firstOrFail();
            return response()->json($cbOperationSchedule, 200);

        } catch(ModelNotFoundException $e){
            return response()->json(['error' => 'CB Operation Schedule not found'], 404);
        } catch(Exception $e){
            return response()->json(['error' => 'Failed to get CB Operation Schedule'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $cbOperationScheduleKey
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $cbOperationScheduleKey)
    {
        $userKey = ONE::verifyToken($request);
        try {
            $cbOperationSchedule = CbOperationSchedule::whereCbOperationScheduleKey($cbOperationScheduleKey)->firstOrFail();

            $cbOperationSchedule->update([
                'active'        => $request->json('active') ?? $cbOperationSchedule->active,
                'start_date'    => $request->json('start_date') ?? $cbOperationSchedule->start_date,
                'end_date'      => $request->json('end_date') ?? $cbOperationSchedule->end_date
            ]);

            return response()->json($cbOperationSchedule, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CB Operation Schedule not found'], 404);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to update CB Operation Schedule'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $cbOperationScheduleKey
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $cbOperationScheduleKey)
    {
        $userKey = ONE::verifyToken($request);
        try{
            $cbOperationSchedule = CbOperationSchedule::whereCbOperationScheduleKey($cbOperationScheduleKey)
                ->firstOrFail()
                ->delete();

            return response()->json('OK', 200);
        }
        catch(ModelNotFoundException $e){
            return response()->json(['error' => 'CB Operation Schedule not found'], 404);
        } catch(QueryException $e){
            return response()->json(['error' => 'Failed to delete CB Operation Schedule'], 500);
        } catch(Exception $e){
            return response()->json(['error' => 'Failed to delete CB Operation Schedule'], 500);

        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Get the specified resource group.
     *
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\Response
     */
    public function getCbSchedules(Request $request, $cbKey)
    {
        $userKey = ONE::verifyToken($request);
        try{
            $entityKey = $request->header('X-ENTITY-KEY');
            $entity = Entity::whereEntityKey($entityKey)->firstOrFail();

            if (EntityCb::whereCbKey($cbKey)->whereEntityId($entity->id)->exists()) {

                //get CB
                $cb = Cb::whereCbKey($cbKey)->firstOrFail();

                $cbOperationSchedules = $cb->operationSchedules()->with('operationType', 'operationAction')->get();

                return response()->json($cbOperationSchedules, 200);
            }

        } catch(ModelNotFoundException $e){
            return response()->json(['error' => 'CB Operation Schedule not found'], 404);
        } catch(Exception $e){
            return response()->json(['error' => 'Failed to get CB Operation Schedule'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * @param $entityKey
     * @param $cbKey
     * @param $operationTypeCode
     * @param $operationActionCode
     * @return bool|\Illuminate\Http\JsonResponse
     */
    private static function verifyCbOperationSchedule($entityKey, $cbKey, $operationTypeCode, $operationActionCode)
    {
        try{
            $entity = Entity::whereEntityKey($entityKey)->firstOrFail();

            if (EntityCb::whereCbKey($cbKey)->whereEntityId($entity->id)->exists()) {

                //get CB
                $cb = Cb::whereCbKey($cbKey)->firstOrFail();

                //get Operations
                $operationType = OperationType::whereCode($operationTypeCode)->firstOrFail();
                $operationAction = OperationAction::whereCode($operationActionCode)->firstOrFail();

                //get Cb Operation Schedule
                $cbOperationSchedule = $cb->operationSchedules()
                    ->whereOperationTypeId($operationType->id)
                    ->whereOperationActionId($operationAction->id)
                    ->active()
                    ->first();

                if (is_null($cbOperationSchedule)){
                    return response()->json(true, 200);
                }

                //Carbon time conversion
                $limitStartDate = Carbon::createFromTimestamp(strtotime($cbOperationSchedule->start_date));
                $limitEndDate = Carbon::createFromTimestamp(strtotime($cbOperationSchedule->end_date));

                $now = Carbon::now();

                //Verification
                if ($now->gte($limitStartDate) && $now->lte($limitEndDate)) {
                    return true;
                }
                return false;
            }

        } catch(ModelNotFoundException $e){
            return response()->json(['error' => 'Model not found'], 404);
        } catch(Exception $e){
            return response()->json(['error' => 'Failed to get CB Operation Schedule'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyScheduleExternal(Request $request)
    {
        try{
            $entityKey = $request->header('X-ENTITY-KEY');

            //get request data
            $cbKey = $request->json('cb_key');

            //get request data
            $operationTypeCode      = $request->json('operation_type_code');
            $operationActionCode    = $request->json('operation_action_code');

            $response = $this->verifyCbOperationSchedule($entityKey, $cbKey, $operationTypeCode, $operationActionCode);

            return response()->json($response, 200);

        } catch(Exception $e){
            return response()->json(['error' => 'Failed to get CB Operation Schedule'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }


    /**
     * @param $entityKey
     * @param $cbKey
     * @param $operationTypeCode
     * @param $operationActionCode
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public static function verifyScheduleInternal($entityKey, $cbKey, $operationTypeCode, $operationActionCode)
    {
        //deal with requests made from this component (EMPATIA)
        //when creating and/or updating topics/posts
        //to verify Operation Schedules

        try{
            return self::verifyCbOperationSchedule($entityKey, $cbKey, $operationTypeCode, $operationActionCode);

        } catch(Exception $e){
            return response()->json(['error' => 'Failed to verify Operation Schedule'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);

    }

    /**
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function operationSchedules(Request $request, $cbKey)
    {
        try{
            $cb = CB::whereCbKey($cbKey)->with([
                'operationSchedules' => function($query) {
                    $query->where("active","=","1")
                        ->where(function($q) {
                            $q->whereDate("start_date","<",Carbon::now())
                                ->WhereDate("end_date",">",Carbon::now());
                        });
                }
            ])->firstOrFail();

            /* Create Operation Schedules object */
            $operationSchedules = array();
            $operationTypes = OperationType::all();
            $operationActions = OperationAction::all();

            foreach ($operationTypes as $operationType) {
                $operationSchedules[$operationType->code] = array();

                foreach ($operationActions as $operationAction) {
                    $cbOperationSchedule = $cb->operationSchedules->where("operation_type_id",$operationType->id)->where("operation_action_id",$operationAction->id);
                    $operationSchedules[$operationType->code][$operationAction->code] = $cbOperationSchedule->isEmpty();
                }
            }

            $dynamicOperations = $cb->operationSchedules()
                ->where('operation_type_id', null)
                ->where('operation_action_id', null)
                ->where('active', 1)
                ->get();

            if ($dynamicOperations){
                foreach ($dynamicOperations as $dynamicOperation){
                    if ($dynamicOperation->start_date <= Carbon::now() && $dynamicOperation->end_date >= Carbon::now()){
                        $operationSchedules[$dynamicOperation->type_code][$dynamicOperation->action_code] = true;
                    } else {
                        $operationSchedules[$dynamicOperation->type_code][$dynamicOperation->action_code] = false;
                    }
                }
            }

            return response()->json($operationSchedules, 200);

        } catch(ModelNotFoundException $e){
            return response()->json(['error' => 'CB not found'], 404);
        } catch(Exception $e){
            return response()->json(['error' => 'Failed to get CB Operation Schedule'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }
}
