<?php

namespace App\Http\Controllers;

use App\CbType;
use App\Entity;
use App\EntityCb;
use App\Idea;
use App\Kiosk;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class KiosksController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Kiosk",
 *   description="Everything about Kiosks",
 * )
 *
 *  @SWG\Definition(
 *      definition="kioskErrorDefault",
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="kioskCreate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"title", "kiosk_type_id", "event_key"},
 *           @SWG\Property(property="cb_key", format="string", type="string"),
 *           @SWG\Property(property="title", format="string", type="string"),
 *           @SWG\Property(property="kiosk_type_id", format="string", type="string"),
 *           @SWG\Property(property="event_key", format="string", type="string")
 *        )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="kioskReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="kiosk_key", format="string", type="string"),
 *           @SWG\Property(property="entity_id", format="integer", type="integer"),
 *           @SWG\Property(property="entity_cb_id", format="string", type="string"),
 *           @SWG\Property(property="event_key", format="string", type="string"),
 *           @SWG\Property(property="kiosk_type_id", format="integer", type="integer"),
 *           @SWG\Property(property="title", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(
 *              property="entity_cb",
 *              type="array",
 *              @SWG\Items(ref="#/definitions/entityPadReply")
 *           ),
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *     definition="kioskDeleteReply",
 *     @SWG\Property(property="string", type="string", format="string")
 *  )
 */

class KiosksController extends Controller
{
    protected $keysRequired = [
        'title',
        'kiosk_type_id',
        'event_key'
    ];

    /**
     * Request the list of Kiosks
     * Returns the list of all Kiosks
     * 
     * @param Request $request
     * @return list of all
     */
    public function index(Request $request)
    {
        try{
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            $kiosks = $entity->kiosks()->get();

            return response()->json(["data" => $kiosks], 200);

        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Kiosks.'], 500);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/kiosk/{kiosk_key}",
     *  summary="Show a Kiosk",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Kiosk"},
     *
     * @SWG\Parameter(
     *      name="kiosk_key",
     *      in="path",
     *      description="Kiosk Key",
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
     *      response="200",
     *      description="Show the Kiosk data",
     *      @SWG\Schema(ref="#/definitions/kioskReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/kioskErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Kiosk not Found",
     *      @SWG\Schema(ref="#/definitions/kioskErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Kiosk",
     *      @SWG\Schema(ref="#/definitions/kioskErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Request of a Kiosk
     * Returns the attributes of the Kiosk
     * 
     * @param Request $request
     * @param $kioskKey
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     * @internal param $
     */
    public function show(Request $request, $kioskKey)
    {
        try{
            $kiosk = Kiosk::with("entityCb")->whereKioskKey($kioskKey)->firstOrFail();
            $kiosk->entityCb['cb_code'] = CbType::findOrFail($kiosk->entityCb->cb_type_id)->code;

            return response()->json($kiosk, 200);

        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Kiosk not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Kiosk'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/kiosk",
     *  summary="Create an Kiosk",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Kiosk"},
     *
     *  @SWG\Parameter(
     *      name="kiosk",
     *      in="body",
     *      description="Kiosk Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/kioskCreate")
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
     *      description="the newly created Kiosk",
     *      @SWG\Schema(ref="#/definitions/kioskReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/kioskErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Entity not found",
     *      @SWG\Schema(ref="#/definitions/kioskErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store Kiosk",
     *      @SWG\Schema(ref="#/definitions/kioskErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a new Kiosk in the database
     * Return the Attributes of the Kiosk created
     * 
     * @param Request $request
     * @return static
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try{
            do {
                $rand = str_random(32);
                if (!($exists = Kiosk::whereKioskKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);            
            
            if( !empty($request->json('cb_key')) ){
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

                $entityCb = EntityCb::whereCbKey($request->json('cb_key'))
                                    ->whereEntityId($entity->id)
                                    ->first();

                $kiosk = $entityCb->kiosks()->create(
                    [
                        'kiosk_key' => $key,
                        'entity_id' => $entity->id,
                        'title' => $request->json('title'),
                        'kiosk_type_id' => $request->json('kiosk_type_id'),
                        'event_key' => $request->json('event_key')
                    ]
                );

                $response = $kiosk->with("entityCb")->whereKioskKey($key)->firstOrFail();

            } else {
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

                $kiosk = $entity->kiosks()->create(
                     [
                         'kiosk_key' => $key,
                         'title' => $request->json('title'),
                         'kiosk_type_id' => $request->json('kiosk_type_id'),
                         'event_key' => $request->json('event_key')
                     ]
                 );
                $response = $kiosk;
            }
            return response()->json($response, 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to store new Kiosk'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/kiosk/{kiosk_key}",
     *  summary="Update an Kiosk",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Kiosk"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Kiosk Update Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/kioskCreate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="kiosk_key",
     *      in="path",
     *      description="Kiosk Key",
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
     *  @SWG\Parameter(
     *      name="X-ENTITY-KEY",
     *      in="header",
     *      description="Entity Key",
     *      required=true,
     *      type="string"
     *  ),
     *
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
     *      description="The updated Kiosk",
     *      @SWG\Schema(ref="#/definitions/kioskReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/kioskErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Kiosk not Found",
     *      @SWG\Schema(ref="#/definitions/kioskErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Kiosk",
     *      @SWG\Schema(ref="#/definitions/kioskErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Update an existing Kiosk.
     * Return the Attributes of the Kiosk Updated
     * 
     * @param Request $request
     * @param $kioskKey
     * @return mixed
     */
    public function update(Request $request, $kioskKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);
        try{
            
            if( !empty($request->json('cb_key')) ){
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();                
                
                $kiosk = Kiosk::whereKioskKey($kioskKey)->firstOrFail();

                $entityCb = EntityCb::whereCbKey($request->json('cb_key'))
                                    ->whereEntityId($entity->id)
                                    ->first();                

                $ideaTmp = $kiosk->entity_cb_id;
                
                $kiosk->entity_cb_id = $entityCb->id;
                $kiosk->title = $request->json('title');
                $kiosk->kiosk_type_id = $request->json('kiosk_type_id');  
                $kiosk->event_key = $request->json('event_key');

                $kiosk->save();

                
                if( ($kiosk->kiosk_type_id == 1) || ($kiosk->kiosk_type_id == 2 && $ideaTmp != $entityCb->entity_cb_id )){
                    $kiosk = Kiosk::whereKioskKey($kioskKey)->firstOrFail();
                    $kiosk->proposals()->delete();
                } 
                
                $kiosk = Kiosk::with("entityCb")->whereKioskKey($kioskKey)->firstOrFail();            
            } else {
                $kiosk = Kiosk::whereKioskKey($kioskKey)->firstOrFail();

                $kiosk->entity_cb_id = 0;
                $kiosk->title = $request->json('title');          
                $kiosk->kiosk_type_id = $request->json('kiosk_type_id');                        
                
                $kiosk->save();

                $kiosk = Kiosk::whereKioskKey($kioskKey)->firstOrFail();                   
            }
            return response()->json($kiosk, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Kiosk not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Kiosk'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @SWG\Delete(
     *  path="/kiosk/{kiosk_key}",
     *  summary="Delete a Kiosk",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Kiosk"},
     *
     * @SWG\Parameter(
     *      name="kiosk_key",
     *      in="path",
     *      description="Kiosk Key",
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
     *      @SWG\Schema(ref="#/definitions/kioskDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/kioskErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Kiosk not Found",
     *      @SWG\Schema(ref="#/definitions/kioskErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Kiosk",
     *      @SWG\Schema(ref="#/definitions/kioskErrorDefault")
     *  )
     * )
     */

    /**
     * Delete an existing Kiosk
     * 
     * @param Request $request
     * @param $kioskKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $kioskKey)
    {
        $userKey = ONE::verifyToken($request);
        try{
            $eventSchedule = Kiosk::whereKioskKey($kioskKey)->firstOrFail();
            $eventSchedule->delete();            

            return response()->json('OK', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Kiosk not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Kiosk'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param $kioskKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProposals($kioskKey){
        try{
            $kiosk = Kiosk::whereKioskKey($kioskKey)->firstOrFail();
            $proposals = $kiosk->proposals()->orderBy("position","asc")->get();
            return response()->json(["data" => $proposals], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e], 404);
        }catch (QueryException $e) {
            return response()->json(['error' => $e], 400);
        }        
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addProposal(Request $request){
        try{
            $kiosk = kiosk::whereKioskKey($request->json('kiosk_key'))->first();
            
            $position = $kiosk->proposals()->max('position');
            
            $kiosk->proposals()->create(
                [
                    'proposal_key' => $request->json('proposal_key'),
                    'position' => ++$position,
                ]
            );
            $kiosk = Kiosk::with("proposals")->whereKioskKey($request->json('kiosk_key'))->firstOrFail();            
            return response()->json(["data" => $kiosk], 200);

        }catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }        
    }

    /**
     * @param Request $request
     * @param $kioskKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeProposals(Request $request, $kioskKey)
    {
        try {
            $proposals = $request->json('proposals');
            $kiosk = Kiosk::whereKioskKey($kioskKey)->firstOrFail();

            $proposalsKeys = [];
            foreach ($proposals as $proposal) {
                $kioskProposals = $kiosk->proposals()->firstOrNew(['proposal_key' => $proposal["proposal_key"]]);
                $kioskProposals->position = $proposal["position"];
                $kioskProposals->save();

                // to delete in the end
                $proposalsKeys[] = $proposal["proposal_key"];
            }

            $kiosk->proposals()->whereNotIn('proposal_key', $proposalsKeys)->delete();

            return response()->json(["data" => $kiosk->proposals()->get()], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Kiosk not found'], 400);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store proposals'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param $kioskKey
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyProposal($kioskKey, $id){
        try{
            $kiosk = kiosk::whereKioskKey($kioskKey)->first();
            $kiosk->proposals()->findOrFail($id)->delete();       
            return response()->json('OK', 200);

        }catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }        
    }    
    
    /**
     * Reorder the proposals item in storage.
     * Returns the details of the updated proposals.
     * 
     * @param Request $request
     * @param $kioskKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function proposalsReorder(Request $request, $kioskKey)
    {
        ONE::verifyToken($request);
        try {
            $positions = $request->json('positions');  
                        
            foreach($positions as $position => $proposalId){
                  $kioskTmp = kiosk::whereKioskKey($kioskKey)->first();
                  $proposals = $kioskTmp->proposals()->findOrFail($proposalId);     
                  $proposals->position = $position;
                  $proposals->save();
            }
            
            $kiosk = Kiosk::with("proposals")->whereKioskKey($kioskKey)->firstOrFail();            
            return response()->json(["data" => $kiosk], 200);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e], 404);
        }          
        catch (QueryException $e) {
            return response()->json(['error' => $e], 400);
        }    
        
        return response()->json(['error' => 'Unauthorized' ], 401);        
    }     
    
}
