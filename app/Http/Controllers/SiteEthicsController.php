<?php

namespace App\Http\Controllers;

use App\One\One;
use App\Site;
use App\SiteEthic;
use App\SiteEthicType;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SiteEthicsController extends Controller
{

    protected $required = [
        'store' => ['site_key', 'site_ethic_type_code', 'translations'],
        'update' => ['site_key', 'translations'],
        'delete' => ['site_key']
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }





    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['store'], $request);

        try {
            /** Verify if site exists */
            $site = Site::where('key',$request->json('site_key'))->firstOrFail();
            $siteEthicType = SiteEthicType::whereCode($request->json('site_ethic_type_code'))->firstOrFail();
            $key = '';
            do {
                $rand = str_random(32);
                if (!($exists = SiteEthic::whereSiteEthicKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $siteEthic = $site->siteEthics()->create(
                [
                    'site_ethic_key' => $key,
                    'site_ethic_type_id' => $siteEthicType->id,
                    'active' => 1
                ]
            );

            foreach ($request->json('translations') as $translation) {
                if (isset($translation['language_code']) && (isset($translation['content']))) {
                    $siteEthicTranslation = $siteEthic->siteEthicTranslations()->create(
                        [
                            'language_code' => $translation['language_code'],
                            'content' => htmlentities($translation['content'], ENT_QUOTES, "UTF-8")
                        ]
                    );
                }
            }

            return response()->json($siteEthic, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Site Ethic'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $siteEthicKey
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$siteEthicKey)
    {

        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest(['version'], $request);
        try {

            /** check if site ethics exists for site send in request and delete */
            $siteEthic = SiteEthic::whereSiteEthicKey($siteEthicKey)->whereVersion($request->json('version'))->with('siteEthicType')->firstOrFail();
            $siteEthic->translations();
            return response()->json($siteEthic, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site Ethic not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Site Ethic'], 400);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $siteEthicKey
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $siteEthicKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['update'], $request);

        try {

            /** check if site exists */
            $site = Site::where('key',$request->json('site_key'))->first();
            if(empty($site)){
                return response()->json(['error' => 'Site not Found'], 404);
            }
            /** check if site ethics exists for site send in request */
            $siteEthic = $site->siteEthics()->whereSiteEthicKey($siteEthicKey)->orderBy('version','desc')->firstOrFail();
            $version = $siteEthic->version + 1;

            $newSiteEthic = $site->siteEthics()->create(
                [
                    'site_ethic_key' => $siteEthic->site_ethic_key,
                    'site_ethic_type_id' => $siteEthic->site_ethic_type_id,
                    'version' => $version,
                    'active' => 0,
                ]
            );

            foreach ($request->json('translations') as $translation) {
                if (isset($translation['language_code']) && (isset($translation['content']))) {
                    $siteEthicTranslation = $newSiteEthic->siteEthicTranslations()->create(
                        [
                            'language_code' => $translation['language_code'],
                            'content' => htmlentities($translation['content'], ENT_QUOTES, "UTF-8")
                        ]
                    );
                }
            }

            return response()->json($newSiteEthic, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site Ethic not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Site Ethic'], 400);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }




    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $siteEthicKey
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$siteEthicKey)
    {
        $userKey = ONE::verifyToken($request);
        try {
            /** check if site ethics exists and delete */
            $siteEthic = SiteEthic::whereSiteEthicKey($siteEthicKey)->delete();

            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site Ethic not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Site Ethic'], 400);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Activate the specified resource in storage.
     *
     * @param Request $request
     * @param $siteEthicKey
     * @return \Illuminate\Http\Response
     */
    public function activateVersion(Request $request,$siteEthicKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest(['version'], $request);

        try {

            $siteEthicVersion = SiteEthic::whereSiteEthicKey($siteEthicKey)->whereVersion($request->json('version'))->firstOrFail();
            $siteEthic = SiteEthic::whereSiteEthicKey($siteEthicKey)->update(['active' => 0]);
            $siteEthicVersion->update(['active' => 1]);


            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Site Ethic not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to activate Site Ethic'], 400);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
