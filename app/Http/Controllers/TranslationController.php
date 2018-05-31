<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Translation;
use App\Cb;
use App\Site;
use App\TranslationCode;
use App\TranslationLanguage;

class TranslationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $translations = [];

        if(!empty($request->site_key)) {
            $site = Site::where('key', '=', $request->site_key)->first();

            if(empty($site))
                return response()->json("wrong site key", 400);

            $translations = $site->translationCode()->with("translationLanguage:translation_code_id,language_code,translation")->get(["id", "code"]);
        } else {
            $cb = CB::whereCbKey($request->cb_key)->first();

            if(empty($cb))
                return response()->json("wrong cb key", 400);

            $translations = $cb->translationCode()->with("translationLanguage:translation_code_id,language_code,translation")->get(["id", "code"]);
        }
        
        return response()->json($translations, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $code = $request->code;
        $id = $request->id;
        $cbKey = $request->cb_key;
        $siteKey = $request->site_key;
        $languageCode = $request->language_code;


        if(!empty($cbKey)){
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();
            $site = null;
        }

        if(!empty($siteKey)){
            $site = Site::where('key', '=', $siteKey)->firstOrFail();
            $cb = null;
        }

        if(!is_null($cb)){

            if(!empty($code)){
                $translations = $cb->translationCode();
                $codeExist = $translations->where('code', '=', $request->code);
                if(!empty($codeExist->first())){
                    return response()->json(["code" => $codeExist->first(), "new" => false], 200);
                }
                else{
                    $translation = $cb->translationCode()->create([
                        'cb_id'=> $cb->id ?? null,
                        'code' => $request->code
                    ]);
                    return response()->json(["code" => $translation, "new" => true], 200);
                }
            }
            else{
                $translations = $cb->translationCode()->with('translationLanguage');
                $translation = $translations->where('id', '=', $id)->first();

                $subTranslation = $translation->translationLanguage()->where('language_code', '=', $languageCode)->first();
                if(!is_null($subTranslation) && !empty($subTranslation)){
                    $subTranslation->translation = $request->translation;
                    $subTranslation->save();

                    return response()->json("translation updated", 200);
                }
                else{
                    $translation = $translation->translationLanguage()->create([
                        'translation_code_id' => $id,
                        'language_code' => $languageCode,
                        'translation' => $request->translation
                    ]);

                    return response()->json("new translation stored", 200);
                }
            }
        }

        if(!is_null($site)){
            
            if(!empty($code)){
                $translations = $site->translationCode();
                $codeExist = $translations->where('code', '=', $request->code);
                if(!empty($codeExist->first())){
                    return response()->json(["code" => $codeExist->first(), "new" => false], 200);
                }
                else{
                    $translation = $site->translationCode()->create([
                        'site_id'=> $site->id ?? null,
                        'code' => $request->code
                    ]);
                    return response()->json(["code" => $translation, "new" => true], 200);
                }
            }
            else{
                $translations = $site->translationCode()->with('translationLanguage');
                $translation = $translations->where('id', '=', $id)->first();

                $subTranslation = $translation->translationLanguage()->where('language_code', '=', $languageCode)->first();
                if(!is_null($subTranslation) && !empty($subTranslation)){
                    $subTranslation->translation = $request->translation;
                    $subTranslation->save();

                    return response()->json("translation updated", 200);
                }
                else{
                    $translation = $translation->translationLanguage()->create([
                        'translation_code_id' => $id,
                        'language_code' => $languageCode,
                        'translation' => $request->translation
                    ]);

                    return response()->json("new translation stored", 200);
                }
            }
        }


        return response()->json("ok", 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
    }

    public function deleteLines(Request $request)
    {
        try{

            if(!empty($request->site_key)){
                $site = Site::where('key', '=', $request->site_key)->firstOrFail();
                $translationCode = $site->translationCode()->where('code', '=', $request->code)->first();
                $translationLanguage = $translationCode->translationLanguage()->where('translation_code_id', '=', $request->id);
                $translationCode->delete();
                $translationLanguage->delete();
                return response()->json("delete ok", 200);
            }

            if(!empty($request->cb_key)){
                $cb = Cb::whereCbKey($request->cb_key)->firstOrFail();
                $translationCode = $cb->translationCode()->where('code', '=', $request->code)->first();
                $translationLanguage = $translationCode->translationLanguage()->where('translation_code_id', '=', $request->id);
                $translationCode->delete();     
                $translationLanguage->delete();           
                
            return response()->json("delete ok", 200);
            }

        }catch(Exception $e){

        }
    }
    
    public function getTranslation(Request $request)
    {
        $translations = [];
        $lang = $request->language_code;
        if(empty($lang))
            return response()->json("no language code", 400);

        if(!empty($request->site_key)) {
            $site = Site::where('key', '=', $request->site_key)->first();

            if(empty($site))
                return response()->json("wrong site key", 400);

            $translations = \DB::table("translation_codes")->select("code", "translation")->join("translation_languages", "translation_languages.translation_code_id", "=", "translation_codes.id")->where("site_id", "=", $site->id)->where("language_code", "=", $lang)->get()->keyBy("code");

        } else {
            $cb = CB::whereCbKey($request->cb_key)->first();

            if(empty($cb))
                return response()->json("wrong cb key", 400);

                $translations = \DB::table("translation_codes")->select("code", "translation")->join("translation_languages", "translation_languages.translation_code_id", "=", "translation_codes.id")->where("cb_id", "=", $cb->id)->where("language_code", "=", $lang)->get()->keyBy("code");
        }

        return response()->json($translations, 200);
    }

    public function uploadFileTranslation(Request $request)
    {
        $new = 0;
        $updated = 0;
        $id = null;
        $cbKey = $request->cbKey;
        $siteKey = $request->siteKey;
        $languageCode = " ";
        $newTranslation = 0;

        $content = explode("\r\n", $request->file);

        $lang = explode(",",trim($content[0]));
        if(count($lang) <= 1 || $lang[0] != "code") {
            return response()->json( "File doesn't contain the header line", 400);
        }

        $lang = array_except($lang, "0");

        for($i = 1; $i < count($content); $i++) {
            $line = explode(",", $content[$i]);
            if (count($line) <= 1) continue;

            $code = $line[0];

            if(!empty($cbKey)){
                $cb = Cb::whereCbKey($cbKey)->firstOrFail();
                $site = null;
            }

            if(!empty($siteKey)){
                $site = Site::where('key', '=', $siteKey)->firstOrFail();
                $cb = null;
            }

            if(!is_null($cb)){
                if(!empty($code)){
                    $translations = $cb->translationCode();
                    $codeExist = $translations->where('code', '=', $code);
                    if(!empty($codeExist->first())){
                        $newTranslation = 0;
                    }
                    else{
                        $translation = $cb->translationCode()->create([
                            'cb_id'=> $cb->id ?? null,
                            'code' => $code
                        ]);
                        $newTranslation = 1;
                    }
                }
            }

            if(!is_null($site)){

                if(!empty($code)){
                    $translations = $site->translationCode();
                    $codeExist = $translations->where('code', '=', $code);
                    if(!empty($codeExist->first())){
                        $newTranslation = 0;
                        $id = $codeExist->first()->id;
                    }
                    else{
                        $translation = $site->translationCode()->create([
                            'site_id'=> $site->id ?? null,
                            'code' => $code
                        ]);
                        $newTranslation = 1;
                        $id = $translation->id;
                    }
                }
            }

            if ($newTranslation == 1) $new++;
            else $updated++;

            for ($j = 1; $j < count($line); $j++) {
                if ($j > count($lang)) break;
                if ($line[$j] != ''){

                    if(!empty($cbKey)){
                        $cb = Cb::whereCbKey($cbKey)->firstOrFail();
                        $site = null;
                    }
                    if(!empty($siteKey)){
                        $site = Site::where('key', '=', $siteKey)->firstOrFail();
                        $cb = null;
                    }

                    if(!is_null($cb)){
                        $translations = $cb->translationCode()->with('translationLanguage');
                        $translation = $translations->where('id', '=', $id)->first();

                        $subTranslation = $translation->translationLanguage()->where('language_code', '=', $lang[$j])->first();
                        if(!is_null($subTranslation) && !empty($subTranslation)){
                            $subTranslation->translation = $line[$j];
                            $subTranslation->save();
                        }
                        else{
                            $translation = $translation->translationLanguage()->create([
                                'translation_code_id' => $id,
                                'language_code' => $lang[$j],
                                'translation' => $line[$j]
                            ]);
                        }
                    }

                    }
                    if(!is_null($site)){
                        $translation = $translations->where('id', '=', $id)->first();
                        $subTranslation = $translation->translationLanguage()->where('language_code', '=', $lang[$j])->first();
                        if(!is_null($subTranslation) && !empty($subTranslation)){
                            $subTranslation->translation = $line[$j];
                            $subTranslation->save();
                        }
                        else{
                            $translation = $translation->translationLanguage()->create([
                                'translation_code_id' => $id,
                                'language_code' => $lang[$j],
                                'translation' => $line[$j]
                            ]);
                        }
                    }
                }
            }


        return response()->json(['new' => $new, 'update' => $updated] , 200);
    }

}
