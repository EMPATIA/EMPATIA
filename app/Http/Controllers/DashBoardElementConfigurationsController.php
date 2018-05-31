<?php

namespace App\Http\Controllers;

use App\DashBoardElementConfiguration;
use Illuminate\Http\Request;

class DashBoardElementConfigurationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $query = DashBoardElementConfiguration::query();
            $tableData = $request->input('tableData') ?? null;

            if (!empty($tableData)) {
                $recordsTotal = $query->count();

                if(!empty($tableData['search']['value'])) {
                    $query = $query
                        ->where('code', 'like', '%'.$tableData['search']['value'].'%');
                }

                $recordsFiltered = $query->count();

                $dashBoardElementConfigurations = $query
                    ->skip($tableData['start'])
                    ->take($tableData['length'])
                    ->get();

                foreach ($dashBoardElementConfigurations as $dashBoardElementConfiguration) {
                    $dashBoardElementConfiguration->newTranslation($request->header('LANG-CODE'),$request->header('LANG-CODE-DEFAULT'));
                }

                $data['dashBoardElementConfigurations'] = $dashBoardElementConfigurations;
                $data['recordsTotal'] = $recordsTotal;
                $data['recordsFiltered'] = $recordsFiltered;
            } else {
                $data = $dashBoardElementConfigurations->get();

                foreach ($data as $dashBoardElementConfiguration) {
                    $dashBoardElementConfiguration->newTranslation($request->header('LANG-CODE'),$request->header('LANG-CODE-DEFAULT'));
                }
            }

            return response()->json(["data" => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * set translation of resource
     * @param $dashBoardElementConfiguration
     * @param $translations
     */
    public function setTranslations($dashBoardElementConfiguration, $translations)
    {
        if (!empty($dashBoardElementConfiguration->translations()->get())) {
            $dashBoardElementConfiguration->translations()->delete();
        }

        foreach ($translations as $translation) {
            $dashBoardElementConfiguration->translations()->create(
                [
                    'language_code' => $translation['language_code'],
                    'title'         => $translation['title'],
                    'description'   => empty($translation['description']) ? null : $translation['description']
                ]
            );

        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $dashBoardElementConfiguration = DashBoardElementConfiguration::create(['code' => $request->json('code'),'type' => $request->json('type'),'default_value' => $request->json('default_value')]);

            if ($request->json('translations')) {
                $translations = $request->json('translations');

                $this->setTranslations($dashBoardElementConfiguration, $translations);
            }

            $dashBoardElementConfiguration->translations = $dashBoardElementConfiguration->translations()->get()->keyBy("language_code")->toArray();

            return response()->json(['data' => $dashBoardElementConfiguration], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new DashBoard Element Configuration'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        try {
            $dashBoardElementConfiguration = DashBoardElementConfiguration::with("translations")->findOrFail($id);
            $dashBoardElementConfiguration->translations = $dashBoardElementConfiguration->translations->keyBy("language_code")->toArray();

            return response()->json(['data' => $dashBoardElementConfiguration], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'DashBoard Element not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the DashBoard Element'], 500);
        }
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
        try {
            $dashBoardElementConfiguration = DashBoardElementConfiguration::findOrFail($id);

            $dashBoardElementConfiguration->code = $request->json('code');
            $dashBoardElementConfiguration->type = $request->json('type');
            $dashBoardElementConfiguration->default_value = $request->json('default_value');
            $dashBoardElementConfiguration->save();


            if ($request->json('translations')) {
                $translations = $request->json('translations');

                $this->setTranslations($dashBoardElementConfiguration, $translations);
            }

            $dashBoardElementConfiguration->translations = $dashBoardElementConfiguration->translations()->get()->keyBy("language_code")->toArray();

            return response()->json(['data' => $dashBoardElementConfiguration], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new DashBoard Element Configuration'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        try {
            DashBoardElementConfiguration::destroy($id);
            return response()->json('Ok', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'DashBoard Element Configuration not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete DashBoard Element Configuration'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
