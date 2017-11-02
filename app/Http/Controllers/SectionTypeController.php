<?php

namespace App\Http\Controllers;

use App\One\One;
use App\SectionType;
use App\SectionTypeParameter;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class SectionTypeController extends Controller
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
            $sectionTypes = SectionType::all();

            foreach ($sectionTypes as $key=>$sectionType) {
                if (!($sectionType->translation($request->header('LANG-CODE')))) {
                    if (!$sectionType->translation($request->header('LANG-CODE-DEFAULT'))){
                        if (!$sectionType->translation('en'))
                            $sectionTypes->forget($key);
                    }
                }
            }

            return response()->json($sectionTypes, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Section Types list'], 500);
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
                if (!($exists = SectionType::whereSectionTypeKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $sectionType = SectionType::create([
                'section_type_key' => $key,
                'code' => $request->json('code')
            ]);

            $sectionTypeTranslations = $request->json('translations');

            //Create The Section Type Parameter Translations
            if (!is_null($sectionTypeTranslations)){
                foreach ($sectionTypeTranslations as $translation){
                    if (isset($translation['language_code']) && isset($translation['value'])){
                        $sectionType->sectionTypeTranslations()->create([
                            'language_code' => $translation['language_code'],
                            'value'          => $translation['value'] ?? null
                        ]);
                    }
                }
            }

            //Create relation between section types ans section type parameters if is sent any section type parameter
            $sectionTypeParametersArray = $request->json('section_type_parameters');

            if (!is_null($sectionTypeParametersArray)){
                $sectionTypeParameters = SectionTypeParameter::whereIn('section_type_parameter_key', $sectionTypeParametersArray)->pluck('id')->toArray();
                $sectionType->sectionTypeParameters()->attach($sectionTypeParameters);
            }

            return response()->json($sectionType, 201);
        }
        catch(QueryException $e){
            return response()->json(['error' => 'Failed to store new Section Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $sectionTypeKey
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $sectionTypeKey)
    {
        try {
            $sectionType = SectionType::whereSectionTypeKey($sectionTypeKey)->with('sectionTypeParameters')->firstOrFail();

            if (!($sectionType->translation($request->header('LANG-CODE')))) {
                if (!$sectionType->translation($request->header('LANG-CODE-DEFAULT'))){
                    if (!$sectionType->translation('en'))
                        $sectionType->value = "";
                }
            }

            foreach ($sectionType->sectionTypeParameters as $section_type_parameter) {
                if (!($section_type_parameter->translation($request->header('LANG-CODE')))) {
                    if (!$section_type_parameter->translation($request->header('LANG-CODE-DEFAULT'))){
                        $section_type_parameter->translation('en');
                    }
                }
            }

            $sectionType->translations();

            return response()->json($sectionType, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Section Type'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $sectionTypeKey
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $sectionTypeKey)
    {
        $userKey = ONE::verifyToken($request);

        try {
            $sectionType = SectionType::whereSectionTypeKey($sectionTypeKey)->firstOrFail();
            $sectionType->code = $request->json('code');
            $sectionType->translatable = $request->json('translatable',false);
            $sectionType->save();

            $sectionTypeTranslations = $request->json('translations');
            if (!is_null($sectionTypeTranslations)){
                foreach ($sectionTypeTranslations as $translation) {
                    if (isset($translation['language_code']) && isset($translation['value'])) {
                        $sectionTypeTranslation = $sectionType->sectionTypeTranslations()->firstOrCreate([
                            'language_code' => $translation['language_code']
                        ]);
                        $sectionTypeTranslation->value = $translation['value'] ?? null;
                        $sectionTypeTranslation->save();
                    }
                }
            }

            $sectionTypeParametersArray = $request->json('section_type_parameters');

            if (!is_null($sectionTypeParametersArray))
                $sectionTypeParameters = SectionTypeParameter::whereIn('section_type_parameter_key', $sectionTypeParametersArray)->pluck('id')->toArray();
            else
                $sectionTypeParameters = [];

            $sectionType->sectionTypeParameters()->sync($sectionTypeParameters);

            return response()->json($sectionType, 200);
        }
        catch(QueryException $e){
            return response()->json(['error' => 'Failed to update Section Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $sectionTypeKey
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $sectionTypeKey)
    {
        ONE::verifyToken($request);

        try {
            SectionType::whereSectionTypeKey($sectionTypeKey)->delete();
            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Section Type  not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete the Section Type'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
