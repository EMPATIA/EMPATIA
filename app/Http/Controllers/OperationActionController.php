<?php

namespace App\Http\Controllers;

use App\OperationAction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ONE;

class OperationActionController extends Controller
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
            $operationActions = OperationAction::all();
            return response()->json($operationActions, 200);

        }catch(ModelNotFoundException $e){
            return response()->json(['error' => 'Operation Actions not found'], 500);
        }catch(QueryException $e){
            return response()->json(['error' => 'Failed to list Operation Actions'], 500);
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
            $operationAction = OperationAction::create([
                'code' => $request->json('code'),
                'name' => $request->json('name') ?? null,
                'description' => $request->json('description') ?? null
            ]);

            return response()->json($operationAction, 201);

        }
        catch(QueryException $e){
            return response()->json(['error' => 'Failed to store new Operation Action'], 500);
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
            $operationAction = OperationAction::whereCode($code)->firstOrFail();
            return response()->json($operationAction, 200);

        } catch(ModelNotFoundException $e){
            return response()->json(['error' => 'Operation Action not found'], 500);
        } catch(QueryException $e){
            return response()->json(['error' => 'Failed to store new Operation Action'], 500);
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
        try {
            $operationAction = OperationAction::whereCode($code)->firstOrFail();

            $operationAction->update([
                'code' => $request->json('code') ?? $operationAction->code,
                'name' => $request->json('name') ?? $operationAction->name,
                'description' => $request->json('description') ?? $operationAction->description
            ]);

            return response()->json($operationAction, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Operation Action not found'], 500);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to update new Operation Action'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
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
            $operationAction = OperationAction::whereCode($code)
                ->firstOrFail()
                ->delete();

            return response()->json('OK', 200);
        }
        catch(ModelNotFoundException $e){
            return response()->json(['error' => 'Operation Action not found'], 500);
        } catch(QueryException $e){
            return response()->json(['error' => 'Failed to update new Operation Action'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }
}
