<?php

namespace App\Http\Controllers;

use App\ConfigurationOption;
use App\Configuration;
use App\ConfigurationType;
use App\One\One;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Exception;

/**
 * Class ConfigurationsController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Configuration",
 *   description="Everything about Configurations",
 * )
 *
 *  @SWG\Definition(
 *      definition="configurationErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="configurationResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"code"},
 *           @SWG\Property(property="code", format="string", type="string"),
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="configurations",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"configuration_type_id", "code", "created_by"},
 *           @SWG\Property(property="configuration_type_id", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="translations", type="array",
 *						@SWG\Items(
 *                          type="object",
 *                          @SWG\Property(property="language_code", type="string"),
 *                          @SWG\Property(property="ttitle", type="string"),
 *                          @SWG\Property(property="description", type="string")
 *
 *					    )
 *          ),
 *       )
 *   }
 * )
 *
 */

class ConfigurationsController extends Controller
{
    protected $required = [
        'store'     => ['code', 'configuration_type_id', 'translations'],
        'update'    => ['code', 'configuration_type_id', 'translations']
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $configurations = Configuration::all();

            foreach ($configurations as $configuration) {
                if (!($configuration->translation($request->header('LANG-CODE')))) {
                    if (!$configuration->translation($request->header('LANG-CODE-DEFAULT')))
                        return response()->json(['error' => 'No translation found'], 404);
                }
            }
            return response()->json(['data' => $configurations], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Configurations list'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Get(
     *  path="/configurations/{id}",
     *  summary="Show a Configuration",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Configuration"},
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      description="Configuration Id",
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
     *      name="LANG_CODE",
     *      in="header",
     *      description="Lang Code",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response="200",
     *      description="Show the Configuration data",
     *      @SWG\Schema(ref="#/definitions/configurationResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Configuration not Found",
     *      @SWG\Schema(ref="#/definitions/configurationErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Configuration",
     *      @SWG\Schema(ref="#/definitions/configurationErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Request a specific Configuration.
     * Returns the details of a specific Configuration.
     *
     * @param $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        try {
            $configuration = Configuration::findOrFail($id);

            if (!($configuration->translation($request->header('LANG-CODE')))) {
                if (!$configuration->translation($request->header('LANG-CODE-DEFAULT')))
                    return response()->json(['error' => 'No translation found'], 404);
            }

            return response()->json($configuration, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Configuration not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $id)
    {
        try {
            $configuration = Configuration::findOrFail($id);

            $configuration->translations();

            return response()->json($configuration, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Configuration not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/configurations",
     *  summary="Creation of a Configuration",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Configuration"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Configuration data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/configurations")
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
     *      description="the newly created Configuration",
     *      @SWG\Schema(ref="#/definitions/configurationResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/configurationErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/configurationErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="409",
     *      description="Entity not Found",
     *      @SWG\Schema(ref="#/definitions/configurationErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Configuration",
     *      @SWG\Schema(ref="#/definitions/configurationErrorDefault")
     *  )
     * )
     *
     */
    /**
     * Store a newly created Configuration in storage.
     * Returns the details of the newly created Configuration.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['store'], $request);

        $configurationType = ConfigurationType::findOrFail($request->json('configuration_type_id'));

        try {
            $configuration = $configurationType->configurations()->create(
                [
                    'code'          => $request->json('code'),
                    'created_by'    => $userKey
                ]
            );

            foreach ($request->json('translations') as $translation){
                if (isset($translation['language_code']) && isset($translation['title']) && isset($translation['description'])){
                    $configurationTranslation = $configuration->configurationTranslations()->create(
                        [
                            'language_code' => $translation['language_code'],
                            'title'         => $translation['title'],
                            'description'   => $translation['description']
                        ]
                    );
                }
            }

            return response()->json($configuration, 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to store new Configuration'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/configurations/{id}",
     *  summary="Update a Configuration",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Configuration"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Configuration Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/configurations")
     *  ),
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      description="Configuration Id",
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
     *      description="The updated Configuration",
     *      @SWG\Schema(ref="#/definitions/configurationResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/configurationErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Configuration not Found",
     *      @SWG\Schema(ref="#/definitions/configurationErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Configuration",
     *      @SWG\Schema(ref="#/definitions/configurationErrorDefault")
     *  )
     * )
     *
     */
    /**
     * Update the Configuration in storage.
     * Returns the details of the updated Configuration.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $userKey = ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->required['update'], $request);

        try {
            $translationsOld = [];
            $translationsNew = [];

            $configuration = Configuration::findOrFail($id);

            if(is_null(ConfigurationType::find($request->json('configuration_type_id')))){
                $configuration->code = $request->json('code');
            } else {
                $configuration->code                    = $request->json('code');
                $configuration->configuration_type_id   = $request->json('configuration_type_id');
            }
            $configuration->save();

            $translationsId = $configuration->configurationTranslations()->get();
            foreach ($translationsId as $translationId){
                $translationsOld[] = $translationId->id;
            }

            foreach($request->json('translations') as $translation){
                if (isset($translation['language_code']) && isset($translation['title']) && isset($translation['description'])){
                    $configurationTranslation = $configuration->configurationTranslations()->whereLanguageCode($translation['language_code'])->first();
                    if (empty($configurationTranslation)) {
                        $configurationTranslation = $configuration->configurationTranslations()->create(
                            [
                                'language_code' => $translation['language_code'],
                                'title'         => $translation['title'],
                                'description'   => $translation['description']
                            ]
                        );
                    }
                    else {
                        $configurationTranslation->title        = $translation['title'];
                        $configurationTranslation->description  = $translation['description'];
                        $configurationTranslation->save();
                    }
                }
                $translationsNew[] = $configurationTranslation->id;
            }

            $deleteTranslations = array_diff($translationsOld, $translationsNew);
            foreach ($deleteTranslations as $deleteTranslation) {
                $deleteId = $configuration->configurationTranslations()->whereId($deleteTranslation)->first();
                $deleteId->delete();
            }

            return response()->json($configuration, 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to update a Configuration'], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Configuration not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeleteConfiguration",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/configurations/{id}",
     *  summary="Delete a Configuration",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Configuration"},
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      description="Configuration Id",
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteConfiguration")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/configurationErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Configuration not Found",
     *      @SWG\Schema(ref="#/definitions/configurationErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Configuration",
     *      @SWG\Schema(ref="#/definitions/configurationErrorDefault")
     *  )
     * )
     *
     */
    /**
     * Remove the specified Configuration from storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @internal param $configurationKey
     */
    public function destroy(Request $request, $id)
    {
        ONE::verifyToken($request);

        try {
            $configuration = Configuration::findOrFail($id);
            $configurationTranslations = $configuration->configurationTranslations()->get();

            foreach ($configurationTranslations as $configurationTranslation) {
                $configurationTranslation->delete();
            }

            $configuration->delete();

            return response()->json('OK', 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to delete a Configuration'], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Configuration not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConfigurationOptions(Request $request)
    {
        try {
            $configurationTypes = ConfigurationType::with("configurations.configurationOptions")->get();

            foreach ($configurationTypes as $configurationType) {
                if (!($configurationType->translation($request->header('LANG-CODE')))) {
                    if (!$configurationType->translation($request->header('LANG-CODE-DEFAULT'))){
                        $translation = $configurationType->configurationTypeTranslations()->first();
                        if ($translation){
                            $configurationType->translation($translation->language_code);
                        } else {
                            $configurationType->setAttribute('title','no translation');
                            $configurationType->setAttribute('description','no translation');
                        }
                    }
                }

                foreach ($configurationType['configurations'] as $configuration) {
                    if (!($configuration->translation($request->header('LANG-CODE')))) {
                        if (!$configuration->translation($request->header('LANG-CODE-DEFAULT'))){
                            $translation = $configuration->configurationTranslations()->first();
                            if ($translation){
                                $configuration->translation($translation->language_code);
                            } else {
                                $configuration->setAttribute('title','no translation');
                                $configuration->setAttribute('description','no translation');
                            }
                        }
                    }
                }
            }

            return response()->json(['data' => $configurationTypes], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Configurations list'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
