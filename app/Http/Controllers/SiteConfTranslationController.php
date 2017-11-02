<?php

namespace App\Http\Controllers;

use App\SiteConfTranslation;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\One\One;

class SiteConfTranslationController extends Controller
{
    protected $required = [
        'store' => [],
        'update' => []
    ];

    /**
     * Returns the list of Site Conf Translations.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $types = SiteConfTranslation::all();

            return response()->json(['data' => $types], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve list of Site Conf Translation'], 500);
        }
    }

    /**
     * Returns the details of the specified SiteConfTranslation.
     *
     * @param Request $request
     * @param $translationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $translationId)
    {
        try {
            $type = SiteConfTranslation::findOrFail($translationId);
            return response()->json($type, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'SiteConfTranslation not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Site Conf Translation']);
        }
    }

    /**
     * Stores a new Parameter Type returning it afterwards.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->required['store'], $request);

        $requestArray = $request->toArray();
        try {
            $type = SiteConfTranslation::create([
                'name' => $requestArray["name"],
                'description' => $requestArray["description"],
                'lang_code'  => $requestArray["lang_code"],
                'site_conf_id' => $requestArray["site_conf_id"],
            ]);

            return response()->json($type, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Site Conf Translation'], 500);
        }
    }

    /**
     * Updates the specified Site Config Translation returning it afterwards.
     *
     * @param Request $request
     * @param $translationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $translationId)
    {
        ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->required['update'], $request);

        try {
            $type = SiteConfTranslation::findOrFail($translationId);
            $requestArray = $request->toArray();
            $type->name             = $requestArray["name"];
            $type->description      = $requestArray["description"];
            $type->lang_code        = $requestArray["lang_code"];
            $type->site_conf_id     = $requestArray["site_conf_id"];
            $type->save();

            return response()->json($type, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site Conf Translation not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Site Conf Translation'], 500);
        }
    }

    /**
     * Deletes the specified SiteConfTranslation.
     *
     * @param Request $request
     * @param $translationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $translationId)
    {
        ONE::verifyToken($request);

        try {
            SiteConfTranslation::destroy($translationId);

            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site Conf Translation Type not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Site Conf Translation'], 500);
        }
    }
}