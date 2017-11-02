<?php

namespace App\Http\Controllers;

use App\ContentType;
use App\ContentTypeType;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\One\One;


class ContentTypeTypesController extends Controller
{


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {

            $contentTypeTypes = ContentTypeType::with('contentType')->get();

            foreach ($contentTypeTypes as $contentTypeType) {
                if (!($contentTypeType->translation($request->header('LANG-CODE')))) {
                    if (!$contentTypeType->translation($request->header('LANG-CODE-DEFAULT'))){
                        $translation = $contentTypeType->contentTypeTypeTranslations()->first();

                        if(!empty($translation)){
                            $contentTypeType->translation($translation->language_code);
                        }
                    }
                }
            }
            return response()->json(['data' => $contentTypeTypes], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listByEntity(Request $request)
    {
        try {
            $entityKey = $request->header('X-ENTITY-KEY');

            $contentTypeTypes = ContentTypeType::with('contentType')->whereEntityKey($entityKey)->get();

            foreach ($contentTypeTypes as $contentTypeType) {
                if (!($contentTypeType->translation($request->header('LANG-CODE')))) {
                    if (!$contentTypeType->translation($request->header('LANG-CODE-DEFAULT'))){
                        $translation = $contentTypeType->contentTypeTypeTranslations()->first();

                        if(!empty($translation)){
                            $contentTypeType->translation($translation->language_code);
                        }
                    }
                }
            }
            return response()->json(['data' => $contentTypeTypes], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }


    /**
     * @param Request $request
     * @param $contentTypeCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllByType(Request $request, $contentTypeCode)
    {
        $entityKey = $request->header('X-ENTITY-KEY');

        try {
            $contentType = ContentType::whereCode($contentTypeCode)->firstOrFail();
            $contentTypeTypes = ContentTypeType::whereContentTypeId($contentType->id)->whereEntityKey($entityKey)->get();

            foreach ($contentTypeTypes as $contentTypeType) {
                if (!($contentTypeType->translation($request->header('LANG-CODE')))) {
                    if (!$contentTypeType->translation($request->header('LANG-CODE-DEFAULT'))){
                        $translation = $contentTypeType->contentTypeTypeTranslations()->first();
                        if (empty($translation)){
                            return response()->json(['error' => 'No translation found'], 404);
                        }else{
                            $contentTypeType->translation($translation->language_code);
                        }
                    }
                }
            }

            return response()->json(['data' => $contentTypeTypes], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        ONE::verifyToken($request);

        $contentType = ContentType::whereId($request->json('content_type_id'))->firstOrFail();

        $entityKey = is_null($request->json('entity_key')) ? $request->header('X-ENTITY-KEY') : $request->json('entity_key');

        // topic_review_key generation
        $key = '';
        do {
            $rand = str_random(32);

            if (!($exists = ContentTypeType::whereContentTypeTypeKey($rand)->exists())) {
                $key = $rand;
            }
        } while ($exists);

        try {
            $contentTypeType = ContentTypeType::create(
                [
                    'content_type_type_key'      => $key,
                    'code'      => $request->json('code'),
                    'color'      => !empty($request->json('color'))? $request->json('color') : null,
                    'text_color'      => !empty($request->json('text_color'))? $request->json('text_color') : null,
                    'file'      => !empty($request->input('file'))? $request->input('file') : null,
                    'entity_key'      => $entityKey,
                    'content_type_id'      => $contentType->id
                ]
            );

            foreach ($request->json('translations') as $translation){
                if (isset($translation['language_code']) && isset($translation['name'])){
                    $contentTypeTypeTranslation = $contentTypeType->ContentTypeTypeTranslations()->create(
                        [
                            'language_code' => $translation['language_code'],
                            'name'          => $translation['name']
                        ]
                    );
                }
            }

            return response()->json($contentTypeType, 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to store new Content Type Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param Request $request
     * @param $contentTypeTypeKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $contentTypeTypeId
     */
    public function show(Request $request, $contentTypeTypeKey)
    {

        try {
            $contentTypeType = ContentTypeType::whereContentTypeTypeKey($contentTypeTypeKey)->firstOrFail();

            if (!($contentTypeType->translation($request->header('LANG-CODE')))) {
                if (!$contentTypeType->translation($request->header('LANG-CODE-DEFAULT'))){

                    $translation = $contentTypeType->contentTypeTypeTranslations()->first();
                    if (empty($translation)){
                        return response()->json(['error' => 'No translation found'], 404);

                    }else{
                        $contentTypeType->translation($translation->language_code);
                    }
                }
            }

            return response()->json($contentTypeType, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Content Type Type not Found'], 404);
        }

    }

    /**
     * @param $contentTypeTypeKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param Request $request
     */
    public function edit($contentTypeTypeKey)
    {
        try {
            $contentTypeType = ContentTypeType::whereContentTypeTypeKey($contentTypeTypeKey)->firstOrFail();

            $contentTypeType->translations();

            return response()->json($contentTypeType, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Content Type Type not Found'], 404);
        }
    }


    /**
     * @param Request $request
     * @param $contentTypeTypeKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $contentTypeTypeId
     */
    public function update(Request $request, $contentTypeTypeKey)
    {

        ONE::verifyToken($request);

        try{

            $translationsOld = [];
            $translationsNew = [];

            $contentTypeType = ContentTypeType::whereContentTypeTypeKey($contentTypeTypeKey)->firstOrFail();

            if(!empty($request->json('content_type_id'))){
                $contentType = ContentType::whereId($request->json('content_type_id'))->firstOrFail();
                $contentTypeType->content_type_id = $contentType->id;
            }
            $contentTypeType->code     = $request->json('code');

            if(!empty($request->json('color'))){
                $contentTypeType->color   = $request->json('color');
            }

            if(!empty($request->json('text_color'))){
                $contentTypeType->text_color   = $request->json('text_color');
            }
            if(!empty($request->json('file'))){
                $contentTypeType->file   = $request->input('file');
            }

            $contentTypeType->save();

            $translationsOld = $contentTypeType->contentTypeTypeTranslations()->pluck('id');


            foreach($request->json('translations') as $translation){
                if (isset($translation['language_code']) && isset($translation['name'])){
                    $contentTypeTypeTranslation = $contentTypeType->contentTypeTypeTranslations()->whereLanguageCode($translation['language_code'])->first();
                    if (empty($contentTypeTypeTranslation)) {
                        $contentTypeTypeTranslation = $contentTypeType->contentTypeTypeTranslations()->create(
                            [
                                'language_code' => $translation['language_code'],
                                'name'          => $translation['name']
                            ]
                        );
                    }
                    else {
                        $contentTypeTypeTranslation->name = $translation['name'];
                        $contentTypeTypeTranslation->save();
                    }
                }
                $translationsNew[] = $contentTypeTypeTranslation->id;
            }

            $deleteTranslations = array_diff($translationsOld->toArray(), $translationsNew);

            foreach ($deleteTranslations as $deleteTranslation) {
                $deleteId = $contentTypeType->contentTypeTypeTranslations()->whereId($deleteTranslation)->first();
                $deleteId->delete();
            }

            return response()->json($contentTypeType, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Content Type Type not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Content Type Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param Request $request
     * @param $contentTypeTypeKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $contentTypeTypeId
     */
    public function destroy(Request $request, $contentTypeTypeKey)
    {
        ONE::verifyToken($request);

        try{
            $contentTypeType = ContentTypeType::whereContentTypeTypeKey($contentTypeTypeKey)->firstOrFail();
            $contentTypeType->contentTypeTypeTranslations()->delete();
            $contentTypeType->delete();

            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Content Type Type not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Content Type Type'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
