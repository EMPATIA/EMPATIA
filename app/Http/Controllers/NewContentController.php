<?php

namespace App\Http\Controllers;

use App\Content;
use App\ContentType;
use App\ContentTypeType;
use App\Entity;
use App\NewContent;
use App\One\One;
use App\Section;
use App\SectionParameter;
use App\SectionType;
use App\SectionTypeParameter;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewContentController extends Controller
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
            $entityKey = $request->header('X-ENTITY-KEY');
            if (!is_null($entityKey)) {

                $contentTypeId = ContentType::whereCode($request->json('content_type_code'))->firstOrFail()->id;
                $siteKey = $request->json('site_key');

                $contents = NewContent::whereContentTypeId($contentTypeId)->whereEntityKey($entityKey)->groupBy("content_key")->get();

                $contents = $contents->map(function ($item, $key) {
                    if($item->active == '0'){
                        return NewContent::whereContentKey($item->content_key)->orderBy('version', 'desc')->first();
                    }
                    return $item;
                });

                if (!is_null($siteKey)) {
                    $contents = $contents->filter(function ($content) use ($siteKey){
                        return ($content->contentSites()->count()==0 || ($content->contentSites()->count()>0 && $content->contentSites()->whereSiteKey($siteKey)->exists()));
                        /* If the content doesn't have any Site associated, it belongs to every site, so it's returned.
                         * Otherwise, the given site should be related to the content to be returned.
                         */
                    });
                }

                return response()->json(['data' => $contents], 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Content Type not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Contents'], 500);
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
            DB::beginTransaction();
            $entityKey = $request->header('X-ENTITY-KEY');
            if(!is_null($entityKey)) {

                //Get Content Type and Content Type Type in case it exists
                $contentType = ContentType::whereCode($request->json('content_type_code'))->firstOrFail();
                $contentTypeType = ContentTypeType::whereContentTypeTypeKey($request->json('content_type_type_key'))->first();
                $code = $request->json('code') ?? null;

                $key = '';
                do {
                    $rand = str_random(32);
                    if (!($exists = NewContent::whereContentKey($rand)->exists())) {
                        $key = $rand;
                    }
                } while ($exists);


                $sites = $request->json('site_keys');

                if (!is_null($code)){
                    //get the content with the same code, if it exists.
                    $sameCodeContent = NewContent::whereCode($code)->first();

                    if (!is_null($sameCodeContent)){
                        //get all the sites associated to the content with the code '$code'
                        $contentSites = $sameCodeContent->contentSites()->pluck('site_key');
                        if (!is_null($sites)) {
                            //get only the sites with no Association to a content with the same code as '$code'
                            $sites = collect($sites)->diff($contentSites);
                        } else {
                            if ($sameCodeContent->entity_key == $entityKey){
                                return  response()->json(['error' => 'Can not add content, Code already exists'], 500);
                            }
                        }
                    }
                }

                //Create the new content
                $content = $contentType->newContents()->create([
                    'content_key' => $key,
                    'entity_key' => $entityKey,
                    'content_type_type_id' => $contentTypeType ?? null,
                    'version' => 1,
                    'code' => $code,
                    'active' => $request->json('active') ?? 0,
                    'name' => $request->json('name') ?? null,
                    'start_date' => $request->json('start_date') ?? null,
                    'end_date' => $request->json('end_date') ?? null,
                    'publish_date' => $request->json('publish_date') ?? null,
                    'highlight' => $request->json('highlight') ?? 0
                ]);

                if (!is_null($sites)){
                    foreach ($sites as $site)
                        $content->contentSites()->create([
                            'site_key' => $site
                        ]);
                }

                $sections = $request->json('sections');

                if (!is_null($sections)) {
                    //Create the Sections
                    $this->saveSections($sections, $content);
                }

                DB::commit();
                return response()->json($content, 201);
            }
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Content Type not Found'], 404);
        }
        catch(QueryException $e){
            return response()->json(['error' => 'Failed to store new Content'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $contentKey
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $contentKey, $version = null)
    {
        try {
            $content = NewContent::whereContentKey($contentKey);
            $versions = $content->get()->unique("version")->map(function($item, $key) {
                return [
                    "version"       => $item->version,
                    "active"        => $item->active,
                    "created_at"    => $item->created_at
                ];
            });

            $content
                ->with(array('sections' => function ($query) {
                    $query->with(array('sectionType', 'sectionParameters' => function($subQuery) {
                        $subQuery->with('sectionTypeParameter');
                    }));
                },'contentSites'));


            if (is_null($version)) {
                if (NewContent::whereContentKey($contentKey)->whereActive(1)->count()==1) {
                    $content = $content->whereActive(1) ->firstOrFail();
                } else {
                    $content = $content->orderBy("version", "desc")->firstOrFail();
                }
            } else
                $content = $content->whereVersion($version)->firstOrFail();

            $content->sections->each(function ($section) use ($request){
                $section->sectionParameters->each(function ($sectionParameter) use ($request) {
                    $sectionParameter->translations();
                    $sectionParameter->sectionTypeParameter();
                    if (!($sectionParameter->sectionTypeParameter->translation($request->header('LANG-CODE')))) {
                        if (!$sectionParameter->sectionTypeParameter->translation($request->header('LANG-CODE-DEFAULT'))) {
                            if (!$sectionParameter->sectionTypeParameter->translation('en')) {
                                $sectionParameter->sectionTypeParameter->name = "";
                                $sectionParameter->sectionTypeParameter->description = "";
                            }
                        }
                    }
                });
            });

            $content["versions_list"] = $versions;
            return response()->json($content, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Content'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $contentKey
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $contentKey)
    {
        $userKey = ONE::verifyToken($request);
        try {
            DB::beginTransaction();
            $entityKey = $request->header('X-ENTITY-KEY');
            if(!is_null($entityKey)) {
                $content = NewContent::whereContentKey($contentKey)->orderBy('version', 'desc')->firstOrFail();
                $code = (!empty($content->code)) ? $content->code : ($request->json('code') ?? null);
                $contentType = ContentType::findOrFail($content->content_type_id);
                $version = $content->version;

                //Create the new content
                $content = $contentType->newContents()->create([
                    'content_key' => $contentKey,
                    'entity_key' => $entityKey,
                    'content_type_type_id' => $contentTypeType ?? null,
                    'version' => $version + 1,
                    'code' => $code,
                    'active' => $request->json('active') ?? 0,
                    'name' => $request->json('name') ?? null,
                    'start_date' => $request->json('start_date') ?? null,
                    'end_date' => $request->json('end_date') ?? null,
                    'publish_date' => $request->json('publish_date') ?? null,
                    'highlight' => $request->json('highlight') ?? 0
                ]);

                $sections = $request->json('sections');
                if (!is_null($sections)) {
                    //Create the Sections
                    $this->saveSections($sections, $content);
                }

                $sites = $request->json('site_keys');
                if (!is_null($sites)){
                    foreach ($sites as $site)
                        $content->contentSites()->create([
                            'site_key' => $site
                        ]);
                }

                DB::commit();
                return response()->json($content, 200);
            }
        } catch (ModelNotFoundException $e) {

            return response()->json(['error' => 'Failed to get existing content'], 404);
        }catch(QueryException $e){
            return response()->json(['error' => 'Failed to update new Content'], 500);
        }
        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $contentKey
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $contentKey)
    {
        ONE::verifyToken($request);

        try {
            NewContent::whereContentKey($contentKey)->delete();
            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Content  not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete the Content'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function toggleActiveStatusOfVersion(Request $request, $contentKey, $version) {
        try {
            if ($request->has("newStatus")) {
                $newStatus = $request->get("newStatus",0);
                NewContent::whereContentKey($contentKey)->update(["active" => 0]);
                $content = NewContent::whereContentKey($contentKey)->whereVersion($version)->firstOrFail();
                $content->active = $newStatus;
                $content->save();

                return response()->json($content, 200);
            } else
                return response()->json(["erro" => "New Status not received"],400);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update the status'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param $sections
     * @param $content
     * @return bool
     */
    private function saveSections($sections, $content){

        try {
            foreach ($sections as $sectionItem) {
                $sectionType = SectionType::whereSectionTypeKey($sectionItem['section_type_key'])->firstOrFail();

                $key = '';
                do {
                    $rand = str_random(32);
                    if (!($exists = Section::whereSectionKey($rand)->exists())) {
                        $key = $rand;
                    }
                } while ($exists);

                $section = $content->sections()->create([
                    'section_type_id' => $sectionType->id,
                    'section_key' => $key,
                    'code' => $sectionItem['code']
                ]);

                $sectionParameters = $sectionItem['section_parameters'];

                //Create the Section Parameters
                if (!is_null($sectionParameters)) {
                    foreach ($sectionParameters as $sectionParameterItem) {
                        //Get the Section Type Parameter
                        $sectionTypeParameter = SectionTypeParameter::whereSectionTypeParameterKey($sectionParameterItem['section_type_parameter_key'])->firstOrFail();

                        $key = '';
                        do {
                            $rand = str_random(32);
                            if (!($exists = SectionParameter::whereSectionParameterKey($rand)->exists())) {
                                $key = $rand;
                            }
                        } while ($exists);

                        $sectionParameter = $section->sectionParameters()->create([
                            'section_parameter_key' => $key,
                            'section_type_parameter_id' => $sectionTypeParameter->id,
                            'code' => $sectionParameterItem['code'],
                            'value' => $sectionParameterItem['value'] ?? null
                        ]);

                        //Create The Section Parameter Translations
                        if (is_null($sectionParameter->value) && (!empty($sectionParameterItem['translations'])) && (is_array($sectionParameterItem['translations']))) {
                            foreach ($sectionParameterItem['translations'] as $translation) {
                                if (isset($translation['language_code']) && isset($translation['value'])) {
                                    $sectionParameter->sectionParameterTranslations()->create([
                                        'language_code' => $translation['language_code'],
                                        'value' => $translation['value'] ?? null
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function publicShow(Request $request, $contentType, $contentKey) {
        try {
            $content = NewContent::whereContentKey($contentKey)->whereActive(1)->firstOrFail();
            $canBeShown = true;
            if ($content->contentType()->firstOrFail()->code!=$contentType)
                $canBeShown = false;

            if ($content->entity_key!=$request->header('X-ENTITY-KEY',null))
                $canBeShown = false;

            if ($content->contentSites()->count()>0 && !is_null($request->header('X-SITE-KEY',null)) && $content->contentSites()->whereSiteKey($request->header('X-SITE-KEY',null))->count()==0)
                $canBeShown = false;

            if ($canBeShown) {
                if (!is_null($request->header('LANG-CODE',null)))
                    $languageCode = $request->header('LANG-CODE');
                else
                    $languageCode = $request->header('LANG-CODE-DEFAULT',null);

                $content->sections->each(function ($section) use ($languageCode) {
                    $section->section_type = $section->sectionType()->first();
                    $section->sectionParameters->each(function ($item) use ($languageCode) {
                        $item->translation($languageCode);
                        $item->section_type_parameter = $item->sectionTypeParameter()->first();
                    });
                });
                return response()->json($content, 200);
            } else
                return response()->json(['error' => 'Failed to retrieve the Content'], 400);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Content'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    public function publicIndex(Request $request, $contentType) {
        try {
            $page = $request->get("page",0);
            $contentsPerPage = $request->get("contentsPerPage",6);
            $siteKey = $request->header('X-SITE-KEY',null);


            $query = NewContent::
                with("sections.sectionType","sections.sectionParameters.sectionTypeParameter")

                ->whereEntityKey($request->header('X-ENTITY-KEY',null))
                ->whereActive(1)
                ->whereHas("contentType",function ($q) use ($contentType) {
                    $q->where("code","=",$contentType);
                })
                ->where(function ($query) use ($siteKey) {
                    $query
                        ->whereDoesntHave("contentSites")
                        ->orWhereHas("contentSites", function($q) use ($siteKey) {
                            $q->where("site_key","=",$siteKey);
                        });
                });

            if ($contentType=="news")
                $query = $query->orderBy("start_date","desc");

            $contentsCount = $query->count();

            if ($contentType=="events" || $contentType=="news")
                $query = $query->orderBy("start_date","desc");
            $contents = $query->skip($page*$contentsPerPage)->limit($contentsPerPage)->get();

            if (!is_null($request->header('LANG-CODE',null)))
                $languageCode = $request->header('LANG-CODE');
            else
                $languageCode = $request->header('LANG-CODE-DEFAULT',null);

            foreach ($contents as $content) {
                foreach ($content->sections as $section) {
                    foreach ($section->sectionParameters as $sectionParameter) {
                        $sectionParameter->translation($languageCode);
                    }
                }
            }

            return response()->json(["contents" => $contents, "contentsCount" => $contentsCount]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Content'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function previewShow(Request $request, $contentKey, $contentVersion) {
        try {
            $content = NewContent::whereContentKey($contentKey)->whereVersion($contentVersion)->firstOrFail();
            $canBeShown = true;
            if ($content->entity_key!=$request->header('X-ENTITY-KEY',null))
                $canBeShown = false;

            if ($content->contentSites()->count()>0 && !is_null($request->header('X-SITE-KEY',null)) && $content->contentSites()->whereSiteKey($request->header('X-SITE-KEY',null))->count()==0)
                $canBeShown = false;

            if ($canBeShown) {
                if (!is_null($request->header('LANG-CODE',null)))
                    $languageCode = $request->header('LANG-CODE');
                else
                    $languageCode = $request->header('LANG-CODE-DEFAULT',null);

                $content->sections->each(function ($section) use ($languageCode) {
                    $section->section_type = $section->sectionType()->first();
                    $section->sectionParameters->each(function ($item) use ($languageCode) {
                        $item->translation($languageCode);
                        $item->section_type_parameter = $item->sectionTypeParameter()->first();
                    });
                });
                return response()->json($content, 200);
            }

            return response()->json(['error' => 'Failed to retrieve the Content'], 400);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Content'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    public function codeShow(Request $request) {
        try {
            $contentCodes = $request->json("codes");
            $entityKey = $request->header('X-ENTITY-KEY',null);
            $siteKey = $request->header('X-SITE-KEY',null);

            if (!is_null($entityKey) && !is_null($siteKey)) {
                $primaryLanguage = $request->header('LANG-CODE');
                $defaultLanguage = $request->header('LANG-CODE-DEFAULT');

                $contents = NewContent::
                    with([
                        "sections.sectionType","sections.sectionParameters.sectionTypeParameter",
                        "sections.sectionParameters.sectionParameterTranslations" => function($q) use($primaryLanguage,$defaultLanguage) {
                            $q->orderByRaw("FIELD(language_code,'".$defaultLanguage."','".$primaryLanguage."') DESC");
                        },"contentSites"])
                    ->where(function($q) use ($entityKey) {
                        $q->where("entity_key","=",$entityKey)->where("active","=",1);
                    })
                    ->where(function ($q) use ($siteKey) {
                        $q->whereDoesntHave("contentSites")
                            ->orWhereHas("contentSites", function($q) use ($siteKey) {
                                $q->where("site_key","=",$siteKey);
                            });
                    });
                
                if (is_array($contentCodes))
                    $contents = $contents->whereIn("code",$contentCodes);
                else
                    $contents = $contents->where("code",$contentCodes);

                $contents = $contents->get();
                
                foreach ($contents as $content) {
                    foreach ($content->sections as $section) {
                        foreach ($section->sectionParameters as $sectionParameter) {
                            if ($sectionParameter->sectionParameterTranslations->count()>0)
                                $sectionParameter->value = ($sectionParameter->sectionParameterTranslations->first()->value ?? ($sectionParameter->value ?? null));

                            unset($sectionParameter->sectionParameterTranslations);
                        }
                    }
                }

                $contents = is_array($contentCodes) ? $contents->keyBy("code") : $contents->first();

                return response()->json($contents, 200);
            }
            return response()->json(['error' => 'Failed to retrieve the Content'], 401);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Content'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function getLastOf(Request $request, $contentType, $count) {
        try {
            $contentTypeId = ContentType::whereCode($contentType)->firstOrFail()->id;

            if ($contentType=="events" || $contentType=="news")
                $contents = NewContent::whereContentTypeId($contentTypeId)->whereActive(1)->orderBy("start_date","desc")->get();
            else
                $contents = NewContent::whereContentTypeId($contentTypeId)->whereActive(1)->orderBy("created_at","desc")->get();

            $contentsToReturn = [];

            if (!is_null($request->header('LANG-CODE',null)))
                $languageCode = $request->header('LANG-CODE');
            else
                $languageCode = $request->header('LANG-CODE-DEFAULT',null);

            foreach ($contents as $content) {
                if (count($contentsToReturn)>=$count)
                    break;

                $canBeShown = true;
                if ($content->contentType()->firstOrFail()->code!=$contentType)
                    $canBeShown = false;

                if ($content->entity_key!=$request->header('X-ENTITY-KEY',null))
                    $canBeShown = false;

                if ($content->contentSites()->count()>0 && !is_null($request->header('X-SITE-KEY',null)) && $content->contentSites()->whereSiteKey($request->header('X-SITE-KEY',null))->count()==0)
                    $canBeShown = false;

                if ($canBeShown) {
                    $content->sections->each(function ($section) use ($languageCode) {
                        $section->section_type = $section->sectionType()->first();
                        $section->sectionParameters->each(function ($item) use ($languageCode) {
                            $item->translation($languageCode);
                            $item->section_type_parameter = $item->sectionTypeParameter()->first();
                        });
                    });

                    $contentsToReturn[] = $content;
                }
            }

            return response()->json(['data' => $contentsToReturn], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Content'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}