<?php

namespace App\Http\Controllers;

use App\BEMenuElement;
use App\BEMenuElementParameter;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BEMenuElementsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $recordsTotal = BEMenuElement::count();
            $tableData = $request->input('tableData') ?? null;

            $query = BEMenuElement::query();

            if (!empty($tableData["order"]["value"]) && !empty($tableData["order"]["dir"])) {
                $query = $query->orderBy($tableData['order']['value'] ?? "id", $tableData['order']['dir'] ?? "ASC");
            }

            if(!empty($tableData['search']['value'])) {
                $query = $query
                    ->where('key', 'like', '%'.$tableData['search']['value'].'%')
                    ->orWhere('code', 'like', '%'.$tableData['search']['value'].'%')
                    ->orWhere('module_code', 'like', '%'.$tableData['search']['value'].'%')
                    ->orWhere('module_type_code', 'like', '%'.$tableData['search']['value'].'%')
                    ->orWhere('permission', 'like', '%'.$tableData['search']['value'].'%')
                    ->orWhere('controller', 'like', '%'.$tableData['search']['value'].'%')
                    ->orWhere('method', 'like', '%'.$tableData['search']['value'].'%');
            }

            $recordsFiltered = $query->count();

            if (!empty($tableData["start"]))
                $query = $query->skip($tableData['start']);
            if (!empty($tableData["length"]))
                $query = $query->take($tableData['length']);

            $beMenuElements = $query->get();

            foreach ($beMenuElements as $beMenuElement) {
                $beMenuElement->newTranslation($request->header('LANG-CODE'),$request->header('LANG-CODE-DEFAULT'));
            }

            $data['beMenuElements'] = $beMenuElements;
            $data['recordsTotal'] = $recordsTotal;
            $data['recordsFiltered'] = $recordsFiltered;

            return response()->json($data, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Failed to retrieve the Back Office Menu Element list'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Back Office Menu Element list'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
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
        try {
            DB::beginTransaction();

            $key = null;
            do {
                $rand = str_random(32);

                if (!($exists = BEMenuElement::where('key',$rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $element = BEMenuElement::create([
                'key' => $key,
                'code' => $request->json('code',''),
                'module_code' => $request->json('module_code',''),
                'module_type_code' => $request->json('module_type_code',''),
                'permission' => $request->json('permission',''),
                'controller' => $request->json('controller',''),
                'method' => $request->json('method','')
            ]);

            $translations = $request->json('translations');
            //Create The BE Menu Element Parameter Translations
            if (!is_null($translations)){
                foreach ($translations as $translation){
                    if (isset($translation['language_code']) && !empty($translation['name']) && !empty($translation['description'])){
                        $element->translations()->create([
                            'language_code' => $translation['language_code'],
                            'name'          => $translation['name'] ?? null,
                            'description'   => $translation['description'] ?? null
                        ]);
                    }
                }
            }

            $parameters = $request->json("parameters");
            foreach ($parameters as $index=>$parameterData) {
                $parameter = BEMenuElementParameter::where('key',$parameterData["key"])->first();
                if (!empty($parameter))
                    $element->parameters()->attach($parameter,["position"=>$index,"code"=>$parameterData["code"] ?? ""]);
            }

            DB::commit();
            return response()->json($element, 201);
        }
        catch(QueryException $e){
            DB::rollBack();
            return response()->json(['error' => 'Failed to store new BE Menu Element'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $key
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $key)
    {
        try {
            $element = BEMenuElement::with(['parameters' => function($q) {
                return $q->orderBy("pivot_position");
            }])->where('key',$key)->firstOrFail();
            $element->translations = $element->translations()->get()->keyBy('language_code');


            $language = $request->header('LANG-CODE');
            $defaultLanguage = $request->header('LANG-CODE-DEFAULT');
            foreach($element->parameters as $parameter) {
                $parameter->newTranslation($language,$defaultLanguage);
            }

            return response()->json($element, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the BE Menu Element'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $key
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $key)
    {
        $userKey = ONE::verifyToken($request);

        try {
            $element = $element = BEMenuElement::where('key',$key)->firstOrFail();
            $element->code = $request->json('code','');
            $element->module_code = $request->json('module_code','');
            $element->module_type_code = $request->json('module_type_code','');
            $element->permission = $request->json('permission','');
            $element->controller = $request->json('controller','');
            $element->method = $request->json('method','');
            $element->save();

            $translations = $request->json('translations');
            if (!is_null($translations)){
                foreach ($translations as $translation) {
                    if (isset($translation['language_code']) && !empty($translation['name']) && !empty($translation['description'])){
                        $currentTranslation = $element->translations()->firstOrCreate([
                            'language_code' => $translation['language_code']
                        ]);

                        $currentTranslation->name = $translation['name'] ?? null;
                        $currentTranslation->description = $translation['description'] ?? null;

                        $currentTranslation->save();
                    }
                }
            }

            $element->parameters()->detach();
            $parameters = $request->json("parameters");
            foreach ($parameters as $index=>$parameterData) {
                $parameter = BEMenuElementParameter::where('key',$parameterData["key"])->first();
                if (!empty($parameter))
                    $element->parameters()->attach($parameter,["position"=>$index,"code"=>$parameterData["code"] ?? ""]);
            }

            return response()->json($element, 200);
        }
        catch(QueryException $e){
            return response()->json(['error' => 'Failed to update BE Menu Element'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $key
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $key)
    {
        ONE::verifyToken($request);

        try {
            BEMenuElement::where('key',$key)->delete();
            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'BE Menu Element not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete the BE Menu Element'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
