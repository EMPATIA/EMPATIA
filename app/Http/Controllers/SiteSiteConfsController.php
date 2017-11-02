<?php

namespace App\Http\Controllers;

use App\SiteConf;
use App\SiteConfGroup;
use App\SiteSiteConfs;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\One\One;

class SiteSiteConfsController extends Controller
{
    protected $required = [
        'store' => [],
        'update' => [],
    ];

    /**
     * Returns the list of Site Confs.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $groups = SiteConfGroup::all();
            $dataToSend = [];
            foreach ($groups as $group) {
                if (!($group->translation($request->header('LANG-CODE')))) {
                    if (!$group->translation($request->header('LANG-CODE-DEFAULT'))) {
                        $group->name = $group->code;
                        $group->description = "";
                    }
                }
                $dataToSend[$group->id] = [
                    'name' => $group->name,
                    'description' => $group->description,
                    'confs' => [],
                ];

                $confsOfGroup = SiteConf::where("site_conf_group_id","=",$group->id)->get();
                $confsOfGroupCount = 0;
                foreach ($confsOfGroup as $confOfGroup) {
                    if (!($confOfGroup->translation($request->header('LANG-CODE')))) {
                        if (!$confOfGroup->translation($request->header('LANG-CODE-DEFAULT'))) {
                            $confOfGroup->name = $confOfGroup->code;
                            $confOfGroup->description = "";
                        }
                    }

                    $siteSiteConf = SiteSiteConfs::where("site_id","=",$request->header("X-SITE-KEY"))
                        ->where("site_conf_id","=",$confOfGroup->id)->first();

                    if (count($siteSiteConf)==1) {
                        $confsOfGroupCount++;
                        $dataToSend[$group->id]['confs'][$confOfGroup->id] = [
                            'id' => $confOfGroup->id,
                            'name' => $confOfGroup->name,
                            'description' => $confOfGroup->description,
                            'value' => (count($siteSiteConf) == 1) ? $siteSiteConf->parameter_value : 0,
                        ];
                    }
                }

                if ($confsOfGroupCount==0)
                    unset($dataToSend[$group->id]);
            }
            return response()->json(['data' => $dataToSend], 200);

            /*
            $types = SiteSiteConfs::where("site_id","=",$request->header("X-SITE-KEY"))->get();
            foreach ($types as $type) {
                $type->siteConf();
                if (!($type->siteConf->translation($request->header('LANG-CODE')))) {
                    if (!$type->siteConf->translation($request->header('LANG-CODE-DEFAULT')))
                        $type->site_conf_name = $type->siteConf->code;
                    else
                        $type->site_conf_name = $type->siteConf->name;
                } else
                    $type->site_conf_name = $type->siteConf->name;
            }
            return response()->json(['data' => $types], 200);*/
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve list of Site Site Configs','e'=>$e], 500);
        }
    }

    /**
     * Returns the details of the specified SiteSiteConfs.
     *
     * @param Request $request
     * @param $typeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $configId)
    {
        try {
            $type = SiteSiteConfs::where("site_id","=",$request->header("X-SITE-KEY"))->with("siteConf")->first();

            if (!($type->siteConf->translation($request->header('LANG-CODE')))) {
                if (!$type->siteConf->translation($request->header('LANG-CODE-DEFAULT')))
                    $type->site_conf_name = $type->siteConf->code;
                else
                    $type->site_conf_name = $type->siteConf->name;
            } else
                $type->site_conf_name = $type->siteConf->name;

            return response()->json($type, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'SiteSiteConfs not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the SiteSiteConfs','e'=>$e->getMessage()]);
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

        try {
            $requestArray = $request->toArray();
            $type = SiteSiteConfs::create([
                'site_id' => $request->header("X-SITE-KEY"),
                'site_conf_id'  => $requestArray["site_conf_id"],
                'parameter_value' => $requestArray["parameter_value"],
            ]);

            return response()->json($type, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Site Site Config'], 500);
        }
    }

    /**
     * Updates the specified Site Config returning it afterwards.
     *
     * @param Request $request
     * @param $confId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $confId)
    {
        ONE::verifyToken($request);

        try {
            $requestArray = $request->toArray();
            $type = SiteSiteConfs::findOrFail($confId);

            $type->site_id        = $request->header("X-SITE-KEY");
            $type->parameter_value          = $requestArray["parameter_value"];
            $type->save();

            return response()->json($type, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'SiteSiteConfs not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update SiteSiteConfs'], 500);
        }
    }

    /**
     * Deletes the specified SiteSiteConf.
     *
     * @param Request $request
     * @param $typeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $configId)
    {
        //ONE::verifyToken($request);

        try {
            SiteSiteConfs::destroy($configId);

            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parameter Type not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Parameter Type'], 500);
        }
    }

}
