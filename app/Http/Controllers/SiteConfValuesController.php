<?php

namespace App\Http\Controllers;

use App\Site;
use App\SiteConf;
use App\SiteConfGroup;
use App\SiteConfValue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;

class SiteConfValuesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dataToSend = SiteConfValue::all();

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
                $site = Site::where('key',$request->header("X-SITE-KEY"))->first();
                $siteSiteConf = SiteConfValue::where("site_id","=",$site->id)
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
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function updateValues(Request $request)
    {


        foreach($request['configurations'] as $req) {

            try {
                $siteId = Site::where('key',$request['siteKey'])->first();
                $type = SiteConfValue::where("site_conf_id", "=", $req['site_conf_id'])->where("site_id", "=", $siteId->id)->firstOrFail();
                $type->site_id = $siteId->id;
                $type->site_conf_id = $req['site_conf_id'];
                $type->value = $req['value'];

                $type->save();
            } catch (Exception $e) {

                $siteId = Site::where('key',$request['siteKey'])->first();

                $type = SiteConfValue::create([
                    'site_id'      => $siteId->id,
                    'site_conf_id' => $req['site_conf_id'],
                    'value'        => $req['value']
                ]);

            }
        }
        return response()->json($type, 201);

    }
}
