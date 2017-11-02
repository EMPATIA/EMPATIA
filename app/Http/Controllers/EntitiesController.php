<?php

namespace App\Http\Controllers;

use App\AuthMethod;
use App\Cb;
use App\ComModules\Notify;
use App\ComModules\Vote;
use App\Cooperator;
use App\Entity;
use App\EntityDomainName;
use App\EntityNotification;
use App\EntityNotificationType;
use App\EntityVatNumber;
use App\Layout;
use App\One\One;
use App\ParameterUserType;
use App\OrchUser;
use App\Topic;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Session\Session;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class EntitiesController
 * @package App\Http\Controllers
 */


/**
 * @SWG\Tag(
 *   name="Entity",
 *   description="Everything about Entity",
 * )
 *
 *  @SWG\Definition(
 *      definition="entityErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="Entity",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"entity_key", "country_id", "timezone_id", "currency_id", "name", "designation", "description", "url"},
 *           @SWG\Property(property="entity_key", format="string", type="string"),
 *           @SWG\Property(property="country_id", format="integer", type="integer"),
 *           @SWG\Property(property="timezone_id", format="integer", type="integer"),
 *           @SWG\Property(property="currency_id", format="integer", type="integer"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="designation", format="string", type="string"),
 *           @SWG\Property(property="description", format="string", type="string"),
 *           @SWG\Property(property="url", format="string", type="string")
 *      )
 *   }
 * )
 *
 */

class EntitiesController extends Controller
{
    protected $keysRequired = [
        'country_id',
        'timezone_id',
        'currency_id',
        'name',
        'url',
    ];

    /**
     * Request list of all Entities
     * Returns the list of all Entities
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function index(Request $request)
    {
        try {
            $entities = Entity::with('sites')->get();
            return response()->json(['data' => $entities], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Entities list'], 500);
        }

    }

    /**
     *
     *
     * @SWG\Definition(
     *    definition="replyEntity",
     *    required={"data"},
     *    @SWG\Property(
     *      property="data",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Entity")
     *      })
     *  )
     *
     * @SWG\Get(
     *  path="/entity/{entity_key}",
     *  summary="Show an Entity",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Entity"},
     *
     * @SWG\Parameter(
     *      name="entity_key",
     *      in="path",
     *      description="Entity Key",
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
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *
     *  @SWG\Response(
     *      response="200",
     *      description="Show the Entity data",
     *      @SWG\Schema(ref="#/definitions/replyEntity")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Entity not Found",
     *      @SWG\Schema(ref="#/definitions/entityErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Entity",
     *      @SWG\Schema(ref="#/definitions/entityErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Request of one Entity
     * Returns the attributes of the Entity
     * @param Request $request
     * @param $key
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function show(Request $request, $key)
    {
        $userKey = ONE::verifyToken($request);
        try {
            $entity = Entity::with('languages', 'sites', 'country', 'timezone', 'currency', 'layouts')->whereEntityKey($key)->firstOrFail();
            if (OrchUser::verifyRole($userKey, "admin") || OrchUser::verifyRole($userKey, "manager", $entity->id)) {

                return response()->json($entity, 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Entity'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Definition(
     *    definition="createEntity",

     *    @SWG\Property(
     *      property="entity",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Entity")
     *      }),
     *    @SWG\Property( property="layout_key", type="string", format="string")
     *  )
     *
     * @SWG\Definition(
     *    definition="replyCreateEntity",
     *    @SWG\Property(
     *      property="entity",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Entity")
     *      })
     *  )
     *
     * @SWG\Post(
     *  path="/entity",
     *  summary="Creation of a Entity",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Entity"},
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Entity data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/createEntity")
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
     *      response=201,
     *      description="the newly created entity",
     *      @SWG\Schema(ref="#/definitions/replyCreateEntity")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/entityErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Entity",
     *      @SWG\Schema(ref="#/definitions/entityErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a new Entity in the database
     * Return the Attributes of the Entity created
     * @param Request $request
     * @return static
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->keysRequired, $request);
        if (OrchUser::verifyRole($userKey, "admin")) {
            try {
                $key='';
                do {
                    $rand = str_random(32);

                    if (!($exists = Entity::whereEntityKey($rand)->exists())) {
                        $key = $rand;
                    }
                } while ($exists);

                $entity = Entity::create(
                    [
                        'entity_key' => $key,
                        'country_id' => $request->json('country_id'),
                        'timezone_id' => $request->json('timezone_id'),
                        'currency_id' => $request->json('currency_id'),
                        'name' => $request->json('name'),
                        'designation' => $request->json('designation') ?? '',
                        'description' => $request->json('description') ?? '',
                        'created_by' => $userKey,
                        'url' => $request->json('url')
                    ]
                );

                if(!empty($request->json('layout_key'))){
                    $layout = Layout::whereLayoutKey($request->json('layout_key'))->first();
                    $entity->layouts()->attach($layout->id);
                }

                return response()->json($entity, 201);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Layout not Found'], 404);
            }catch (Exception $e) {
                return response()->json(['error' => 'Failed to store new Entity'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Definition(
     *    definition="updateEntity",
     *    @SWG\Property(
     *      property="entity",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Entity")
     *      }),
     * @SWG\Property( property="layout_key", type="string", format="string")
     *  )
     *
     * @SWG\Definition(
     *    definition="replyUpdatedEntity",
     *    @SWG\Property(
     *      property="entity",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Entity")
     *      })
     *  )
     *
     * @SWG\Put(
     *  path="/entity/{entity_key}",
     *  summary="Update a Entity",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Entity"},
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Entity Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/updateEntity")
     *  ),
     *
     * @SWG\Parameter(
     *      name="entity_key",
     *      in="path",
     *      description="Entity Key",
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
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *
     *  @SWG\Response(
     *      response=200,
     *      description="The updated Entity",
     *      @SWG\Schema(ref="#/definitions/replyUpdatedEntity")
     *  ),
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/entityErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Entity not Found",
     *      @SWG\Schema(ref="#/definitions/entityErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Entity",
     *      @SWG\Schema(ref="#/definitions/entityErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Update a existing Entities
     * Return the Attributes of the Entity Updated
     * @param Request $request
     * @param $key
     * @return mixed
     */
    public function update(Request $request, $key)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);
        try {
            $entity = Entity::whereEntityKey($key)->firstOrFail();
            if (OrchUser::verifyRole($userKey, "admin") || OrchUser::verifyRole($userKey, "manager", $entity->id)) {

                $entity->country_id = $request->json('country_id');
                $entity->timezone_id = $request->json('timezone_id');
                $entity->currency_id = $request->json('currency_id');
                $entity->name = $request->json('name');
                $entity->designation = $request->json('designation') ?? '';
                $entity->description = $request->json('description') ?? '';
                $entity->url = $request->json('url');
                $entity->save();

                if(!empty($request->json('layout_key'))){
                    $layout = Layout::whereLayoutKey($request->json('layout_key'))->first();
                    $entity->layouts()->sync($layout->id);
                }

                return response()->json($entity, 200);

            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Entity'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     *  @SWG\Definition(
     *     definition="replyDeleteEntity",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/entity/{entity_key}",
     *  summary="Delete a Entity",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Entity"},
     *
     * @SWG\Parameter(
     *      name="entity_key",
     *      in="path",
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
     *  @SWG\Parameter(
     *      name="X-AUTH-TOKEN",
     *      in="header",
     *      description="Authentication Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=200,
     *      description="OK",
     *      @SWG\Schema(ref="#/definitions/replyDeleteEntity")
     *  ),
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Entity",
     *      @SWG\Schema(ref="#/definitions/entityErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/entityErrorDefault")
     *   ),
     *  @SWG\Response(
     *      response="404",
     *      description="Entity not Found'",
     *      @SWG\Schema(ref="#/definitions/entityErrorDefault")
     *  ),
     * )
     *
     */

    /**
     * Delete existing Entity
     * @param Request $request
     * @param $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $key)
    {
        $userKey = ONE::verifyToken($request);
        if (OrchUser::verifyRole($userKey, "admin")) {
            try {
                $entity = Entity::whereEntityKey($key)->firstOrFail();
                Entity::destroy($entity->id);
                return response()->json('Ok', 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Entity not Found'], 404);
            } catch (Exception $e) {
                return response()->json(['error' => 'Failed to delete Entity'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function addLanguage(Request $request)
    {
        ONE::verifyToken($request);
        try {
            if (!empty ($request->header('X-ENTITY-KEY'))) {
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            } else {
                $entity = Entity::whereEntityKey($request->json('entity_key'))->firstOrFail();
            }

            if(!$entity->languages()->exists()){
                $entity->languages()->attach($request->json('language_id'), ['default' => 1]);
            } else {
                if(!$entity->languages()->whereLanguageId($request->json('language_id'))->whereEntityId($entity->id)->exists()){
                    if($request->json('default') == 1){
                        $entity->languages()->updateExistingPivot($entity->languages()->whereDefault('1')->first()->id, ['default' => 0]);
                        $entity->languages()->attach($request->json('language_id'), ['default' => 1]);
                    } else {
                        $entity->languages()->attach($request->json('language_id'));
                    }
                }
            }
            $entity = Entity::with('languages')->findOrFail($entity->id);
            return response()->json($entity, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to Add Language'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function defaultLanguage(Request $request)
    {
        try {
            if (!empty($request->json('entity_key'))) {
                $entity = Entity::whereEntityKey($request->json('entity_key'))->firstOrFail();
            } else if (!empty($request->header('X-ENTITY-KEY'))) {
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            }

            if ($request->json('default') == 1) {
                $oldLangDefault = $entity->languages()->whereDefault('1')->first();
                if (!empty($oldLangDefault)) {
                    $entity->languages()->updateExistingPivot($oldLangDefault->id, ['default' => 0]);
                }
                $entity->languages()->updateExistingPivot($request->json('language_id'), ['default' => $request->json('default')]);
            }

            $entity = Entity::with('languages')->findOrFail($entity->id);
            return response()->json($entity, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Entity'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $entityKey
     * @param $langId
     * @return mixed
     */
    public function removeLanguage(Request $request, $entityKey, $langId)
    {
        ONE::verifyToken($request);

        try {
            if (!empty($entityKey)) {
                $entity = Entity::whereEntityKey($entityKey)->firstOrFail();
            } else if (!empty($request->header('X-ENTITY-KEY'))) {
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            }

            $language = $entity->languages()->whereLanguageId($langId)->firstOrFail();

            if(!$language->pivot->default){
                $entity->languages()->detach($langId);
                $entity = Entity::with('languages')->findOrFail($entity->id);
                return response()->json($entity, 200);
            } else {
                return response()->json(['error' => 'Tried to delete default Language'], 409);
            }

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Entity'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function addLayout(Request $request)
    {
        ONE::verifyToken($request);
        try {
            $entity = Entity::whereEntityKey($request->json('entity_key'))->firstOrFail();

            if (!empty($request->json('layout_key',"")))
                $layout = Layout::whereLayoutKey($request->json('layout_key'))->firstOrFail();
            else
                $layout = Layout::whereReference($request->json('layout_reference'))->firstOrFail();

            $entity->layouts()->attach($layout->id);

            $entity = Entity::with('layouts')->findOrFail($entity->id);
            return response()->json($entity, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Entity'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param Request $request
     * @param $entityKey
     * @param $layoutKey
     * @return mixed
     */
    public function removeLayout (Request $request, $entityKey, $layoutKey)
    {
        ONE::verifyToken($request);
        try {
            $entity = Entity::whereEntityKey($entityKey)->firstOrFail();
            $layout = Layout::whereLayoutKey($layoutKey)->firstOrFail();

            $entity->layouts()->detach($layout->id);
            $entity = Entity::with('layouts')->findOrFail($entity->id);

            return response()->json($entity, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to remove Layout'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addAuthMethod(Request $request)
    {
        ONE::verifyToken($request);
        try {
            $entity = Entity::whereEntityKey($request->json('entity_key'))->firstOrFail();
            $authMethod = AuthMethod::whereAuthMethodKey($request->json('authMethod_key'))->firstOrFail();
            $entity->authMethodEntities()->attach($authMethod->id);
            $entity = Entity::with('authMethodEntities')->findOrFail($entity->id);

            return response()->json($entity, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Entity'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $entityKey
     * @param $authMethodKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeAuthMethod (Request $request, $entityKey, $authMethodKey)
    {
        ONE::verifyToken($request);
        try {
            $entity = Entity::whereEntityKey($entityKey)->firstOrFail();
            $authMethod = AuthMethod::whereAuthMethodKey($authMethodKey)->firstOrFail();

            $entity->authMethodEntities()->detach($authMethod->id);
            $entity = Entity::with('authMethodEntities')->findOrFail($entity->id);

            return response()->json($entity, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to remove Authentication Method'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }



    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function entityParameters(Request $request)
    {
        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            $parameters = ParameterUserType::with('parameterType', 'parameterUserOptions')->whereEntityId($entity->id)->get();

            $primaryLanguage = $request->header('LANG-CODE');
            $defaultLanguage = $request->header('LANG-CODE-DEFAULT');

            foreach ($parameters as $parameter) {
                $parameter->newTranslation($primaryLanguage,$defaultLanguage);

                foreach ($parameter->parameterUserOptions as $parameterUserOption){
                    $parameterUserOption->newTranslation($primaryLanguage,$defaultLanguage);
                }
            }

            return response()->json(['data' => $parameters], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameters User Type not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function totalEntityUsers(Request $request)
    {
        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            $countUsers = $entity->users()->count();

            return response()->json($countUsers, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Total of Users'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * returns all the users that need manual verification to level up
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersAwaitingValidation(Request $request)
    {
        ONE::verifyToken($request);
        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            $sites = $entity->sites()->get();

            $users = [];

            foreach ($sites as $site){
                $siteUsers = [];

                $levelParameters = $site->levelParameters()->get();
                $manualLevel = $levelParameters->where('manual_verification', true)->first();

                $siteUsers['site_key'] = $site->key;
                $siteUsers['users'] = $levelParameters->where('position', ($manualLevel->position)-1)->first()->users()->pluck('user_key');

                $users[] = $siteUsers;
            }
            return response()->json(['data' => $users], 200);

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve User Level'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }



    /** -----------------------------------------------------------
     *  {BEGIN} Methods to deal with the entity registration values
     * ------------------------------------------------------------
     */

    /**
     * return the entity registration
     * values according to the requested type
     * @param Request $request
     * @return mixed
     * @internal param $entityKey
     */
    public function getEntityRegistrationValues(Request $request)
    {
        ONE::verifyToken($request);
        try {
            $entity = Entity::whereEntityKey($request['entity_key'])->firstOrFail();
            if($request['type'] == 'vat_numbers')
                $registrationFields = $entity->vatNumbers();
            else
                $registrationFields = $entity->domainNames();

            $tableData = $request->input('tableData') ?? null;
            $recordsTotal = $registrationFields->count();
            $query = $registrationFields;

            $query = $query->orderBy($tableData['order']['value'], $tableData['order']['dir']);

            if(!empty($tableData['search']['value'])) {
                $query = $query
                    ->where('vat_number', 'like', '%'.$tableData['search']['value'].'%')
                    ->orWhere('name', 'like', '%'.$tableData['search']['value'].'%')
                    ->orWhere('surname', 'like', '%'.$tableData['search']['value'].'%')
                    ->orWhere('birthdate', 'like', '%'.$tableData['search']['value'].'%')
                    ->orWhere('birthplace', 'like', '%'.$tableData['search']['value'].'%')
                    ->orWhere('residential_address', 'like', '%'.$tableData['search']['value'].'%');
            }

            $recordsFiltered = $query->count();

            $registrationFields = $query
                ->skip($tableData['start'])
                ->take($tableData['length'])
                ->get();

            $data['registrationValues'] = $registrationFields;
            $data['recordsTotal'] = $recordsTotal;
            $data['recordsFiltered'] = $recordsFiltered;

            return response()->json($data, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the vat numbers'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * stores the entity registration
     * values according to the requested type
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importRegistrationFields(Request $request)
    {
        ONE::verifyToken($request);
        try {
            $entity = Entity::whereEntityKey($request->json('entity_key'))->firstOrFail();
            $typeOfRegistration = $request->json('type');
            $added = 0;
            if($typeOfRegistration == 'vat_numbers'){
                foreach ($request->json('values') as $key=>$value){
                    if(!$entity->vatNumbers()->whereVatNumber($value['vat_number'])->exists()) {
                        EntityVatNumber::create([
                            'entity_id'  => $entity->id,
                            'vat_number' => $value['vat_number'],
                            'name' => !empty($value['name']) ? $value['name']: null,
                            'surname' => !empty($value['surname']) ? $value['surname']: null,
                            'birthdate' => !empty($value['birthdate']) ? Carbon::createFromFormat('m/d/Y' , $value['birthdate'])->toDateTimeString() :null,
                            'birthplace' => !empty($value['birthplace']) ? $value['birthplace']: null,
                            'residential_address' => !empty($value['residential_address']) ? $value['residential_address'] : null,
                            'gender' => !empty($value['gender']) ? $value['gender'] :null,
                        ]);
                        $added++;
                    }else{

                        $newEntry = EntityVatNumber::whereVatNumber($value['vat_number'])->firstOrFail();

                        $newEntry->name =  !empty($value['name']) ? $value['name']: null;
                        $newEntry->surname = !empty($value['surname']) ? $value['surname']: null;
                        $newEntry->birthdate =  !empty($value['birthdate']) ? Carbon::createFromFormat('m/d/Y' , $value['birthdate'])->toDateTimeString(): null;
                        $newEntry->birthplace =  !empty($value['birthplace']) ? $value['birthplace']: null;
                        $newEntry->residential_address =  !empty($value['residential_address']) ? $value['residential_address']: null;
                        $newEntry->gender =  !empty($value['gender']) ? $value['gender']: null;

                        $newEntry->save();

                        $added++;
                    }
                }
                $total = count($entity->vatNumbers()->get());
            }else{
                foreach ($request->json('values') as $value){
                    if(!$entity->domainNames()->whereDomainName($value['name'])->exists() and !$entity->domainNames()->whereDomainTitle($value['title'])->exists()) {
                        EntityDomainName::create([
                            'entity_id'  => $entity->id,
                            'domain_name' => $value['name'],
                            'domain_title' => $value['title'],
                        ]);
                        $added++;
                    }

                }
                $total = count($entity->domainNames()->get());
            }
            return response()->json(['message' => 'Imported: '.$added.' Total: '.$total], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to import Entity Registration values'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * deletes a entity registration
     * value according to a given type
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteRegistrationValues(Request $request)
    {
        ONE::verifyToken($request);
        try {
            $entity = Entity::whereEntityKey($request->json('entity_key'))->firstOrFail();
            $typeOfRegistration = $request->json('type');

            if($typeOfRegistration == 'vat_numbers'){
                $vatNumberId = $request->json('value_id');
                $vatNumber = $entity->vatNumbers()->whereId($vatNumberId)->first();
                EntityVatNumber::destroy($vatNumber->id);
            }else{
                $domainNameId =  $request->json('value_id');
                $domainName = $entity->domainNames()->whereId($domainNameId)->first();
                EntityDomainName::destroy($domainName->id);
            }
            return response()->json("OK", 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to import Entity Registration values'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /** ----------------------------------------------------------
     *  {END} Methods to deal with the entity registration values
     * -----------------------------------------------------------
     */


    /** ----------------------------------------------------------
     * Valitates a given Vat Number
     * @param Request $request
     * @param $vatNumber
     * @return \Illuminate\Http\JsonResponse
     */

    public function validateVatNumber(Request $request)
    {
        try {
            $vatNumber = $request->json('vat_number');
            $name = $request->json('name');
            $surname = $request->json('surname');
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

            $responseVatNumber = ($entity->vatNumbers()->whereVatNumber($vatNumber)->exists()) ? true : false;

            $responseName = ($entity->vatNumbers()->whereName($name)->whereSurname($surname)->exists()) ? true : false;

            return response()->json(['vat_number'=> $responseVatNumber, 'name' => $responseName], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to Validate Vat Number'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @internal param $domainName
     */
    public function validateDomainName(Request $request)
    {
        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

            if ($entity->domainNames()->count()>0)
                $response = ($entity->domainNames()->whereDomainName($request->json('domain_name'))->exists()) ? 1 : 0;
            else
                $response = -1;

            return response()->json($response, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to Validate Domain Name'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $entityKey
     * @param $requiredUserKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function addManager(Request $request, $requiredUserKey)
    {
        $userKey = ONE::verifyToken($request);
        try {

            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

            if (OrchUser::verifyRole($userKey, "admin") || OrchUser::verifyRole($userKey, "manager", $entity->id)) {

                $user = OrchUser::whereUserKey($requiredUserKey)->firstOrFail();

                if ($entity->users()->whereUserId($user->id)->exists()){
                    $entity->users()->updateExistingPivot($user->id, ['role' => 'manager']);
                };

                return response()->json($entity->users()->whereUserId($user->id)->first());
            }

            return response()->json('error');

            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEntityUserList(Request $request)
    {
        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

            $topicKey = $request->input('topicKey') ?? null;
            $tableData = $request->input('tableData') ?? null;
            $role = $request->json('role');

            if (is_null($role)) {
                $query = $entity->users()->where('role', '!=', 'admin');
            } else {
                $query = $entity->users()->whereRole($role);
            }

            if (!empty($topicKey)) {
                $topic = Topic::whereTopicKey($topicKey)->first();

                $cooperators = Cooperator::whereTopicId($topic->id)->pluck('user_key');

                $query = $query->whereNotIn('user_key', $cooperators);
            }
            $recordsTotal = $query->count();

            if(!empty($tableData['search']['value'])) {
                $usersFiltered = User::where('name', 'like', '%'.$tableData['search']['value'].'%')->pluck('user_key');

                $query = $query->whereIn('user_key', $usersFiltered);
            }

            if (!empty($tableData['start']))
                $query = $query->skip($tableData['start']);
            if (!empty($tableData["length"]))
                $query = $query->take($tableData['length']);
                
            $users = $query->get();

            $usersData = User::whereIn('user_key', $users->pluck('user_key'))->get();

            foreach ($users as $key=>$user){
                if ($usersData->where('user_key', '=', $user->user_key)->count()>0)
                    $user->name = $usersData->where('user_key', '=', $user->user_key)->first()->name;
            }
            $recordsFiltered = $users->count();

            $data['users'] = $users;
            $data['recordsTotal'] = $recordsTotal;
            $data['recordsFiltered'] = $recordsFiltered;

            return response()->json($data, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPublicEntityForNotify(Request $request, $key)
    {
        try {
            $entity = Entity::whereEntityKey($key)->select('name')->firstOrFail();

            return response()->json($entity, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get Public Entity'], 500);
        }
    }

    public function getEntityStatistics(Request $request)
    {
        try {

            $currentTime = time();
            $entity = Entity::
            whereEntityKey($request->header('X-ENTITY-KEY'))
                ->withCount('users')
                ->with('users')
                ->with('entityCbs')
                ->firstOrFail();


            $loggedUsers = User::whereIn('user_key',collect($entity->users)->pluck('user_key')->toArray())->where('timeout', '>', $currentTime)->count();

            $numberOfTopics = 0;
            $numberOfPosts = 0;
            foreach ($entity->entityCbs as $entityCb){
                $cb = CB::whereCbKey($entityCb->cb_key)->firstOrFail();
                $topics = $cb->topics()->with('firstPost')->get();
                $numberOfTopics += $topics->count();
                foreach ($topics as $topic){
                    $removedKeys[] = $topic->firstPost->id;
                }
                $numberOfPosts += $cb->posts()
                    ->whereNotIn('posts.id',$removedKeys)
                    ->where('posts.enabled', '=', 1)
                    ->where('posts.active', '=', 1)
                    ->count();
            }
            $data = array(
                'registeredUsers' => $entity->users_count,
                'loggedUsers' => $loggedUsers,
                'numberOfTopics' => $numberOfTopics,
                'numberOfPosts' => $numberOfPosts,

            );

            return response()->json(['data' => $data], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Model not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get Entity Statistics'], 500);
        }
    }

    public function getListOfAvailableUsersToSendEmails(Request $request)
    {
        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            $entityUsersKeys = $entity->users()->get()->pluck('user_key');
            $users = User::whereIn('user_key',$entityUsersKeys)->get();
            return response()->json(['data' => $users], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get Public Entity'], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNotificationTypes(Request $request){
        try{
            $notifications = EntityNotificationType::all()->keyBy('code');

            foreach ($notifications as $notification) {
                if (!($notification->translation($request->header('LANG-CODE')))) {
                    if (!$notification->translation($request->header('LANG-CODE-DEFAULT'))){
                        $translation = $notification->entityNotificationTypeTranslations()->first();
                        if(!empty($translation)){
                            $notification->translation($translation->language_code);
                        }
                    }
                }
            }
            return response()->json(['data' => $notifications], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity Notification Type not Found'], 404);
        } catch (Exception $e){
            return response()->json(['error' => 'Failed to get Notification Types'], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEntityNotifications(Request $request){
        try{
            $entityKey = $request->header('X-ENTITY-KEY');

            if ($entityKey){
                $entity = Entity::whereEntityKey($entityKey)->firstOrFail();
                $entityNotifications = $entity->entityNotifications()->with('entityNotificationType')->get()->keyBy('entityNotificationType.code');

                return response()->json(['data' => $entityNotifications], 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e){
            return response()->json(['error' => 'Failed to get Entity Notifications'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setEntityNotifications(Request $request){
        $userKey = ONE::verifyToken($request);
        try{
            $entityKey = $request->header('X-ENTITY-KEY');

//          Entity Notification Type Codes are the notifications that where selected for activation
            $entityNotificationTypeCodes = $request->json('entity_notification_type_codes');
            $groups = $request->json('groups');

            if ($entityKey){
                $entity = Entity::whereEntityKey($entityKey)->firstOrFail();
                if (OrchUser::verifyRole($userKey, "admin") || OrchUser::verifyRole($userKey, "manager", $entity->id)) {

                    $newEntityNotifications = collect();

                    //If there is no Entity Notification Type Codes, all of the Entity Notification are deactivated
                    if(!$entityNotificationTypeCodes){
                        $entity->entityNotifications()->update([
                            'active' => 0
                        ]);

                        return response()->json('OK', 201);
                    }

                    //If there is Entity Notification Type Codes, the correspondent Entity Notification will be updated ou created
                    foreach ($entityNotificationTypeCodes as $entityNotificationTypeCode) {
                        $entityNotificationType = EntityNotificationType::whereCode($entityNotificationTypeCode)->firstOrFail();
                        $entityNotification = $entity->entityNotifications()->whereEntityNotificationTypeId($entityNotificationType->id)->first();

                        if ($entityNotification){
                            $entityNotification->update([
                                'active' => 1,
                                'groups' => empty($groups[$entityNotificationTypeCode]) ? null : json_encode($groups[$entityNotificationTypeCode])
                            ]);

                            $newEntityNotifications->push($entityNotification->id);

                        } else {
                            $key = '';
                            do {
                                $rand = str_random(32);
                                if (!($exists = EntityNotification::whereEntityNotificationKey($rand)->exists())) {
                                    $key = $rand;
                                }
                            } while ($exists);

                            $entityNotification = $entity->entityNotifications()->create([
                                'entity_notification_key' => $key,
                                'entity_notification_type_id' => $entityNotificationType->id,
                                'template_key' => null,
                                'groups' => empty($groups[$entityNotificationTypeCode]) ? null : json_encode($groups[$entityNotificationTypeCode]),
                                'active' => 1,
                            ]);

                            $newEntityNotifications->push($entityNotification->id);
                        }
                    }
//                    deactivate the chosen Entity Notification
                    $entity->entityNotifications()->whereNotIn('id', $newEntityNotifications)->update([
                        'active' => 0
                    ]);

                    return response()->json('OK', 201);
                }
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e){
            return response()->json(['error' => 'Failed to get Entity Notifications'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setEntityNotificationTemplate(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        try{
            $entityKey = $request->header('X-ENTITY-KEY');

            if ($entityKey) {
                $entity = Entity::whereEntityKey($entityKey)->firstOrFail();

                if (OrchUser::verifyRole($userKey, "admin") || OrchUser::verifyRole($userKey, "manager", $entity->id)) {

                    $typeKey = Notify::getTypeKey('generic_entity_notifications');
                    $siteKey = $request->json('site_key');
                    $translations = $request->json('translations');
                    $notificationCode = $request->json('notification_code');

                    $entityNotificationType = EntityNotificationType::whereCode($notificationCode)->firstOrFail();

                    $entityNotification = $entity->entityNotifications()->whereEntityNotificationTypeId($entityNotificationType->id)->first();

                    if (isset($entityNotification)){
                        $template = Notify::entityNotificationTemplates($request, $typeKey, $siteKey, $translations);

                        $entityNotification->update([
                            'template_key' => $template->email_template_key
                        ]);
                    } else {
                        $template = Notify::entityNotificationTemplates($request, $typeKey, $siteKey, $translations);

                        $key = '';
                        do {
                            $rand = str_random(32);
                            if (!($exists = EntityNotification::whereEntityNotificationKey($rand)->exists())) {
                                $key = $rand;
                            }
                        } while ($exists);

                        $entityNotification = $entity->entityNotifications()->create([
                            'entity_notification_key' => $key,
                            'entity_notification_type_id' => $entityNotificationType->id,
                            'template_key' => $template->email_template_key,
                            'groups' => null,
                            'active' => 0,
                        ]);
                    }

                    return response()->json('OK', 201);
                }
            }
        }  catch (Exception $e){
            return response()->json(['error' => 'Failed to set Entity Notification Template'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $templateKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEntityNotificationTemplate(Request $request, $templateKey)
    {
        try{
            $emailTemplate = Notify::getEmailTemplateTranslations($templateKey);
            return response()->json($emailTemplate, 200);
        }  catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Email Template not Found'], 404);
        }catch (Exception $e){
            return response()->json(['error' => 'Failed to get Entity Notification Template'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $templateKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateEntityNotificationTemplate(Request $request, $templateKey)
    {
        $userKey = ONE::verifyToken($request);
        try{
            $entityKey = $request->header('X-ENTITY-KEY');
            if ($entityKey) {
                $entity = Entity::whereEntityKey($entityKey)->firstOrFail();

                if (OrchUser::verifyRole($userKey, "admin") || OrchUser::verifyRole($userKey, "manager", $entity->id)) {

                    $typeKey = Notify::getTypeKey('generic_entity_notifications');
                    $translations = $request->json('translations');

                    $emailTemplate = Notify::editEmailTemplate($request, $typeKey, $templateKey, $translations);

                    return response()->json($emailTemplate, 200);
                }
            }
        }  catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Email Template not Found'], 404);
        }catch (Exception $e){
            return response()->json(['error' => 'Failed to get Entity Notification Template'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function getEntityManagers(Request $request)
    {
        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

            $managers = $entity->users()->whereRole('manager')->get();

            $usersData = User::whereIn('user_key', $managers->pluck('user_key'))->get();

            foreach ($managers as $user) {
                $user->name = $usersData->where('user_key', '=', $user->user_key)->first()->name ?? 'unnamed';
            }

            return response()->json($managers, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get Public Entity'], 500);
        }
    }

    /**
     * @param Request $request
     * @param $entityKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function manualUpdateTopicVotesInfo(Request $request, $entityKey){
        $userKey = ONE::verifyLogin($request);

        try {
            $entity = Entity::whereEntityKey($entityKey)->firstOrFail();

            $cbs = $entity->entityCbs()->get();

            if ($cbs) {
                foreach ($cbs as $entityCb) {

                    $cb = Cb::whereCbKey($entityCb->cb_key)->with('votes')->firstOrFail();
                    $cbVotes = $cb->votes;

                    if ($cbVotes) {

                        $eventKeys = $cbVotes->pluck('vote_key');
                        $eventVotesByTopic = Vote::manualUpdateTopicVotesInfo($request, $eventKeys);

                        foreach ($eventVotesByTopic as $eventKey => $eventVotes) {

                            $allTopicKeys = [];
                            $topicKeys = collect(json_decode($eventVotes)->topics)->keys();
                            $topics = Topic::whereIn('topic_key', $topicKeys)->get();

                            $allTopicKeys = array_merge($allTopicKeys, $topicKeys->toArray());

                            foreach ($topics as $topic) {
                                $newVotes = json_decode($eventVotes)->topics->{$topic->topic_key};
                                $cachedData = json_decode($topic->_cached_data, true) ?? [];

                                if (isset($cachedData->votes->{$eventKey})) {
                                    $cachedData->votes->{$eventKey} = $newVotes;
                                    $topic->_cached_data = json_encode($cachedData);
                                    $topic->save();
                                } else {
                                    $votes = ['votes' => [$eventKey => $newVotes]];
                                    $cachedData = array_merge($cachedData, $votes);
                                    $topic->_cached_data = json_encode($cachedData);
                                    $topic->save();
                                }
                            }
                            $topicsNoVotes = $cb->topics()->whereNotIn('topic_key', $allTopicKeys)->get();

                            foreach ($topicsNoVotes as $topic){
                                $cachedData = json_decode($topic->_cached_data, true) ?? [];
                                if (!isset($cachedData->votes)){
                                    $votes = ['votes' => [$eventKey => ["neutral"=>0,"negative"=>0,"positive"=>0,"sum_negative"=>null,"sum_positive"=>null]]];
                                    $cachedData = array_merge($cachedData, $votes);
                                    $topic->_cached_data = json_encode($cachedData);
                                    $topic->save();
                                }
                            }
                        }
                    }
                }

                return response()->json(true, 200);
            }
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity or CB not found'], 404);
        }catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
            return response()->json(['error' => 'Failed to update Topic Vote Information'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
