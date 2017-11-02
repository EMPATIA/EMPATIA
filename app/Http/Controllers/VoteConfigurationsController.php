<?php

namespace App\Http\Controllers;

use App\One\One;
use App\VoteConfiguration;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class VoteConfigurationsController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="VoteConfiguration",
 *   description="Everything about Vote Configurations",
 * )
 *
 *  @SWG\Definition(
 *      definition="voteConfigurationErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="voteConfigurationResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"vote_configuration_key", "code", "created_at", "updated_at"},
 *           @SWG\Property(property="vote_configuration_key", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="string", type="string"),
 *           @SWG\Property(property="updated_at", format="string", type="string"),
 *           @SWG\Property(property="translations", type="array",
 *						@SWG\Items(
 *                          type="object",
 *                          @SWG\Property(property="vote_configuration_id", type="integer"),
 *                          @SWG\Property(property="language_code", type="string"),
 *                          @SWG\Property(property="name", type="string"),
 *                          @SWG\Property(property="description", type="string"),
 *                          @SWG\Property(property="created_at", type="string"),
 *                          @SWG\Property(property="updated_at", type="string")
 *
 *					    )
 *          ),
 *     )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="voteConfiguration",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"code", "translations"},
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="translations", type="array",
 *              @SWG\Items(
 *                  type="object",
 *                  @SWG\Property(property="vote_configuration_id", type="integer"),
 *                  @SWG\Property(property="language_code", type="string"),
 *                  @SWG\Property(property="name", type="string"),
 *                  @SWG\Property(property="description", type="string"),
 *                  @SWG\Property(property="created_at", type="string"),
 *                  @SWG\Property(property="updated_at", type="string")
 *              ),
 *          )
 *      )
 *   }
 * )
 *
 */

class VoteConfigurationsController extends Controller
{
    protected $required = [
        'store' => ['code', 'translations'],
        'update' => ['code', 'translations']
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $voteConfigurations = VoteConfiguration::all();

            foreach ($voteConfigurations as $voteConfiguration) {
                if (!($voteConfiguration->translation($request->header('LANG-CODE')))) {
                    if (!$voteConfiguration->translation($request->header('LANG-CODE-DEFAULT')))
                        $voteConfiguration->translation('en');
                }
            }
            return response()->json(['data' => $voteConfigurations], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Vote Configurations list'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Get(
     *  path="/voteConfigurations/{voteConfigurationKey}",
     *  summary="Show a Vote Configuration",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"VoteConfiguration"},
     *
     * @SWG\Parameter(
     *      name="voteConfigurationKey",
     *      in="path",
     *      description="Vote Configuration Key",
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
     *      description="Show the Vote Configuration data",
     *      @SWG\Schema(ref="#/definitions/voteConfigurationResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Vote Configuration not Found",
     *      @SWG\Schema(ref="#/definitions/voteConfigurationErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Vote Configuration",
     *      @SWG\Schema(ref="#/definitions/voteConfigurationErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @param $voteConfigurationKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $voteConfigurationKey)
    {
        try {
            $voteConfiguration = VoteConfiguration::whereVoteConfigurationKey($voteConfigurationKey)->firstOrFail();

            if (!($voteConfiguration->translation($request->header('LANG-CODE')))) {
                if (!$voteConfiguration->translation($request->header('LANG-CODE-DEFAULT')))
                    return response()->json(['error' => 'No translation found'], 404);
            }

            return response()->json($voteConfiguration, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Vote Configuration not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $voteConfigurationKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $voteConfigurationKey)
    {
        try {
            $voteConfiguration = VoteConfiguration::whereVoteConfigurationKey($voteConfigurationKey)->firstOrFail();

            $voteConfiguration->translations();

            return response()->json($voteConfiguration, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Vote Configuration not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/voteConfigurations",
     *  summary="Creation of a Vote Configuration",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"VoteConfiguration"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Vote Configuration data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/voteConfiguration")
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
     *  @SWG\Parameter(
     *      name="X-ENTITY-KEY",
     *      in="header",
     *      description="Entity Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=201,
     *      description="the newly created vote configuration",
     *      @SWG\Schema(ref="#/definitions/voteConfigurationResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/voteConfigurationErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/voteConfigurationErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="409",
     *      description="Entity not Found",
     *      @SWG\Schema(ref="#/definitions/voteConfigurationErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Vote Configuraton",
     *      @SWG\Schema(ref="#/definitions/voteConfigurationErrorDefault")
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
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['store'], $request);

        if (ONE::verifyRoleAdmin($request, $userKey) == 'admin') {
            try {
                do {
                    $rand = str_random(32);
                    if (!($exists = VoteConfiguration::whereVoteConfigurationKey($rand)->exists())) {
                        $key = $rand;
                    }
                } while ($exists);

                $voteConfiguration = VoteConfiguration::create(
                    [
                        'code' => $request->json('code'),
                        'vote_configuration_key' => $key,
                    ]
                );

                foreach ($request->json('translations') as $translation) {
                    if (isset($translation['language_code']) && isset($translation['name'])) {
                        $voteConfiguration->voteConfigurationTranslations()->create(
                            [
                                'language_code' => $translation['language_code'],
                                'name'          => $translation['name'],
                                'description'   => empty($translation['description']) ? null : $translation['description']
                            ]
                        );
                    }
                }

                return response()->json($voteConfiguration, 201);
            } catch (QueryException $e) {
                return response()->json(['error' => 'Failed to store new Vote Configuration'], 500);
            }
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     *
     * @SWG\Put(
     *  path="/voteConfigurations/{voteConfigurationKey}",
     *  summary="Update a Vote Configuration",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"VoteConfiguration"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Vote Configuration Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/voteConfiguration")
     *  ),
     *
     * @SWG\Parameter(
     *      name="voteConfigurationKey",
     *      in="path",
     *      description="Vote Configuration Key",
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
     *  @SWG\Response(
     *      response=200,
     *      description="The updated Vote Configuration",
     *      @SWG\Schema(ref="#/definitions/voteConfigurationResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/voteConfigurationErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Vote Configuration not Found",
     *      @SWG\Schema(ref="#/definitions/voteConfigurationErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Vote Configuration",
     *      @SWG\Schema(ref="#/definitions/voteConfigurationErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @param $voteConfigurationKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $voteConfigurationKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['update'], $request);

        if (ONE::verifyRoleAdmin($request, $userKey) == 'admin') {
            try {
                $translationsOld = [];
                $translationsNew = [];

                $voteConfiguration = VoteConfiguration::whereVoteConfigurationKey($voteConfigurationKey)->firstOrFail();

                $voteConfiguration->code = $request->json('code');
                $voteConfiguration->save();

                $translationsId = $voteConfiguration->voteConfigurationTranslations()->get();
                foreach ($translationsId as $translationId) {
                    $translationsOld[] = $translationId->id;
                }

                foreach ($request->json('translations') as $translation) {
                    if (isset($translation['language_code']) && isset($translation['name'])) {
                        $voteConfigurationTranslation = $voteConfiguration->voteConfigurationTranslations()->whereLanguageCode($translation['language_code'])->first();
                        if (empty($voteConfigurationTranslation)) {
                            $voteConfigurationTranslation = $voteConfiguration->voteConfigurationTranslations()->create(
                                [
                                    'language_code' => $translation['language_code'],
                                    'name'          => $translation['name'],
                                    'description'   => empty($translation['description']) ? null : $translation['description']
                                ]
                            );
                        } else {
                            $voteConfigurationTranslation->name = $translation['name'];
                            $voteConfigurationTranslation->description = empty($translation['description']) ? null : $translation['description'];
                            $voteConfigurationTranslation->save();
                        }
                    }
                    $translationsNew[] = $voteConfigurationTranslation->id;
                }

                $deleteTranslations = array_diff($translationsOld, $translationsNew);
                foreach ($deleteTranslations as $deleteTranslation) {
                    $deleteId = $voteConfiguration->voteConfigurationTranslations()->whereId($deleteTranslation)->first();
                    $deleteId->delete();
                }

                return response()->json($voteConfiguration, 200);
            } catch (QueryException $e) {
                return response()->json(['error' => 'Failed to update Vote Configuration'], 500);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Vote Configuration not Found'], 404);
            }
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeleteVoteConfiguration",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/voteConfigurations/{voteConfigurationKey}",
     *  summary="Delete a Vote Configuration",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"VoteConfiguration"},
     *
     * @SWG\Parameter(
     *      name="voteConfigurationKey",
     *      in="path",
     *      description="Vote Configuration Key",
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
     *  @SWG\Parameter(
     *      name="X-ENTITY-KEY",
     *      in="header",
     *      description="Entity Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=200,
     *      description="OK",
     *      @SWG\Schema(ref="#/definitions/replyDeleteVoteConfiguration")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/voteConfigurationErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="VoteConfiguration not Found",
     *      @SWG\Schema(ref="#/definitions/voteConfigurationErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete VoteConfiguration",
     *      @SWG\Schema(ref="#/definitions/voteConfigurationErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @param $voteConfigurationKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $voteConfigurationKey)
    {
        $userKey = ONE::verifyToken($request);

        if (ONE::verifyRoleAdmin($request, $userKey) == 'admin') {
            try {
                $voteConfiguration = VoteConfiguration::whereVoteConfigurationKey($voteConfigurationKey)->firstOrFail();

                $voteConfigurationTranslations = $voteConfiguration->voteConfigurationTranslations()->get();
                foreach ($voteConfigurationTranslations as $voteConfigurationTranslation) {
                    $voteConfigurationTranslation->delete();
                }

                $voteConfiguration->delete();

                return response()->json('OK', 200);
            } catch (QueryException $e) {
                return response()->json(['error' => 'Failed to delete a Vote Configuration'], 500);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Vote Configuration not Found'], 404);
            }
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
