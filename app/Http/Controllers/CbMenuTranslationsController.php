<?php

namespace App\Http\Controllers;

use App\CbMenuTranslation;
use App\Entity;
use App\Cb;
use Exception;
use Illuminate\Http\Request;


class CbMenuTranslationsController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($cbKey)
    {
        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();
            $cbMenuTranslations = $cb->cbMenuTranslations()->get();

            if (!empty($cbMenuTranslations)){
                $cbMenuTranslationArray = [];
                foreach ($cbMenuTranslations as $cbMenuTranslation) {
                    $cbMenuTranslationArray[$cbMenuTranslation->code][$cbMenuTranslation->language_code]= $cbMenuTranslation->translation;
                }
                return response()->json(['data' => $cbMenuTranslationArray], 200);
            }

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the CBMenuTranslation list'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeOrUpdate(Request $request) {
        try {
            $cbKey = $request->json('cb_key');
            $code = $request->json('code');
            $translations = $request->json('translations');

            $cb = Cb::whereCbKey($cbKey)->firstOrFail();

            foreach ($translations as $language => $value) {
                $newCbMenuTranslation = $cb->cbMenuTranslations()->updateOrCreate([
                        "code" => $code,
                        "language_code" => $language
                    ], [
                        'translation'           => $value
                    ]
                );

                if ($newCbMenuTranslation->wasRecentlyCreated) {
                    $key = null;
                    do {
                        $rand = str_random(32);
                        if (!($exists = CbMenuTranslation::whereCbMenuTranslationKey($rand)->exists())) {
                            $newCbMenuTranslation->cb_menu_translation_key = $rand;
                            $newCbMenuTranslation->save();
                        }
                    } while ($exists);
                }
            }
            return response()->json('success', 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to Store or Update the CB Menu Translation'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $cbKey, $code) {
        try {
            Cb::whereCbKey($cbKey)->firstOrFail()->cbMenuTranslations()->whereCode($code)->delete();
            return response()->json('OK', 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Cb Translations'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEntityCbsWithTranslations(Request $request)
    {
        try {
            $userRole = $request->json('user');

            if($userRole == 'admin' && !empty($request->json('entity')))
                $entity = Entity::whereEntityKey($request->json('entity'))->firstOrFail();
            else
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

            $entityCbKeys = $entity->entityCbs()->pluck('cb_key');

            if(!empty($entityCbKeys)){
                $cbs = Cb::whereIn("cb_key",$entityCbKeys)
                        ->whereHas("cbMenuTranslations")
                        ->get()
                        ->pluck("title","cb_key");
                return response()->json($cbs);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the CbsEntity list'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function copyFromAnotherCB(Request $request,$destiny)
    {
        try {
            $originCbMenuTranslations = Cb::whereCbKey($request->get("origin"))->firstOrFail()->cbMenuTranslations()->get();
            $destinyCb = Cb::whereCbKey($destiny)->firstOrFail();

            $destinyCb->cbMenuTranslations()->delete();

            foreach ($originCbMenuTranslations as $originCbMenuTranslation) {
                $key = '';
                do {
                    $rand = str_random(32);
                    if (!($exists = CbMenuTranslation::where('cb_menu_translation_key', '=', $rand)->exists())) {
                        $key = $rand;
                    }
                } while ($exists);

                $destinyCb->cbMenuTranslations()->create([
                    "cb_menu_translation_key"   => $key,
                    "code"                      => $originCbMenuTranslation->code,
                    "language_code"             => $originCbMenuTranslation->language_code,
                    "translation"               => $originCbMenuTranslation->translation,
                ]);
            }

            return response()->json('StoreOk', 200);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to copy Cb Translations'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function isCodeUsed($cbKey, $code)
    {
        try {
            if (Cb::whereCbKey($cbKey)->firstOrFail()->cbMenuTranslations()->whereCode($code)->exists())
                return response()->json('false');
            else
                return response()->json('true');
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the CBTranslation getCode'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
