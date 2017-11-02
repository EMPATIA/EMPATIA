<?php

namespace App\Http\Controllers;

use App\BEMenuElementParameter;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class BEMenuElementParametersController extends Controller
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
            $recordsTotal = BEMenuElementParameter::count();
            $tableData = $request->input('tableData') ?? null;

            $query = BEMenuElementParameter::query();

            if (!empty($tableData["order"]["value"]) && !empty($tableData["order"]["dir"]) )
                $query = $query->orderBy($tableData['order']['value'], $tableData['order']['dir']);

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

            $beMenuElementParameters = $query->get();

            foreach ($beMenuElementParameters as $beMenuElement) {
                $beMenuElement->newTranslation($request->header('LANG-CODE'),$request->header('LANG-CODE-DEFAULT'));
            }

            $data['beMenuElementParameters'] = $beMenuElementParameters;
            $data['recordsTotal'] = $recordsTotal;
            $data['recordsFiltered'] = $recordsFiltered;

            return response()->json($data, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Failed to retrieve the Back Office Menu Element Parameters list'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Back Office Menu Element Parameters list'], 500);
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
            $key = null;
            do {
                $rand = str_random(32);

                if (!($exists = BEMenuElementParameter::where('key',$rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $parameter = BEMenuElementParameter::create([
                'key' => $key,
                'code' => $request->json('code')
            ]);

            $translations = $request->json('translations');
            //Create The BE Menu Element Parameter Parameter Translations
            if (!is_null($translations)){
                foreach ($translations as $translation){
                    if (isset($translation['language_code']) && !empty($translation['name']) && !empty($translation['description'])){
                        $parameter->translations()->create([
                            'language_code' => $translation['language_code'],
                            'name'          => $translation['name'] ?? null,
                            'description'   => $translation['description'] ?? null
                        ]);
                    }
                }
            }

            return response()->json($parameter, 201);
        }
        catch(QueryException $e){
            
            return response()->json(['error' => 'Failed to store new BE Menu Element Parameter'], 500);
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
            $parameter = BEMenuElementParameter::where('key',$key)->firstOrFail();
            $parameter->translations = $parameter->translations()->get()->keyBy('language_code');

            return response()->json($parameter, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the BE Menu Element Parameter'], 500);
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
            $parameter = $parameter = BEMenuElementParameter::where('key',$key)->firstOrFail();
            $parameter->code = $request->json('code');
            $parameter->save();

            $translations = $request->json('translations');
            if (!is_null($translations)){
                foreach ($translations as $translation) {
                    if (isset($translation['language_code']) && !empty($translation['name']) && !empty($translation['description'])){
                        $currentTranslation = $parameter->translations()->firstOrCreate([
                            'language_code' => $translation['language_code']
                        ]);

                        $currentTranslation->name = $translation['name'] ?? null;
                        $currentTranslation->description = $translation['description'] ?? null;

                        $currentTranslation->save();
                    }
                }
            }

            return response()->json($parameter, 200);
        }
        catch(QueryException $e){
            return response()->json(['error' => 'Failed to update BE Menu Element Parameter'], 500);
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
            BEMenuElementParameter::where('key',$key)->delete();
            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'BE Menu Element Parameter not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete the BE Menu Element Parameter'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
