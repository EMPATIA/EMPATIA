<?php

namespace App\Http\Controllers;

use App\ConfigurationType;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class SitesController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="ConfigurationType",
 *   description="Everything about Configuration Types",
 * )
 *
 *  @SWG\Definition(
 *      definition="configurationTypeErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="configurationType",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"code", "translations"},
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="translations", type="array",
 *                  @SWG\Items(
 *                      @SWG\Property(property="configuration_type_id", format="string", type="integer"),
 *                      @SWG\Property(property="language_code", format="string", type="string"),
 *                      @SWG\Property(property="title", format="string", type="string"),
 *                      @SWG\Property(property="description", format="string", type="string"),
 *                      @SWG\Property(property="created_at", format="string", type="string"),
 *                      @SWG\Property(property="updated_at", format="string", type="string"),
 *                  )
 *           ),
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="configurationTypeResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"code", "created_by", "created_at", "updated_at", "translations"},
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="string", type="string"),
 *           @SWG\Property(property="updated_at", format="string", type="string"),
 *           @SWG\Property(property="translations", type="array",
 *                  @SWG\Items(
 *                      @SWG\Property(property="configuration_type_id", format="string", type="integer"),
 *                      @SWG\Property(property="language_code", format="string", type="string"),
 *                      @SWG\Property(property="title", format="string", type="string"),
 *                      @SWG\Property(property="description", format="string", type="string"),
 *                      @SWG\Property(property="created_at", format="string", type="string"),
 *                      @SWG\Property(property="updated_at", format="string", type="string"),
 *                  )
 *           ),
 *       )
 *   }
 * )
 *
 */

class ConfigurationTypesController extends Controller
{
    protected $required = [
        'store'     => ['code', 'translations'],
        'update'    => ['code', 'translations']
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $configurationTypes = ConfigurationType::all();
            

            foreach ($configurationTypes as $configurationType) {
                if (!($configurationType->translation($request->header('LANG-CODE')))) {
                    if (!$configurationType->translation($request->header('LANG-CODE-DEFAULT'))){
                        if (!$configurationType->translation($request->header('en')))
                            return response()->json(['error' => 'No translation found'], 404);
                    }
                }
            }
            return response()->json(['data' => $configurationTypes], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Configurations Type list'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Get(
     *  path="/configurationType/{id}",
     *  summary="Show a Configuration Type",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"ConfigurationType"},
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      description="Configuration Type Id",
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
     *      description="Show the Configuration Type data",
     *      @SWG\Schema(ref="#/definitions/configurationTypeResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Configuration Type not Found",
     *      @SWG\Schema(ref="#/definitions/configurationTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Configuration Type",
     *      @SWG\Schema(ref="#/definitions/configurationTypeErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        try {
            $configurationType = ConfigurationType::findOrFail($id);

            if (!($configurationType->translation($request->header('LANG-CODE')))) {
                if (!$configurationType->translation($request->header('LANG-CODE-DEFAULT')))
                    return response()->json(['error' => 'No translation found'], 404);
            }

            return response()->json($configurationType, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Configurations Type not Found'], 404);
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
            $configurationType = ConfigurationType::findOrFail($id);

            $configurationType->translations();

            return response()->json($configurationType, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Configurations Type not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->required['store'], $request);

        try {
            $configurationType = ConfigurationType::create(
                [
                    'code'          => $request->json('code'),
                    'created_by'    => $userKey
                ]
            );

            foreach ($request->json('translations') as $translation){
                if (isset($translation['language_code']) && isset($translation['title']) && isset($translation['description'])){
                    $configurationTypeTranslation = $configurationType->configurationTypeTranslations()->create(
                        [
                            'language_code' => $translation['language_code'],
                            'title'         => $translation['title'],
                            'description'   => $translation['description']
                        ]
                    );
                }
            }

            return response()->json($configurationType, 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to store new Configurations Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/configurationType/{id}",
     *  summary="Update a User",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"ConfigurationType"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Configuration Type Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/configurationType")
     *  ),
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      description="COnfiguration Type Id",
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
     *      description="The updated Configuration Type",
     *      @SWG\Schema(ref="#/definitions/configurationTypeResponse")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/configurationTypeErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="User not Found",
     *      @SWG\Schema(ref="#/definitions/configurationTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Configuration Type",
     *      @SWG\Schema(ref="#/definitions/configurationTypeErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['update'], $request);

        //VERIFY PERMISSIONS

        try{

            $translationsOld = [];
            $translationsNew = [];

            $configurationType = ConfigurationType::findOrFail($id);

            $configurationType->code = $request->json('code');
            $configurationType->save();

            $translationsId = $configurationType->configurationTypeTranslations()->get();
            foreach ($translationsId as $translationId){
                $translationsOld[] = $translationId->id;
            }

            foreach($request->json('translations') as $translation){
                if (isset($translation['language_code']) && isset($translation['title']) && isset($translation['description'])){
                    $configurationTypeTranslation = $configurationType->configurationTypeTranslations()->whereLanguageCode($translation['language_code'])->first();
                    if (empty($configurationTypeTranslation)) {
                        $configurationTypeTranslation = $configurationType->configurationTypeTranslations()->create(
                            [
                                'language_code' => $translation['language_code'],
                                'title'         => $translation['title'],
                                'description'   => $translation['description']
                            ]
                        );
                    }
                    else {
                        $configurationTypeTranslation->title        = $translation['title'];
                        $configurationTypeTranslation->description  = $translation['description'];
                        $configurationTypeTranslation->save();
                    }
                }
                $translationsNew[] = $configurationTypeTranslation->id;
            }

            $deleteTranslations = array_diff($translationsOld, $translationsNew);
            foreach ($deleteTranslations as $deleteTranslation) {
                $deleteId = $configurationType->configurationTypeTranslations()->whereId($deleteTranslation)->first();
                $deleteId->delete();
            }

            return response()->json($configurationType, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Configurations Type not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Configurations Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeleteConfigurationType",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/configurationType/{id}",
     *  summary="Delete a Configuration Types",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"ConfigurationType"},
     *
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      description="Configuration Type Id",
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteConfigurationType")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/configurationTypeErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Configuration Type not Found",
     *      @SWG\Schema(ref="#/definitions/configurationTypeErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Configuration Type",
     *      @SWG\Schema(ref="#/definitions/configurationTypeErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        ONE::verifyToken($request);

        try{
            $configurationType = ConfigurationType::findOrFail($id);
            $configurationTypeTranslations = $configurationType->configurationTypeTranslations()->get();

            foreach ($configurationTypeTranslations as $configurationTypeTranslation) {
                $configurationTypeTranslation->delete();
            }
            $configurationType->delete();

            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Configurations Type not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Configurations Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showTypeConfigurations(Request $request, $id)
    {
        try {
            $configurationType = ConfigurationType::findOrFail($id);

            if (!($configurationType->translation($request->header('LANG-CODE')))) {
                if (!$configurationType->translation($request->header('LANG-CODE-DEFAULT')))
                    return response()->json(['error' => 'No translation found'], 404);
            }

            $configurations = $configurationType->configurations()->get();
            foreach ($configurations as $configuration){
                if(!($configuration->translation($request->header('LANG-CODE')))){
                    if (!$configuration->translation($request->header('LANG-CODE-DEFAULT')))
                        return response()->json(['error' => 'No translation found'], 404);
                }
            }

            $configurationType['configurations'] = $configurations;

            return response()->json($configurationType, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Configurations Type not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
