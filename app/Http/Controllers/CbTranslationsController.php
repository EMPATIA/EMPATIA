<?php

namespace App\Http\Controllers;

use App\CbTranslation;
use App\Entity;
use App\Cb;
use Exception;
use Illuminate\Http\Request;
use App\Http\Requests;

/**
 * Class CbsController
 * @package App\Http\Controllers
 */

class CbTranslationsController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $cbKey = $request->input('cb_key');

        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();
            $cbTranslations = $cb->cbTranslations()->get();

            if (!empty($cbTranslations)){
                $cbTranslationArray = [];
                foreach ($cbTranslations as $cbTranslation) {
                    $cbTranslationArray[$cbTranslation->code][$cbTranslation->status][$cbTranslation->language_code]= $cbTranslation->translation;
                }
                return response()->json(['data' => $cbTranslationArray], 200);
            }

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the CBTranslation list'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeOrUpdate(Request $request)
    {
        try {
            $cbKey = $request->json('cb_key');
            $codes = $request->json('code');
            $translations = $request->json('translations');

            $cb = Cb::whereCbKey($cbKey)->firstOrFail();
            $cbTranslation = $cb->cbTranslations()->get();

            if(empty($cbTranslation)){
                foreach ($codes as $code) {
                    foreach ($translations as $status => $value) {
                        $key = null;
                        do {
                            $rand = str_random(32);
                            if (!($exists = CbTranslation::where('cb_translation_key','=',$rand)->exists())) {
                                $key = $rand;
                            }
                        } while ($exists);
                        $language = explode("_", $status);

                        $translation = $cb->cbTranslations()->create(
                            [
                                'cb_translation_key'    => $key,
                                'code'                  => $code,
                                'status'                => $language[0],
                                'language_code'         => $language[1],
                                'translation'           => $value
                            ]
                        );
                    }
                }
                return response()->json('StoreOk', 200);
            }else{
                foreach ($codes as $key => $value) {
                    if($key == 'code'){
                        if(empty($value)){
                            return response()->json('CodeNotExist', 200);
                        } else {
                            if(!$cb->cbTranslations()->whereCode($value)->exists()){

                                foreach ($translations as $status => $item) {

                                    $cbTranslationKey = '';
                                    do {
                                        $rand = str_random(32);
                                        if (!($exists = CbTranslation::where('cb_translation_key','=',$rand)->exists())) {
                                            $cbTranslationKey = $rand;
                                        }
                                    } while ($exists);

                                    $language = explode("_", $status);

                                    $translation = $cb->cbTranslations()->create(
                                        [
                                            'cb_translation_key'    => $cbTranslationKey,
                                            'code'                  => $value,
                                            'status'                => $language[0],
                                            'language_code'         => $language[1],
                                            'translation'           => $item
                                        ]
                                    );
                                }
                                return response()->json('StoreOk', 200);
                            }
                            else{
                                return response()->json('CodeExist');
                            }
                        }
                    } else {
                        if(empty($value)){
                            return response()->json('CodeNotInsert', 200);
                        }else{
                            foreach ($translations as $status => $newTranslation){
                                $translation = $cb->cbTranslations()->whereCode($key)->whereStatus($status)->first();

                                if($translation){
                                    $translation->update([
                                        'translation' => $newTranslation
                                    ]);
                                } else {
                                    $cbTranslationKey = '';
                                    do {
                                        $rand = str_random(32);
                                        if (!($exists = CbTranslation::where('cb_translation_key','=',$rand)->exists())) {
                                            $cbTranslationKey = $rand;
                                        }
                                    } while ($exists);

                                    $language = explode("_", $status);

                                    $translation = $cb->cbTranslations()->create(
                                        [
                                            'cb_translation_key'    => $cbTranslationKey,
                                            'code'                  => $value,
                                            'status'                => $language[0],
                                            'language_code'         => $language[1],
                                            'translation'           => $newTranslation
                                        ]
                                    );
                                }
                            }
                            return response()->json('UpdateOk', 200);
                        }
                    }
                }
            }

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to Store or Update the translation'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $cbKey = $request->json('cb_key');
        $code = $request->json('code');
        try {
            $cb = Cb::where('cb_key','=', $cbKey)->first();

            $cb->cbTranslations()->whereCode($code)->delete();

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
    public function getCbEntity(Request $request)
    {
        try {
            $cbsEntity = [];
            $userRole = $request->json('user');

            if($userRole == 'admin'){
                $entity = Entity::findOrFail($request->json('entity'));
                $entityCbKeys = $entity->entityCbs()->pluck('cb_key');

                if(!empty($entityCbKeys)){
                    foreach ($entityCbKeys as $key) {
                        $cb =  Cb::whereCbKey($key)->first();

                        if(!empty($cb)){
                            $cbTranslation = $cb->cbTranslations()->get();

                            if(!($cbTranslation)->isEmpty()) {
                                $cbsEntity[] = $cb;
                            }
                        }
                    }
                    return response()->json($cbsEntity);
                }
            } elseif($userRole == 'manager'){

                $cb = Cb::all();

                foreach ($cb as $value2) {

                    $cbTranslation = CbTranslation::where('cb_id','=',$value2['id'])->get();

                    if($cbTranslation != '[]'){

                        $cbsEntity[] = $value2;
                    }
                }
                return response()->json($cbsEntity);
            }
        }
        catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the CbsEntity list'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeCodeAdminOrManager(Request $request)
    {
        try {
            $cbKey = $request->json('cb_key');
            $cbId = $request->json('cb');

            $destinationCb = Cb::whereCbKey($cbKey)->firstOrFail();
            $originCb = Cb::findOrFail($cbId);

            $cbTranslations = $originCb->cbTranslations()->get();

            if (!empty($cbTranslations)) {

                foreach ($cbTranslations as $cbTtranslation) {
                    if (!$destinationCb->cbTranslations()->whereCode($cbTtranslation->code)->whereStatus($cbTtranslation->status)->exists() && !empty($cbTtranslation->translation)){

                        $key = '';
                        do {
                            $rand = str_random(32);
                            if (!($exists = CbTranslation::where('cb_translation_key', '=', $rand)->exists())) {
                                $key = $rand;
                            }
                        } while ($exists);

                        $newTranslation = $destinationCb->cbTranslations()->create([
                            'cb_translation_key'    => $key,
                            'code'                  => $cbTtranslation->code,
                            'status'                => $cbTtranslation->status,
                            'language_code'         => $cbTtranslation->language_code,
                            'translation'           => $cbTtranslation->translation,
                        ]);
                    }
                }
                return response()->json('StoreOk', 200);
            }

        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to copy Cb Translations'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function translation(Request $request)
    {
        try {

            $cb = Cb::where('cb_key','=',$request->cbKey)->first();

            $cb_translation=CbTranslation::where('cb_id','=',$cb->id)->where('code','=',$request->code)
                ->where('language_code','=',$request->language)
                ->where('status','=',$request->status)
                ->first();

            return response()->json($cb_translation->translation, 200);

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the CBTranslation translation'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCode(Request $request)
    {
        try {
            $cbKey = $request->input('cb_key');
            $code = $request->input('code');

            $cb = Cb::where('cb_key','=',$cbKey)->first();
            $cb_translation = $cb->cbTranslations()->whereCode($code)->first();

            if (empty($cb_translation)){
                return response()->json('true');
            } else {
                return response()->json('false');
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the CBTranslation getCode'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatusTranslations(Request $request)
    {
        try {
            $cbKey = $request->input('cb_key');
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();
            $cbTranslations = $cb->cbTranslations()->select('code', 'status', 'language_code', 'translation')->get();

            $response = [];

            foreach ($cbTranslations as $cbTranslation){
                $response[$cbTranslation->language_code][$cbTranslation->status][$cbTranslation->code] = $cbTranslation->translation;
            }
            return response()->json($response);

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the CBTranslation getCode'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

}
