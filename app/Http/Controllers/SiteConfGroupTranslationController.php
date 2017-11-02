<?php

namespace App\Http\Controllers;

use App\SiteConfGroupTranslation;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\One\One;

class SiteConfGroupTranslationController extends Controller
{
    protected $required = [
        'store' => ["name","description","lang_code","site_conf_group_id","site_conf_group_id"],
        'update' => []
    ];

    /**
     * Returns the list of Site Conf Groups.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $types = SiteConfGroupTranslation::all();

            return response()->json(['data' => $types], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve list of Site Conf Group Translation'], 500);
        }
    }

    /**
     * Returns the details of the specified SiteConfGroupTranslation.
     *
     * @param Request $request
     * @param $translationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $translationId)
    {
        try {
            $type = SiteConfGroupTranslation::findOrFail($translationId);
            return response()->json($type, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'SiteConfGroupTranslation not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Site Conf Group Translation']);
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

        $requestArray = $request->toArray();
        try {
            $type = SiteConfGroupTranslation::create([
                'name' => $requestArray["name"],
                'description' => $requestArray["description"],
                'lang_code'  => $requestArray["lang_code"],
                'site_conf_group_id' => $requestArray["site_conf_group_id"],
            ]);

            return response()->json($type, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Site Conf Group Translation'], 500);
        }
    }

    /**
     * Updates the specified Site Config Group returning it afterwards.
     *
     * @param Request $request
     * @param $translationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $translationId)
    {
        ONE::verifyToken($request);

        $requestArray = $request->toArray();
        try {
            $type = SiteConfGroupTranslation::findOrFail($translationId);
            $type->name                 = $requestArray["name"];
            $type->description          = $requestArray["description"];
            $type->lang_code            = $requestArray["lang_code"];
            $type->site_conf_group_id   = $requestArray["site_conf_group_id"];
            $type->save();

            return response()->json($type, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site Conf Group Translation not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Site Conf Group Translation',"x"=>$e], 500);
        }
    }

    /**
     * Deletes the specified SiteConfGroupTranslation.
     *
     * @param Request $request
     * @param $translationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $translationId)
    {
        ONE::verifyToken($request);

        try {
            SiteConfGroupTranslation::destroy($translationId);

            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site Conf Group TranslationType not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Site Conf Group Translation'], 500);
        }
    }
}