<?php

namespace App\Http\Controllers;

use App\Cb;
use App\Flag;
use App\FlagAttachmentTranslation;
use App\FlagType;
use App\One\One;
use App\Post;
use App\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class FlagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    /**
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFlagsFromCb(Request $request, $cbKey)
    {


        try {

            $cb = Cb::with([
                "flags",
                "flags.translations" => function ($q) use ($request) {
                    $q->where('language_code', '=', $request->header('LANG-CODE'));
                },
            ])->whereCbKey($cbKey)->firstOrFail();
            
            return response()->json(['data' => $cb->flags], 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }

    /**
     * get translation of resource
     * @param $flag
     * @param $request
     * @return mixed
     */
    public static function getTranslation($flag, $request)
    {
        if (!($flag->translation($request->header('LANG-CODE')))) {
            if (!$flag->translation($request->header('LANG-CODE-DEFAULT')))
                $flag->setAttribute('title','no translation');
            $flag->setAttribute('description','no translation');
        }

        return $flag;
    }


    public function getAllTranslations($flag)
    {
        $translations =  $flag->translations()->get();
        foreach ($translations as $translation){
            $flagTranslations[$translation->language_code] = $translation;
        }
        $flag->translations = $flagTranslations;
    }

    /**
     * set translation of resource
     * @param $flag
     * @param $translations
     */
    public function setTranslations($flag, $translations)
    {
        if(!empty($flag->translations()->get())){
            $flag->translations()->delete();
        }

        foreach ($translations as $translation){
            $flag->translations()->create(
                [
                    'language_code' => $translation['language_code'],
                    'title'     => $translation['title'],
                    'description'   => empty($translation['description']) ? null : $translation['description']
                ]
            );

        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $userKey = ONE::verifyLogin($request);
            $attributes = $request->json('attributes');
            $flag = Flag::create(
                [
                    'flag_type_id' => $attributes['flag_type'],
                    'position' => 0,
                ]
            );
            $this->setFlagAttributes($flag,$attributes);

            if($request->json('translations')){
                $translations = $request->json('translations');
                $this->setTranslations($flag,$translations);
            }

            if($request->json('attachmentCode')){
                $attributes['userKey'] = $userKey;
                $this->createNewAttachment($request->json('attachmentCode'),$flag,$attributes);
            }

            $this->getAllTranslations($flag,$request);

            return response()->json(['data' => $flag], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Parameter'], 500);
        }
    }


    public function createNewAttachment($attachmentCode,$flag,$attributes,$translations = [])
    {
        $active = !empty($attributes["status"]??"") ? $attributes["status"] : false;
        switch ($attachmentCode) {
            case 'CB':
                $cb = Cb::whereCbKey($attributes['cbKey'])->firstOrFail();
                $cb->flags()->attach($flag,['active' => $active , 'created_by' => $attributes['userKey']]);

                break;
            case 'TOPIC':
                $topic = Topic::whereTopicKey($attributes['topicKey'])->firstOrFail();
                
                if ($attributes["inactivateOldFlags"]??false) {
                    $relations = $topic->flags()->get();
                    foreach ($relations as $relation){
                        $relation->pivot->active = false;
                        $relation->pivot->save();
                    }
                }

                $topic->flags()->attach($flag,['active' => $active , 'created_by' => $attributes['userKey']]);
                $relationId = $topic->flags()->whereFlagId($flag->id)->withPivot("id")->orderBy('pivot_created_at', 'desc')->first()->pivot->id;
                if(!empty($translations)){
                    $this->setAttachmentTranslation($relationId,$translations,$attachmentCode);
                }
                break;
            case 'POST':
                $post = Post::wherePostKey($attributes['postKey'])->firstOrFail();

 
                if ($attributes["inactivateOldFlags"]??false) {
                    $relations = $post->flags()->get();
                    foreach ($relations as $relation){
                        $relation->pivot->active = false;
                        $relation->pivot->save();
                    }
                }

                $post->flags()->attach($flag,['active' => $active , 'created_by' => $attributes['userKey']]);
                $relationId = $post->flags()->whereFlagId($flag->id)->withPivot("id")->orderBy('pivot_created_at', 'desc')->first()->pivot->id;
                if(!empty($translations)){
                    $this->setAttachmentTranslation($relationId,$translations,$attachmentCode);
                }
                break;
        }
    }

    public function setAttachmentTranslation($element,$translations,$attachmentCode)
    {
        foreach ($translations as $languageCode => $translation){
            FlagAttachmentTranslation::create(
                [
                    'relation_id'       =>  $element,
                    'relation_type_code'      => $attachmentCode,
                    'language_code' => $languageCode,
                    'active'        => true,
                    'description'   => $translation
                ]
            );
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
            $flag = Flag::with("translations")->findOrFail($id);

            $flagFlagType = FlagType::with("currentLanguageTranslation")->findOrFail($flag->flag_type_id);




            $flagTypes = FlagType::with(
                ["currentLanguageTranslation" => function ($q) use ($request) {
                    $q->where('language_code', '=', $request->header('LANG-CODE'));
            }])->get();

            $flag->flag_type = $flagFlagType;
                $flag->available_flag_types = $flagTypes;

            return response()->json(['data' => $flag], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Flag not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the flag'], 500);
        }
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
        try {
            $attributes = $request->json('attributes');
            $flag = Flag::findOrFail($id);

            $this->setFlagAttributes($flag,$attributes);

            if($request->json('translations')){
                $translations = $request->json('translations');
                $this->setTranslations($flag,$translations);
            }

            if($request->json('attachmentCode')){
                $this->createNewAttachment($request->json('attachmentCode'),$flag,$attributes);
            }

            $this->getAllTranslations($flag,$request);

            return response()->json(['data' => $flag], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Parameter'], 500);
        }
    }


    public function setFlagAttributes($flag,$attributes)
    {
        if(isset($attributes['private_flag'])){
            $flag->private_flag = true;
        }else{
            if($flag->private_flag == true){
                $flag->private_flag = null;
            }
        }

        if(isset($attributes['flag_visible'])){
            $flag->flag_visible = true;
        }else{
            if($flag->flag_visible == true){
                $flag->flag_visible = null;
            }
        }

        if(isset($attributes['public_visible'])){
            $flag->public_visible = true;
        }else{
            if($flag->public_visible == true){
                $flag->public_visible = null;
            }
        }

        $flag->save();

    }

    public function attachFlag(Request $request)
    {
        $userKey = ONE::verifyLogin($request);
        try {
            $attachmentCode = $request->json('attachmentCode');            

            $inactivateOldFlags = true;
            foreach ($request->get("flag") as $flagId => $flagData) {
                $flag = Flag::findOrFail($flagId);
                
                $translations = $flagData["translation"]??[];
                
                $flagData["userKey"] = $userKey;
                $flagData["inactivateOldFlags"] = $inactivateOldFlags;
                
                if (!empty($request->get("topicKey")))
                    $flagData["topicKey"] = $request->get("topicKey");
                elseif (!empty($request->get("postKey")))
                    $flagData["postKey"] = $request->get("postKey");
                
                $this->createNewAttachment($attachmentCode,$flag,$flagData,$translations);

                $inactivateOldFlags = false;
            }
            
            return response()->json(['data' => 'OK'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Parameter'], 500);
        }
    }


    public function getElementFlagHistory(Request $request)
    {
            $attachmentCode = $request->json('attachmentCode');

            switch ($attachmentCode) {
                case 'TOPIC':
                    $topic = Topic::whereTopicKey($request->json('elementKey'))->firstOrFail();
                    
                    $flagHistory = $topic->flags()->with(
                        ["translations" => function ($q) use ($request) {
                            $q->where('language_code', '=', $request->header('LANG-CODE'));
                            }
                        ])->withPivot("id")->orderByDesc("pivot_id")->get();
                    foreach ($flagHistory as $flag){
                        $description = '';
                        if(FlagAttachmentTranslation::whereRelationId($flag->pivot->id)->exists()){
                            if($firstFetch = FlagAttachmentTranslation::whereRelationId($flag->pivot->id)->whereLanguageCode($request->header('LANG-CODE'))->first()){
                                $description = $firstFetch->description;
                            }else{
                                $description = FlagAttachmentTranslation::whereRelationId($flag->pivot->id)->first()->description;
                            }
                        }
                        $flag['attachmentDescription'] = $description;
                    }
                    return response()->json(['data' => $flagHistory], 200);
                    break;
                case 'POST':
                    $post = Post::wherePostKey($request->json('elementKey'))->firstOrFail();
                    $flagHistory = $post->flags()->with(
                        ["translations" => function ($q) use ($request) {
                            $q->where('language_code', '=', $request->header('LANG-CODE'));
                            }
                        ])->withPivot("id")->orderByDesc("pivot_id")->get();
                    foreach ($flagHistory as $flag){
                        $description = '';
                        if(FlagAttachmentTranslation::whereRelationId($flag->pivot->id)->exists()){
                            if($firstFetch = FlagAttachmentTranslation::whereRelationId($flag->pivot->id)->whereLanguageCode($request->header('LANG-CODE'))->first()){
                                $description = $firstFetch->description;
                            }else{
                                $description = FlagAttachmentTranslation::whereRelationId($flag->pivot->id)->first()->description;
                            }
                        }
                        $flag['attachmentDescription'] = $description;
                    }
                    return response()->json(['data' => $flagHistory], 200);
                    break;
            }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Flag::destroy($id);
            return response()->json('Ok', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Flag not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Flag'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function toggleActiveStatus(Request $request) {
        try{
            $relationId = $request->get("relationId");
            $elementKey = $request->get("elementKey");
            $status = $request->get("status");
            $attachmentCode = $request->get("attachmentCode");

            switch ($attachmentCode) {
                case 'TOPIC':
                    $topic = Topic::whereTopicKey($elementKey)->firstOrFail();
                    \DB::statement("UPDATE `flag_topic` set `updated_at` = ?, `active` = ? WHERE `id` = ?",[Carbon::now(), $status, $relationId]);
                    
                    break;
                case 'POST':
                    $post = Post::wherePostKey($elementKey)->firstOrFail();
                    \DB::statement("UPDATE `flag_post` set `updated_at` = ?, `active` = ? WHERE `id` = ?",[Carbon::now(), $status, $relationId]);
                    
                    break;
            }

            return response()->json('Ok', 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to toggle active status'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
