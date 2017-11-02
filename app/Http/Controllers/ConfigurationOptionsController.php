<?php

namespace App\Http\Controllers;

use App\Configuration;
use App\ConfigurationOption;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class ConfigurationOptionsController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="ConfigOpts",
 *   description="Everything about Configuration Options",
 * )
 *
 *  @SWG\Definition(
 *      definition="configOptsErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="congOpts",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"cm_key"},
 *           @SWG\Property(property="cm_key", format="string", type="string"),
 *       )
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="congOptsResponse",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"conf_opt_key"},
 *           @SWG\Property(property="conf_opt_key", format="string", type="string"),
 *       )
 *   }
 * )
 *
 */
class ConfigurationOptionsController extends Controller
{
    protected $required = [
        'store'     => ['configuration_id', 'code', 'translations'],
        'update'    => ['configuration_id', 'code', 'translations']
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $configurationOptions = ConfigurationOption::all();

            foreach ($configurationOptions as $configurationOption) {
                $configurationOption->timezone($request);
                if (!($configurationOption->translation($request->header('LANG-CODE')))) {
                    if (!$configurationOption->translation($request->header('LANG-CODE-DEFAULT')))
                        return response()->json(['error' => 'No translation found'], 404);
                }
            }
            return response()->json(['data' => $configurationOptions], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Configurations Option list'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $conf_opt_key)
    {
        try {
            $configurationOption = ConfigurationOption::findOrFail($conf_opt_key)->timezone($request);

            if (!($configurationOption->translation($request->header('LANG-CODE')))) {
                if (!$configurationOption->translation($request->header('LANG-CODE-DEFAULT')))
                    return response()->json(['error' => 'No translation found'], 404);
            }

            return response()->json($configurationOption, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Configurations Option not Found'], 404);
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
            $configurationOption = ConfigurationOption::findOrFail($id)->timezone($request);;

            $configurationOption->translations();

            return response()->json($configurationOption, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Configurations Option not Found'], 404);
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

        $configuration = Configuration::findOrFail($request->json('configuration_id'));

        try {
            $configurationOption = $configuration->create(
                [
                    'code'  => $request->json('code'),
                ]
            );

            foreach ($request->json('translations') as $translation){
                if (isset($translation['language_code']) && isset($translation['value']) && isset($translation['title']) && isset($translation['description'])){
                    $configurationOptionTranslation = $configurationOption->configurationOptionTranslations()->create(
                        [
                            'language_code' => $translation['language_code'],
                            'title'         => $translation['title'],
                            'value'         => $translation['value'],
                            'description'   => $translation['description']
                        ]
                    );
                }
            }

            return response()->json($configurationOption, 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to store new Configurations Option'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
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

            $configurationOption = ConfigurationOption::findOrFail($id);

            if(is_null(Configuration::find($request->json('configuration_id')))){
                $configurationOption->code = $request->json('code');
            } else {
                $configurationOption->code              = $request->json('code');
                $configurationOption->configuration_id  = $request->json('configuration_id');
            }
            $configurationOption->save();

            $translationsId = $configurationOption->configurationOptionTranslations()->get();
            foreach ($translationsId as $translationId){
                $translationsOld[] = $translationId->id;
            }

            foreach($request->json('translations') as $translation){
                if (isset($translation['language_code']) && isset($translation['title']) && isset($translation['description']) && isset($translation['tag'])){
                    $configurationOptionTranslation = $configurationOption->configurationOptionTranslations()->whereLanguageCode($translation['language_code'])->first();
                    if (empty($configurationOptionTranslation)) {
                        $configurationOptionTranslation = $configurationOption->configurationOptionTranslations()->create(
                            [
                                'language_code' => $translation['language_code'],
                                'title'         => $translation['title'],
                                'value'           => $translation['value'],
                                'description'   => $translation['description']
                            ]
                        );
                    }
                    else {
                        $configurationOptionTranslation->title        = $translation['title'];
                        $configurationOptionTranslation->description  = $translation['description'];
                        $configurationOptionTranslation->tag          = $translation['value'];
                        $configurationOptionTranslation->save();
                    }
                }
                $translationsNew[] = $configurationOptionTranslation->id;
            }

            $deleteTranslations = array_diff($translationsOld, $translationsNew);
            foreach ($deleteTranslations as $deleteTranslation) {
                $deleteId = $configurationOption->configurationOptionTranslations()->whereId($deleteTranslation)->first();
                $deleteId->delete();
            }

            return response()->json($configurationOption, 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to update a Configurations Option'], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Configurations Option not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        ONE::verifyToken($request);

        try {
            $configurationOption = ConfigurationOption::findOrFail($id);
            $configurationOptionTranslations = $configurationOption->configurationOptionTranslations()->get();

            foreach ($configurationOptionTranslations as $configurationOptionTranslation) {
                $configurationOptionTranslation->delete();
            }

            $configurationOption->delete();

            return response()->json('OK', 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to delete a Configurations Option'], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Configurations Option not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
