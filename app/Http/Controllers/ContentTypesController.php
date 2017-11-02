<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use App\One\One;
use App\ContentType;
use App\ContentTypeTranslation;

class ContentTypesController extends Controller
{
    /**
     * Requests a list of ContentTypes.
     * Returns the list of all ContentTypes.
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function all(Request $request)
    {
        try {
            $contentTypes = ContentType::all();

            foreach ($contentTypes as $contentType) {
                if (!($contentType->translation($request->header('LANG-CODE')))) {
                    if (!$contentType->translation($request->header('LANG-CODE-DEFAULT'))){

                        $translation = $contentType->contentTypeTranslations()->first();
                        if(!empty($translation)){
                            $contentType->translation($translation->language_code);
                        }
                    }
                }
            }

            return response()->json($contentTypes, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests a list of ContentTypes.
     * Returns the list of ContentTypes that are linkable.
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function linkable(Request $request)
    {
        try {
            $contentTypes = ContentType::whereLinkable(1)->get();

            foreach ($contentTypes as $contentType) {
                if (!($contentType->translation($request->header('LANG-CODE')))) {
                    if (!$contentType->translation($request->header('LANG-CODE-DEFAULT'))){
                        $translation = $contentType->contentTypeTranslations()->first();
                        if(!empty($translation)){
                            $contentType->translation($translation->language_code);
                        }
                    }
                }
            }

            return response()->json($contentTypes, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
