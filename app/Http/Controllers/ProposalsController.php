<?php

namespace App\Http\Controllers;

use App\Entity;
use App\One\One;
use App\Proposal;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class ProposalsController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Proposal",
 *   description="Everything about Proposals",
 * )
 *
 *  @SWG\Definition(
 *      definition="proposalErrorDefault",
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="proposalCreate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"code", "module", "api", "create", "view", "update", "delete"},
 *           @SWG\Property(property="role_key", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="module", format="string", type="string"),
 *           @SWG\Property(property="api", format="string", type="string"),
 *           @SWG\Property(property="create", format="boolean", type="boolean"),
 *           @SWG\Property(property="view", format="boolean", type="boolean"),
 *           @SWG\Property(property="update", format="boolean", type="boolean"),
 *           @SWG\Property(property="delete", format="boolean", type="boolean")
 *        )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="proposalReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="proposal_key", format="string", type="string"),
 *           @SWG\Property(property="cb_key", format="string", type="string"),
 *           @SWG\Property(property="entity_id", format="integer", type="integer"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *     definition="proposalDeleteReply",
 *     @SWG\Property(property="string", type="string", format="string")
 * )
 */

class ProposalsController extends Controller
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
                $proposals = $entity->proposals()->get();
            }
            else{
                $proposals = Proposal::all();
            }

            return response()->json(['data' => $proposals], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Proposals'], 500);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/proposal/{proposal_key}",
     *  summary="Show a Proposal",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Proposal"},
     *
     *  @SWG\Parameter(
     *      name="proposal_key",
     *      in="path",
     *      description="Proposal Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-ENTITY-KEY",
     *      in="header",
     *      description="Entity Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response="200",
     *      description="Show the Proposal data",
     *      @SWG\Schema(ref="#/definitions/proposalReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/proposalErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Proposal not Found",
     *      @SWG\Schema(ref="#/definitions/proposalErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Proposal",
     *      @SWG\Schema(ref="#/definitions/proposalErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param $proposalKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($proposalKey)
    {
        try{
            $proposal = Proposal::whereProposalKey($proposalKey)->firstOrFail();
            return response()->json($proposal, 200);

        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Proposal not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Proposal'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/proposals",
     *  summary="Create a Proposal",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Proposal"},
     *
     *  @SWG\Parameter(
     *      name="proposal",
     *      in="body",
     *      description="Proposal Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/proposalCreate")
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-AUTH-TOKEN",
     *      in="header",
     *      description="User Auth Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-ENTITY-KEY",
     *      in="header",
     *      description="Entity Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=201,
     *      description="the newly created Proposal",
     *      @SWG\Schema(ref="#/definitions/proposalReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/proposalErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Role not found",
     *      @SWG\Schema(ref="#/definitions/proposalErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store Proposal",
     *      @SWG\Schema(ref="#/definitions/proposalErrorDefault")
     *  )
     * )
     *
     */

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

                if (!($exists = Proposal::whereProposalKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            $proposal = $entity->proposals()->create(
                [
                    'proposal_key'  =>  $key,
                    'cb_key'        =>  $request->json('cb_key')
                ]
            );
            return response()->json($proposal, 201);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to store new Proposal'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/proposals/{proposal_key}",
     *  summary="Update a Proposal",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Proposal"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Proposal Update Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/proposalCreate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="proposal_key",
     *      in="path",
     *      description="Proposal Key",
     *      required=true,
     *      type="integer"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-AUTH-TOKEN",
     *      in="header",
     *      description="User Auth Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=200,
     *      description="The updated Proposal",
     *      @SWG\Schema(ref="#/definitions/proposalReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/proposalErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Proposal not Found",
     *      @SWG\Schema(ref="#/definitions/proposalErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Proposal",
     *      @SWG\Schema(ref="#/definitions/proposalErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @param $proposalKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $proposalKey)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);
        try{
            $proposal = Proposal::whereProposalKey($proposalKey)->firstOrFail();

            $proposal->cb_key = $request->json('cb_key');
            $proposal->save();

            return response()->json($proposal, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Proposal not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Proposal'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Delete(
     *  path="/proposal/{proposal_key}",
     *  summary="Delete a Proposal",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Proposal"},
     *
     * @SWG\Parameter(
     *      name="proposal_key",
     *      in="path",
     *      description="Proposal Key",
     *      required=true,
     *      type="integer"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-AUTH-TOKEN",
     *      in="header",
     *      description="User Auth Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=200,
     *      description="OK",
     *      @SWG\Schema(ref="#/definitions/proposalDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/proposalErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Proposal not Found",
     *      @SWG\Schema(ref="#/definitions/proposalErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Proposal",
     *      @SWG\Schema(ref="#/definitions/proposalErrorDefault")
     *  )
     * )
     */

    /**
     * @param Request $request
     * @param $proposalKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $proposalKey)
    {
        ONE::verifyToken($request);
        try{
            $proposal = Proposal::whereProposalKey($proposalKey)->firstOrFail();
            $proposal->delete();

            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Proposal not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Proposal'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
