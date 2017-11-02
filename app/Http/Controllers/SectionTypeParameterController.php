<?php

namespace App\Http\Controllers;

use App\SectionTypeParameter;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\One\One;

class SectionTypeParameterController extends Controller
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
            $sectionTypeParameters = SectionTypeParameter::all();

            foreach ($sectionTypeParameters as $key=>$sectionTypeParameter) {
                if (!($sectionTypeParameter->translation($request->header('LANG-CODE')))) {
                    if (!$sectionTypeParameter->translation($request->header('LANG-CODE-DEFAULT'))){
                        if (!$sectionTypeParameter->translation('en'))
                            $sectionTypeParameters->forget($key);
                    }
                }
            }

            return response()->json($sectionTypeParameters, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Section Type Parameters list'], 500);
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

            $key = '';
            do {
                $rand = str_random(32);
                if (!($exists = SectionTypeParameter::whereSectionTypeParameterKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $sectionTypeParameter = SectionTypeParameter::create([
                'section_type_parameter_key' => $key,
                'code' => $request->json('code'),
                'type_code' => $request->json('type_code')
            ]);

            $sectionTypeParameterTranslations = $request->json('translations');

            //Create The Section Type Parameter Translations
            if (!is_null($sectionTypeParameterTranslations)){
                foreach ($sectionTypeParameterTranslations as $translation){
                    if (isset($translation['language_code']) && (isset($translation['name']) || isset($translation['description']))){
                        $sectionTypeParameter->sectionTypeParameterTranslations()->create([
                            'language_code' => $translation['language_code'],
                            'name'          => $translation['name'] ?? null,
                            'description'   => $translation['description'] ?? null
                        ]);
                    }
                }
            }
            return response()->json($sectionTypeParameter, 201);
        }
        catch(QueryException $e){
            return response()->json(['error' => 'Failed to store new Section Type Parameter'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $sectionTypeParameterKey
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $sectionTypeParameterKey)
    {
        try {
            $sectionTypeParameter = SectionTypeParameter::whereSectionTypeParameterKey($sectionTypeParameterKey)->firstOrFail();

            if (!($sectionTypeParameter->translation($request->header('LANG-CODE')))) {
                if (!$sectionTypeParameter->translation($request->header('LANG-CODE-DEFAULT'))){
                    if (!$sectionTypeParameter->translation('en'))
                        $sectionTypeParameter->value = "";
                }
            }

            $sectionTypeParameter->translations();

            return response()->json($sectionTypeParameter, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Section Type Parameter'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $sectionTypeParameterKey
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $sectionTypeParameterKey)
    {
        $userKey = ONE::verifyToken($request);

        try {
            $sectionTypeParameter = SectionTypeParameter::whereSectionTypeParameterKey($sectionTypeParameterKey)->firstOrFail();
            $sectionTypeParameter->code = $request->json('code');
            $sectionTypeParameter->type_code = $request->json('type_code');
            $sectionTypeParameter->save();

            $sectionTypeParameterTranslations = $request->json('translations');

            //Create The Section Type Parameter Translations
            if (!is_null($sectionTypeParameterTranslations)){
                foreach ($sectionTypeParameterTranslations as $translation){
                    if (isset($translation['language_code']) && (isset($translation['name']) || isset($translation['description']))){
                        $sectionTypeTranslation = $sectionTypeParameter->sectionTypeParameterTranslations()->firstOrCreate([
                            'language_code' => $translation['language_code']
                        ]);
                        $sectionTypeTranslation->name = $translation["name"];
                        $sectionTypeTranslation->description = $translation["description"];
                        $sectionTypeTranslation->save();
                    }
                }
            }
            return response()->json($sectionTypeParameter, 200);
        }
        catch(QueryException $e){
            return response()->json(['error' => 'Failed to update Section Type Parameter'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $sectionTypeParameterKey
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $sectionTypeParameterKey)
    {
        ONE::verifyToken($request);

        try {
            SectionTypeParameter::whereSectionTypeParameterKey($sectionTypeParameterKey)->delete();
            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Section Type Parameter not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete the Section Type Parameter'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
