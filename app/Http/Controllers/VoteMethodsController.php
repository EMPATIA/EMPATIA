<?php

namespace App\Http\Controllers;

use App\Entity;
use App\One\One;
use App\OrchUser;
use App\VoteMethod;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class VoteMethodsController extends Controller
{
    protected $keysRequired = [
        'vote_method_id',
        'entity_id'
    ];

    /**
     * Request list of all Vote Method
     * Returns the list of all Vote methods
     * @param
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function index(Request $request)
    {

        try{
            $voteMethods = VoteMethod::with('entity')->get();
            return response()->json(['data' => $voteMethods], 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Vote Methods list'], 500);
        }

    }

    /**
     * Request of one Vote  Method
     * Returns the attributes of the Vote Method
     * @param Request $request
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function show(Request $request, $id)
    {
        $userKey = ONE::verifyToken($request);
        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first();
            if (empty($entity)) {
                return response()->json(['error' => 'Entity not found'], 404);
            }
            if (OrchUser::verifyRole($userKey, "admin") || OrchUser::verifyRole($userKey, "manager")){

                $voteMethod = VoteMethod::findOrFail($id);
                return response()->json($voteMethod, 200);
            }
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Vote Method not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Vote Method'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Store a new Vote Method in the database
     * Return the Attributes of the Vote Method created
     * @param Request $request
     * @return static
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->keysRequired, $request);
        try{
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

            if (OrchUser::verifyRole($userKey, "admin") || OrchUser::verifyRole($userKey, "manager",$entity->id)){

                $voteMethod = $entity->voteMethods()->create(
                    [
                        'vote_method_id'    => $request->json("vote_method_id"),
                        'created_by'        => $userKey
                    ]
                );

                return response()->json($voteMethod, 201);

            }
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to store new Vote Method'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Update a existing Vote Method
     * Return the Attributes of the Vote Method Updated
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);
        try{
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first();
            if (empty($entity)) {
                return response()->json(['error' => 'Entity not found'], 404);
            }
            if (OrchUser::verifyRole($userKey, "admin") || OrchUser::verifyRole($userKey, "manager")){

                $voteMethod = VoteMethod::findOrFail($id);
                $voteMethod->vote_method_id = $request->json("vote_method_id");
                $voteMethod->entity_id = $request->json("entity_id");

                $voteMethod->save();

                return response()->json($voteMethod, 200);

            }
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Vote Method not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Vote Method'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Delete existing Vote Method
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $userKey = ONE::verifyToken($request);
        try{
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first();
            if (empty($entity)) {
                return response()->json(['error' => 'Entity not found'], 404);
            }
            if ( (OrchUser::verifyRole($userKey, "admin")) || (OrchUser::verifyRole($userKey, "manager",$entity->id))){

                VoteMethod::destroy($id);
                return response()->json('Ok', 200);

            }
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Vote Method not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Vote Method'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }
}
