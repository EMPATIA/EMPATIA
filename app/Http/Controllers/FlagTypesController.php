<?php

namespace App\Http\Controllers;

use App\FlagType;
use App\FlagTypeTranslation;
use Illuminate\Http\Request;

class FlagTypesController extends Controller
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

            $flagTypes = FlagType::with(
                ["currentLanguageTranslation" => function ($q) use ($request) {
                    $q->where('language_code', '=', $request->header('LANG-CODE'));
                }])->get();

            return response()->json(['data' => $flagTypes], 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * get translation of resource
     * @param $flagType
     * @param $request
     * @return mixed
     */
    public static function getTranslation($flagType, $request)
    {
        if (!($flagType->translation($request->header('LANG-CODE')))) {
            if (!$flagType->translation($request->header('LANG-CODE-DEFAULT')))
                $flagType->setAttribute('title', 'no translation');
            $flagType->setAttribute('description', 'no translation');
        }

        return $flagType;
    }


    public function getAllTranslations($flagType)
    {
        $translations = $flagType->translations()->get();
        foreach ($translations as $translation) {
            $flagTypeTranslations[$translation->language_code] = $translation;
        }
        $flagType->translations = $flagTypeTranslations;
    }

    /**
     * set translation of resource
     * @param $flagType
     * @param $translations
     */
    public function setTranslations($flagType, $translations)
    {
        if (!empty($flagType->translations()->get())) {
            $flagType->translations()->delete();
        }

        foreach ($translations as $translation) {
            $flagType->translations()->create(
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $flagType = FlagType::create(['code' => $request->json('code')]);

            if ($request->json('translations')) {
                $translations = $request->json('translations');

                $this->setTranslations($flagType, $translations);
            }

            $this->getAllTranslations($flagType);

            return response()->json(['data' => $flagType], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Parameter'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            $flagType = FlagType::findOrFail($id);

            $this->getAllTranslations($flagType, $request);
            return response()->json(['data' => $flagType], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Flag type not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the flag type'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        try {
            $flagType = FlagType::findOrFail($id);
            $flagType = $this->getTranslations($flagType, $request);

            return response()->json($flagType, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Flag type not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the flag type'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $flagType = FlagType::findOrFail($id);

            if ($request->json('code')) {
                $flagType->code = $request->json('code');
                $flagType->save();
            }
            if ($request->json('translations')) {
                $translations = $request->json('translations');
                $this->setTranslations($flagType, $translations);
            }

            $this->getAllTranslations($flagType);

            return response()->json(['data' => $flagType], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Flag type not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the flag type'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            FlagType::destroy($id);
            return response()->json('Ok', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'FlagType not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete FlagType'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
