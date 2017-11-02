<?php

namespace App\Http\Controllers;

use App\Cb;
use App\Entity;
use App\OperationType;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ONE;

class OperationTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        try{
            $operationTypes = OperationType::all();
            return response()->json($operationTypes, 200);

        }catch(ModelNotFoundException $e){
            return response()->json(['error' => 'Operation Types not found'], 500);
        }catch(QueryException $e){
            return response()->json(['error' => 'Failed to list Operation Types'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
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
            $operationType = OperationType::create([
                'code' => $request->json('code'),
                'name' => $request->json('name') ?? null,
                'description' => $request->json('description') ?? null
            ]);

            return response()->json($operationType, 201);

        }
        catch(QueryException $e){
            return response()->json(['error' => 'Failed to store new Operation Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $code
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $code)
    {
        $userKey = ONE::verifyToken($request);
        try{
            $operationType = OperationType::whereCode($code)->firstOrFail();
            return response()->json($operationType, 200);

        }catch(ModelNotFoundException $e){
            return response()->json(['error' => 'Operation Type not found'], 500);
        }catch(QueryException $e){
            return response()->json(['error' => 'Failed to show new Operation Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $code
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $code)
    {
        $userKey = ONE::verifyToken($request);
        try{
            $operationType = OperationType::whereCode($code)->firstOrFail();

            $operationType->update([
                'code' => $request->json('code') ?? $operationType->code,
                'name' => $request->json('name') ?? $operationType->name,
                'description' => $request->json('description') ?? $operationType->description
            ]);

            return response()->json($operationType, 200);
        }
        catch(ModelNotFoundException $e){
            return response()->json(['error' => 'Operation Type not found'], 500);
        } catch(QueryException $e){
            return response()->json(['error' => 'Failed to update new Operation Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $code
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $code)
    {
        $userKey = ONE::verifyToken($request);
        try{
            $operationType = OperationType::whereCode($code)
                ->firstOrFail()
                ->delete();

            return response()->json('OK', 200);
        }
        catch(ModelNotFoundException $e){
            return response()->json(['error' => 'Operation Type not found'], 500);
        } catch(QueryException $e){
            return response()->json(['error' => 'Failed to update new Operation Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }
}
