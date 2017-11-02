<?php

namespace App\Http\Controllers;

use App\Content;
use App\ContentFile;
use App\ContentTranslation;
use App\ContentType;
use App\ContentTypeType;
use App\One\One;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Exception;
use Carbon\Carbon;

/**
 * Class ContentsController
 * @package App\Http\Controllers
 */
class ContentsController extends Controller
{

    protected $required = [
        /* Keys required to store or update contents */
        'store_update' => ['type'],
        /* Keys required to add or update files */
        'add_update_file' => ['file_id', 'name', 'type_id'],
        /* Keys required to reorder files */
        'order_file' => ['movement']
    ];

    /**
     * Class ContentsController
     * @package App\Http\Controllers
     */

    /**
     * @SWG\Tag(
     *   name="Contents Method",
     *   description="Everything about Contents Method",
     * )
     *
     *  @SWG\Definition(
     *      definition="contentsMethodErrorDefault",
     *      required={"error"},
     *      @SWG\Property( property="error", type="string", format="string")
     *  )
     *
     *  @SWG\Definition(
     *   definition="contentCreate",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           required={"type"},
     *      @SWG\Property(property="type", format="string", type="string"),
     *      @SWG\Property(property="fixed", format="integer", type="integer"),
     *      @SWG\Property(property="clean", format="integer", type="integer"),
     *      @SWG\Property(property="published", format="integer", type="integer"),
     *      @SWG\Property(property="translations", type="array", @SWG\Items(ref="#/definitions/translations"))
     *       )
     *   }
     * )
     *
     * @SWG\Definition(
     *   definition="contentUpdate",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *      @SWG\Property(property="type", format="string", type="string"),
     *      @SWG\Property(property="fixed", format="integer", type="integer"),
     *      @SWG\Property(property="clean", format="integer", type="integer"),
     *      @SWG\Property(property="published", format="integer", type="integer"),
     *      @SWG\Property(property="translations", type="array", @SWG\Items(ref="#/definitions/translations"))
     *       )
     *   }
     * )
     *

     *
     *
     * @SWG\Definition(
     *   definition="contentReply",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           @SWG\Property(property="id", format="integer", type="integer"),
     *           @SWG\Property(property="content_key", format="string", type="string"),
     *           @SWG\Property(property="type_id", format="integer", type="integer"),
     *           @SWG\Property(property="fixed", format="integer", type="integer"),
     *           @SWG\Property(property="clean", format="integer", type="integer"),
     *           @SWG\Property(property="published", format="integer", type="integer"),
     *           @SWG\Property(property="start_date", format="date", type="string"),
     *           @SWG\Property(property="end_date", format="date", type="string"),
     *           @SWG\Property(property="publish_date", format="date", type="string"),
     *           @SWG\Property(property="title", format="string", type="string"),
     *           @SWG\Property(property="summary", format="string", type="string"),
     *           @SWG\Property(property="content", format="string", type="string"),
     *           @SWG\Property(property="content_id", format="integer", type="integer"),
     *           @SWG\Property(property="version", format="integer", type="integer"),
     *           @SWG\Property(property="link", format="string", type="string"),
     *           @SWG\Property(property="enabled", format="integer", type="integer"),
     *           @SWG\Property(property="created_by", format="string", type="string"),
     *           @SWG\Property(property="docs_main", format="integer", type="integer"),
     *           @SWG\Property(property="docs_side", format="integer", type="integer"),
     *           @SWG\Property(property="highlight", format="integer", type="integer"),
     *           @SWG\Property(property="slideshow", format="integer", type="integer")
     *       )
     *   }
     * )
     *
     *
     *
     *
     *   @SWG\Definition(
     *   definition="contentCreateReply",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           @SWG\Property(property="id", format="integer", type="integer"),
     *           @SWG\Property(property="content_key", format="string", type="string"),
     *           @SWG\Property(property="type_id", format="integer", type="integer"),
     *           @SWG\Property(property="fixed", format="integer", type="integer"),
     *           @SWG\Property(property="clean", format="integer", type="integer"),
     *           @SWG\Property(property="start_date", format="date", type="string"),
     *           @SWG\Property(property="end_date", format="date", type="string"),
     *           @SWG\Property(property="publish_date", format="date", type="string")
     *       )
     *   }
     * )
     *
     *
     *
     *   @SWG\Definition(
     *   definition="contentDeleteReply",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     *   @SWG\Definition(
     *   definition="contentUpdateReply",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           @SWG\Property(property="content", type="array", @SWG\Items(ref="#/definitions/content")),
     *           @SWG\Property(property="version", format="integer", type="integer")
     *       )
     *   }
     * )
     *
     *   @SWG\Definition(
     *   definition="content",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           @SWG\Property(property="id", format="integer", type="integer"),
     *           @SWG\Property(property="content_key", format="string", type="string"),
     *           @SWG\Property(property="type_id", format="integer", type="integer"),
     *           @SWG\Property(property="fixed", format="integer", type="integer"),
     *           @SWG\Property(property="clean", format="integer", type="integer"),
     *           @SWG\Property(property="start_date", format="date", type="string"),
     *           @SWG\Property(property="end_date", format="date", type="string"),
     *           @SWG\Property(property="publish_date", format="date", type="string")
     *       )
     *   }
     * )
     *
     *
     *
     *   @SWG\Definition(
     *   definition="translations",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           required={"language_code", "title"},
     *           @SWG\Property(property="language_code", format="string", type="string"),
     *           @SWG\Property(property="title", format="string", type="string"),
     *           @SWG\Property(property="summary", format="string", type="string"),
     *           @SWG\Property(property="content", format="string", type="string"),
     *           @SWG\Property(property="version", format="integer", type="integer"),
     *           @SWG\Property(property="enabled", format="integer", type="integer"),
     *           @SWG\Property(property="link", format="string", type="string"),
     *           @SWG\Property(property="docs_main", format="integer", type="integer"),
     *           @SWG\Property(property="docs_side", format="integer", type="integer"),
     *           @SWG\Property(property="highlight", format="integer", type="integer"),
     *           @SWG\Property(property="slideshow", format="integer", type="integer")
     *       )
     *   }
     * )
     *
     * @SWG\Definition(
     *   definition="getActiveContentKeys",
     *   type="array",
     *   allOf={
     *       @SWG\Schema(
     *           @SWG\Property(property="n_items", format="integer", type="integer"),
     *           @SWG\Property(property="content_keys", type="array",
     *              @SWG\Items(type="string")
     *           )
     *       )
     *   }
     * )
     *
     * @SWG\Definition(
     *   definition="contentArrayKeysReply",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           @SWG\Property(property="data", type="array",
     *              @SWG\Items(type="string")
     *           )
     *       )
     *   }
     * )
     *
     *
     */

    /**
     * Requests a list of Contents.
     * Returns the list of Contents.
     *
     * @param $request
     * @param $typeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $typeId = 0)
    {
        try {
            $contents = Content::whereTypeId($typeId)->get();


            foreach ($contents as $content) {
                if (!($content->translation($request->header('LANG-CODE')))) {
                    if (!$content->translation($request->header('LANG-CODE-DEFAULT')))
                        return response()->json(['error' => 'No translation found'], 404);
                }

                $content['news_types'] = ContentTypeType::whereId(isset($content->content_type_type_id) ? $content->content_type_type_id : null)->first();
            }

            return response()->json(["data" => $contents], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Content List'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $contentKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $contentKey)
    {
        try {
            $version = isset($request->version) ? $request->version : null;

            $content = Content::whereContentKey($contentKey)->firstOrFail();
            $content->translations($version);
            $contentTypeType = ContentTypeType::whereId(isset($content->content_type_type_id) ? $content->content_type_type_id : null)->first();

            if(!empty($contentTypeType)){

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
            $content['content_type_type'] = $contentTypeType;

            return response()->json($content, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Content List not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Get(
     *  path="/content/{content_key}",
     *  summary="Show Content Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Content Method"},
     *
     * @SWG\Parameter(
     *      name="content_key",
     *      in="path",
     *      description="Content Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="LANG-CODE-DEFAULT",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response="200",
     *      description="Show the Content data",
     *      @SWG\Schema(ref="#/definitions/contentReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/contentsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Content not Found | No translation found",
     *      @SWG\Schema(ref="#/definitions/contentsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve the content",
     *      @SWG\Schema(ref="#/definitions/contentsMethodErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Request a specific Content.
     * Returns the details of a specific Content.
     *
     * @param Request $request
     * @param $contentKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $contentKey)
    {
        try {
            $version = isset($request->version) ? $request->version : null;

            $content = Content::whereContentKey($contentKey)->firstOrFail();

            if (!($content->translation($request->header('LANG-CODE'), $version))) {
                if (!$content->translation($request->header('LANG-CODE-DEFAULT'), $version)){
                    $translation = $content->contentTranslations()->first();
                    if(!is_null($translation)){
                        $content->translation($translation->language_code);
                    } else {
                        return response()->json(['error' => 'No translation found'], 404);
                    }
                }
            }
            $contentTypeType = ContentTypeType::whereId(isset($content->content_type_type_id) ? $content->content_type_type_id : null)->first();

            if($contentTypeType) {
                if (!($contentTypeType->translation($request->header('LANG-CODE')))) {
                    if (!$contentTypeType->translation($request->header('LANG-CODE-DEFAULT'))) {

                        $translation = $contentTypeType->contentTypeTypeTranslations()->first();
                        if (empty($translation)) {
                            return response()->json(['error' => 'No translation found'], 404);
                        } else {
                            $contentTypeType->translation($translation->language_code);
                        }

                    }

                }
                $content['content_type_type'] = $contentTypeType;
            }

            return response()->json($content, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Content not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the content'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     *
     * @SWG\Post(
     *  path="/content",
     *  summary="Create a Content Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Content Method"},
     *
     *  @SWG\Parameter(
     *      name="content",
     *      in="body",
     *      description="Content Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/contentCreate")
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-AUTH-TOKEN",
     *      in="header",
     *      description="User Auth Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=201,
     *      description="the newly created Content Method",
     *      @SWG\Schema(ref="#/definitions/contentCreateReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/contentsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Content Type not Found",
     *      @SWG\Schema(ref="#/definitions/contentsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Content",
     *      @SWG\Schema(ref="#/definitions/contentsMethodErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a newly created Content in storage.
     * Returns the details of the newly created Content.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required["store_update"], $request);

        try {
            do {
                $rand = str_random(32);
                if (!($exists = Content::whereContentKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $type = ContentType::whereCode($request->json('type'))->firstOrFail();

            $content = Content::create([
                'content_key' => $key,
                'type_id' => $type->id,
                'content_type_type_id' => empty($request->json('content_type_type')) ? null : $request->json('content_type_type'),
                'fixed' => empty($request->json('fixed')) ? 0 : $request->json('fixed'),
                'clean' => empty($request->json('clean')) ? 0 : $request->json('clean'),
                'published' => empty($request->json('published')) ? 0 : $request->json('published'),
                'start_date' => empty($request->json('start_date')) ? NULL : Carbon::createFromFormat('Y-m-d', $request->json('start_date'))->toDateString(),
                'end_date' => empty($request->json('end_date')) ? NULL : Carbon::createFromFormat('Y-m-d', $request->json('end_date'))->toDateString(),
                'publish_date' => empty($request->json('publish_date')) ? NULL : Carbon::createFromFormat('Y-m-d', $request->json('publish_date'))->toDateString()
            ]);

            foreach ($request->json('translations') as $translation) {
                if (isset($translation['language_code']) && isset($translation['title'])) {
                    $content->contentTranslations()->create(
                        [
                            'language_code' => $translation['language_code'],
                            'title' => htmlentities($translation['title'], ENT_QUOTES, "UTF-8"),
                            'summary' => htmlentities(empty($translation['summary']) ? "" : $translation['summary'], ENT_QUOTES, "UTF-8"),
                            'content' => htmlentities(empty($translation['content']) ? "" : $translation['content'], ENT_QUOTES, "UTF-8"),
                            'version' => empty($translation['version']) ? 1 : $translation['version'],
                            'enabled' => empty($translation['enabled']) ? 1 : $translation['enabled'],
                            'link' => empty($translation['link']) ? "" : $translation['link'],
                            'docs_main' => empty($translation['docs_main']) ? 0 : $translation['docs_main'],
                            'docs_side' => empty($translation['docs_side']) ? 0 : $translation['docs_side'],
                            'highlight' => empty($translation['highlight']) ? 0 : $translation['highlight'],
                            'slideshow' => empty($translation['slideshow']) ? 0 : $translation['slideshow'],
                            'updated_by' => $userKey,
                            'created_by' => $userKey
                        ]
                    );
                }
            }
            return response()->json($content, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Content Type not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store a new Content'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/content/{content_key}",
     *  summary="Update a Content Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Content Method"},
     *
     *  @SWG\Parameter(
     *      name="content",
     *      in="body",
     *      description="Content Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/contentUpdate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="content_key",
     *      in="path",
     *      description="Content Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-AUTH-TOKEN",
     *      in="header",
     *      description="User Auth Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=200,
     *      description="The updated Content Method",
     *      @SWG\Schema(ref="#/definitions/contentUpdateReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/contentsMethodErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Content not Found",
     *      @SWG\Schema(ref="#/definitions/contentsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update a Content",
     *      @SWG\Schema(ref="#/definitions/contentsMethodErrorDefault")
     *  )
     * )
     *
     */



    /**
     * Update the Content in storage.
     * Returns the details of the updated Content.
     *
     * @param Request $request
     * @param $contentKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $contentKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required["store_update"], $request);

        try {
            $content = Content::with('contentTypeType')->whereContentKey($contentKey)->firstOrFail();
            if (!empty($request->json('fixed'))) {
                $content->fixed = $request->json('fixed');
            }
            if (!empty($request->json('clean'))) {
                $content->clean = $request->json('clean');
            }
            if (!empty($request->json('published'))) {
                $content->published = $request->json('published');
            }

            if(!empty($request->json('content_type_type'))) {
                $content->content_type_type_id = $request->json('content_type_type');
            }
            $content->start_date = empty($request->json('start_date')) ? $content->start_date : Carbon::createFromFormat('Y-m-d', $request->json('start_date'))->toDateString();
            $content->end_date = empty($request->json('end_date')) ? $content->end_date : Carbon::createFromFormat('Y-m-d', $request->json('end_date'))->toDateString();
            $content->publish_date = empty($request->json('publish_date')) ? $content->publish_date : Carbon::createFromFormat('Y-m-d', $request->json('publish_date'))->toDateString();
            $content->save();

            //get the last version of a translation
            $lastVersion = $content->contentTranslations()->max('version');
            $version = is_null($lastVersion) ? 0 : $lastVersion + 1;

            foreach ($request->json('translations') as $translation) {
                if (isset($translation['language_code']) && isset($translation['title'])) {

                    $content->contentTranslations()->create([
                        'language_code' => $translation['language_code'],
                        'title' => htmlentities($translation['title'], ENT_QUOTES, "UTF-8"),
                        'summary' => htmlentities(empty($translation['summary']) ? "" : $translation['summary'], ENT_QUOTES, "UTF-8"),
                        'content' => htmlentities(empty($translation['content']) ? "" : $translation['content'], ENT_QUOTES, "UTF-8"),
                        'version' => $version,
                        'enabled' => empty($translation['enabled']) ? 0 : $translation['enabled'],
                        'link' => empty($translation['link']) ? "" : $translation['link'],
                        'updated_by' => $userKey,
                        'created_by' => $userKey,
                        'docs_main' => empty($translation['docs_main']) ? 0 : $translation['docs_main'],
                        'docs_side' => empty($translation['docs_side']) ? 0 : $translation['docs_side'],
                        'highlight' => empty($translation['highlight']) ? 0 : $translation['highlight'],
                        'slideshow' => empty($translation['slideshow']) ? 0 : $translation['slideshow'],
                    ]);

                }
            }
            return response()->json(["content" => $content, "version" => $version ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Content not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update a Content'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Publishes a Content.
     * Returns the details of the published Content.
     *
     * @param Request $request
     * @param $contentKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function publish(Request $request, $contentKey)
    {
        ONE::verifyToken($request);

        try {
            $content = Content::with('contentTypeType')->whereContentKey($contentKey)->firstOrFail();
            $content->published = 1;
            $content->publish_date = Carbon::now();
            $content->save();
            return response()->json($content, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Content not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to publish a Content'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Unpublishes a Content.
     * Returns the details of the unpublished Content.
     *
     * @param Request $request
     * @param $contentKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function unpublish(Request $request, $contentKey)
    {
        ONE::verifyToken($request);

        try {
            $content = Content::with('contentTypeType')->whereContentKey($contentKey)->firstOrFail();
            $content->published = 0;
            $content->save();
            return response()->json($content, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Content not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to unpublish a Content'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     *
     *
     * @SWG\Delete(
     *  path="/content/{content_key}",
     *  summary="Delete Content Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Content Method"},
     *
     * @SWG\Parameter(
     *      name="content_key",
     *      in="path",
     *      description="Content Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-AUTH-TOKEN",
     *      in="header",
     *      description="User Auth Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=200,
     *      description="OK",
     *      @SWG\Schema(ref="#/definitions/contentDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/contentsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Content not Found",
     *      @SWG\Schema(ref="#/definitions/contentsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Content",
     *      @SWG\Schema(ref="#/definitions/contentsMethodErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Remove the specified Content from storage.
     *
     * @param Request $request
     * @param $contentKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $contentKey)
    {
        ONE::verifyToken($request);

        try {
            $content = Content::whereContentKey($contentKey)->firstOrFail();
            $content->delete();
            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Content not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete a Content'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listContent(Request $request)
    {

        try {
            $contents = Content::with('contentFiles', 'contentTypeType')->whereIn('content_key', $request->json('page_keys'))->get();

            foreach ($contents as $key => $content) {
                if (!($content->translation($request->header('LANG-CODE')))) {
                    if (!$content->translation($request->header('LANG-CODE-DEFAULT'))){
                        $translation = $content->contentTranslations()->first();
                        if(!is_null($translation)){
                            $content->translation($translation->language_code);
                        }
                    }
                }

                /*if (!is_null($content->contentTypeType)){
                    if (!($content->contentTypeType->translation($request->header('LANG-CODE')))) {
                        $content->contentTypeType->translation($request->header('LANG-CODE-DEFAULT'));
                    }
                }*/
            }


            return response()->json(["data" => $contents], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Content not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Add a File to Content in storage.
     * Returns the details of the newly created File.
     *
     * @param Request $request
     * @param $contentKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function addFile(Request $request, $contentKey)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required["add_update_file"], $request);

        try {
            $content = Content::whereContentKey($contentKey)->firstOrFail();

            $files = $content->contentFiles()
                ->where('content_files.type_id', '=', $request->json('type_id'))
                ->orderBy('content_files.position', 'desc');

            $position = !empty($files->first()) ? $files->first()->position + 1 : 0;

            $file = $content->contentFiles()->create(
                [
                    'file_id' => $request->json('file_id'),
                    'name' => $request->json('name'),
                    'description' => $request->json('description'),
                    'position' => $position,
                    'type_id' => $request->json('type_id')
                ]
            );

            return response()->json($file, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Content not Found'], 404);
        } catch (QueryException $e) {
            return response()->json(['error' => $e], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests a list of Files from the Content in storage.
     * Returns the list of Files.
     *
     * @param Request $request
     * @param $contentKey
     * @param null $typeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFiles(Request $request, $contentKey, $typeId = NULL)
    {
        try {
            if (!is_null($typeId)) {
                $content = Content::whereContentKey($contentKey)->first();
                $files = $content->contentFiles()->whereTypeId($typeId)
                    ->orderBy('position', 'desc')
                    ->get();
            } else {
                $content = Content::whereContentKey($contentKey)->first();
                $files = $content->contentFiles()->orderBy('position', 'desc')->get();
            }

            return response()->json(["data" => $files], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the files list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests a list of Files from the Content in storage.
     * Returns the list of Files.
     *
     * @param Request $request
     * @param $contentKey
     * @param null $typeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFirstFiles(Request $request, $contentKey, $typeId = NULL)
    {
        try {
            $content = Content::whereContentKey($contentKey)->firstOrFail();
            $id = $content->id;

            if (!is_null($typeId)) {
                $files = Content::FindOrFail($id)->contentFiles()
                    ->where('type_id', '=', $typeId)
                    ->orderBy('position', 'desc')
                    ->take(5)
                    ->get();
            } else {
                $files = Content::FindOrFail($id)->contentFiles()
                    ->orderBy('position', 'desc')
                    ->take(5)
                    ->get();
            }

            return response()->json(["data" => $files], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the files list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests the Files details.
     * Returns the File details.
     *
     * @param Request $request
     * @param $contentKey
     * @param $fileId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFileDetails(Request $request, $contentKey, $fileId)
    {
        try {
            $content = Content::whereContentKey($contentKey)->firstOrFail();
            $id = $content->id;

            $file = Content::FindOrFail($id)->contentFiles()
                ->whereFileId($fileId)
                ->first();

            return response()->json($file, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'File not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests the Files details.
     * Returns the File details.
     *
     * @param Request $request
     * @param $contentKey
     * @param type $fileId
     * @return \Illuminate\Http\JsonResponse
     * @internal param type $contentId
     */
    public function updateFileDetails(Request $request, $contentKey, $fileId)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required["add_update_file"], $request);

        try {
            $content = Content::whereContentKey($contentKey)->firstOrFail();
            $file = $content->contentFiles()
                ->whereFileId($fileId)
                ->first();

            $file->name = $request->json('name');
            $file->description = $request->json('description');
            $file->file_id = $request->json('file_id');
            $file->type_id = $request->json('type_id');
            $file->position = $request->json('position');
            $file->save();

            return response()->json($file, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'File not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update a file details'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Remove the specified File from Content.
     *
     * @param Request $request
     * @param $contentKey
     * @param type $fileId
     * @return \Illuminate\Http\JsonResponse
     * @internal param type $contentId
     */
    public function deleteFile(Request $request, $contentKey, $fileId)
    {
        ONE::verifyToken($request);

        try {
            $content = Content::whereContentKey($contentKey)->firstOrFail();
            $content->contentFiles()
                ->whereFileId($fileId)
                ->delete();

            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'File not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete a Content'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Sets a new order for a file list.
     *
     * @param Request $request
     * @param $contentKey
     * @param type $fileId
     * @return \Illuminate\Http\JsonResponse
     * @internal param type $contentId
     */
    public function orderFile(Request $request, $contentKey, $fileId)
    {
        ONE::verifyKeysRequest($this->required["order_file"], $request);

        try {
            $content = Content::whereContentKey($contentKey)->firstOrFail();

            $fileArray = $content->contentFiles()
                ->where('content_files.type_id', '=', $request->json('type_id'))
                ->orderBy('content_files.position', 'desc')
                ->pluck('id', 'content_files.position');

            $file = $content->contentFiles()->whereFileId($fileId)->first();

            if ($request->json('movement') > 0 && $file->position <= (sizeof($fileArray) - $request->json('movement'))) {

                for ($i = $file->position + 1; $i <= $file->position + $request->json('movement'); $i++) {

                    $tmp = Content::whereContentKey($contentKey)->contentFiles()
                        ->findOrFail($fileArray[$i]);
                    $tmp->position -= 1;
                    $tmp->save();
                }
                $file->position += $request->json('movement');
                $file->save();

                return response()->json('OK', 200);

            } elseif ($request->json('movement') < 0 && $file->position >= -$request->json('movement')) { // movement is a negative value

                for ($i = $file->position - 1; $i >= $file->position + $request->json('movement'); $i--) {

                    $tmp = Content::whereContentKey($contentKey)->contentFiles()
                        ->findOrFail($fileArray[$i]);
                    $tmp->position += 1;
                    $tmp->save();
                }
                $file->position += $request->json('movement');
                $file->save();

                return response()->json('OK', 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e], 404);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 400);
        }

        return response()->json('Bad request', 400);
    }

    /**
     * Requests to show a page version with translations.
     * Returns page version with translations.
     *
     * @param Request $request
     * @param $contentKey
     * @param $version
     * @return \Illuminate\Http\JsonResponse
     * @internal param $contentId
     */
    public function showVersion(Request $request, $contentKey, $version)
    {
        try {
            $content = Content::whereContentKey($contentKey)->firstOrFail();

            $versions = ContentTranslation::whereContentId($content->id)
                ->whereVersion($version)
                ->get();
            return response()->json(["data" => $versions], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => "Content not found"], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the page version with translations'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests a list of page versions.
     * Returns the list of page versions.
     *
     * @param Request $request
     * @param $contentKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $contentId
     */
    public function showVersions(Request $request, $contentKey)
    {
        try {
            $content = Content::whereContentKey($contentKey)->firstOrFail();

            $versions = ContentTranslation::whereContentId($content->id)
                ->orderBy('version', 'desc')
                ->distinct()
                ->get(['version', 'created_at', 'enabled']);
            return response()->json($versions, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => "Content not found"], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the versions'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests a list of pages.
     * Returns the list of pages.
     *
     * @param Request $request
     * @param $contentId
     * @return \Illuminate\Http\JsonResponse
     */
    /*    public function content(Request $request, $contentId)
        {
            try {
                $pageContents = ContentTranslation::whereContentId($contentId)
                    ->whereEnabled(1)
                    ->get();
                return response()->json($pageContents, 200);
            } catch (Exception $e) {
                return response()->json(['error' => 'Failed to retrieve the page contents list'], 500);
            }
    
            return response()->json(['error' => 'Unauthorized'], 401);
        }*/

    /**
     * Request to enable a page content version in storage.
     * Returns the details of the enabled page content.
     *
     * @param Request $request
     * @param $contentKey
     * @param $version
     * @return \Illuminate\Http\JsonResponse
     * @internal param $contentId
     */
    public function enable(Request $request, $contentKey, $version)
    {
        ONE::verifyToken($request);
        try {
            $content = Content::whereContentKey($contentKey)->firstOrFail();

            // Disable active page
            $otherPages = ContentTranslation::whereContentId($content->id)
                ->whereEnabled(1);
            $otherPages->update(array("enabled" => 0));
            // Enable requested page
            $pageContent = ContentTranslation::whereContentId($content->id)
                ->whereVersion($version);
            $pageContent->update(array("enabled" => 1));
            $response = $pageContent->get();
            return response()->json($response, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Page Content not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to enable a Page Content'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests a list of News Contents.
     * Returns the list of News Contents.
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNewsList(Request $request)
    {
        try {
            $contents = Content::with('contentTypeType')->whereTypeId(2)
                ->wherePublished(1)
                ->orderBy('start_date', 'desc')
                ->get();

            foreach ($contents as $content) {
                if (!($content->translation($request->header('LANG-CODE')))) {
                    if (!$content->translation($request->header('LANG-CODE-DEFAULT')))
                        return response()->json(['error' => 'No translation found'], 404);
                }
            }

            return response()->json(["data" => $contents], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the content list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests an array with News Contents Ids.
     * Returns the list of array with News Contents Ids.
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNewsIds(Request $request)
    {
        try {
            $content = Content::with('contentTypeType')->whereTypeId(2)
                ->wherePublished(1)
                ->orderBy('start_date', 'desc')
                ->pluck('id')
                ->toarray();

            return response()->json(["data" => $content], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the content list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests a list of Present News Contents.
     * Returns the list of Present News Contents.
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPresentNews(Request $request)
    {
        try {
            $contents = Content::with('contentFiles','contentTypeType')->whereTypeId(2)
                ->wherePublished(1)
                ->where('publish_date', '<=', Carbon::now())
                ->orderBy('start_date','desc')
                ->take(5)
                ->get();

            foreach ($contents as $content) {
                if (!($content->translation($request->header('LANG-CODE')))) {
                    if (!$content->translation($request->header('LANG-CODE-DEFAULT')))
                        return response()->json(['error' => 'No translation found'], 404);
                }
            }

            return response()->json(["data" => $contents], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the content list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContentsByKey(Request $request)
    {
        try {


            if(!empty($request->json('content_keys'))){
                $contents = [];
                $contentKeys = $request->json('content_keys');
                $contents = Content::whereIn('content_key',$contentKeys)->with('contentFiles','contentTypeType')
                            ->wherePublished(1)
                            ->where('publish_date', '<=', Carbon::now())
                            ->orderBy('start_date','desc')->get();

                foreach ($contents as $content){
                    $content->newTranslation($request->header('LANG-CODE'),$request->header('LANG-CODE-DEFAULT') );
                    if (!is_null($content->contentTypeType)){
                        $content->contentTypeType->newTranslation($request->header('LANG-CODE'),$request->header('LANG-CODE-DEFAULT'));
                    }
                }
//                foreach ($request->json('content_keys') as $contentKey){
//                    $content = Content::with('contentFiles','contentTypeType')
//                        ->wherePublished(1)
//                        ->where('publish_date', '<=', Carbon::now())
//                        ->orderBy('start_date','desc')
//                        ->whereContentKey($contentKey)
//                        ->first();
//
//                    if (!is_null($content->contentTypeType)){
//                        if (!($content->contentTypeType->translation($request->header('LANG-CODE')))) {
//                            $content->contentTypeType->translation($request->header('LANG-CODE-DEFAULT'));
//                        }
//                    }
//                    if ($content)
//                        $contents[] = $content;
//                }
//
//                foreach ($contents as $key => $content) {
//                    if (!($content->translation($request->header('LANG-CODE')))) {
//                        if (!$content->translation($request->header('LANG-CODE-DEFAULT'))){
//                            unset($contents[$key]);
//                        }
//                    }
//                }
//
//                $response = [];
//                foreach ($contents as $content){
//                    $response[] = $content;
//                }

                return response()->json(["data" => $contents], 200);
            }
            return response()->json(["data" => $request], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the content list'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests a list of Last 5 News Contents.
     * Returns the list of Last 5 Contents.
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLastNews(Request $request)
    {
        try {
            $contents = Content::whereTypeId(2)
                ->wherePublished(1)
                ->orderBy('start_date', 'desc')
                ->take(5)
                ->get();

            foreach ($contents as $content) {
                if (!($content->translation($request->header('LANG-CODE')))) {
                    if (!$content->translation($request->header('LANG-CODE-DEFAULT')))
                        return response()->json(['error' => 'No translation found'], 404);
                }
            }

            return response()->json(["data" => $contents], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the content list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests a list of Last 5 Events Contents.
     * Returns the list of Last 5 Contents.
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEventsList(Request $request)
    {
        try {
            $contents = Content::whereTypeId(3)
                ->wherePublished(1)
                ->where('end_date', '>=', Carbon::today())
                ->orderBy('start_date')
                ->get();

            foreach ($contents as $content) {
                if (!($content->translation($request->header('LANG-CODE')))) {
                    if (!$content->translation($request->header('LANG-CODE-DEFAULT')))
                        return response()->json(['error' => 'No translation found'], 404);
                }
            }

            return response()->json(["data" => $contents], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the content list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests an array with Events Contents Ids.
     * Returns the list of array with Events Contents Ids.
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEventsIds(Request $request)
    {
        try {
            $content = Content::whereTypeId(3)
                ->wherePublished(1)
                ->where('end_date', '>=', Carbon::today())
                ->orderBy('start_date')
                ->pluck('id')
                ->toArray();

            return response()->json(["data" => $content], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the content list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Requests a list of Last 5 Events Contents.
     * Returns the list of Last 5 Contents.
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLastEvents(Request $request)
    {
        try {
            $contents = Content::whereTypeId(3)
                ->wherePublished(1)
                ->orderBy('start_date', 'desc')
                ->take(5)
                ->get();

            foreach ($contents as $content) {
                if (!($content->translation($request->header('LANG-CODE')))) {
                    $content->translation($request->header('LANG-CODE-DEFAULT'));
//                        return response()->json(['error' => 'No translation found'], 404);
                }
            }

            return response()->json(["data" => $contents], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the content list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function getContentsByKeyWithType(Request $request)
    {
        try {

            $code = !empty($request->json('content_type_type')) ? $request->json('content_type_type') : null;

            $contentTypeType = ContentTypeType::whereCode($code)->first();
            //when code doesn't exist
            if(is_null($contentTypeType)){
                return response()->json(["data" => []], 200);
            }


            if(!empty($request->json('content_keys'))){
                $contents = [];
                foreach ($request->json('content_keys') as $contentKey){
                    $content = Content::with('contentFiles', 'contentTypeType')
                        ->whereContentTypeTypeId($contentTypeType->id)
                        ->wherePublished(1)
                        ->where('publish_date', '<=', Carbon::now())
                        ->orderBy('start_date','desc')
                        ->whereContentKey($contentKey)
                        ->first();


                    if (isset($content->contentTypeType)){
                        if (!($content->contentTypeType->translation($request->header('LANG-CODE')))) {
                            $content->contentTypeType->translation($request->header('LANG-CODE-DEFAULT'));
                        }
                    }

                    if ($content)
                        $contents[] = $content;
                }

                foreach ($contents as $key => $content) {
                    if (!($content->translation($request->header('LANG-CODE')))) {
                        if (!$content->translation($request->header('LANG-CODE-DEFAULT'))){
                            unset($contents[$key]);
                        }
                    }
                }

                $response = [];
                foreach ($contents as $content){
                    $response[] = $content;
                }

                return response()->json(["data" => $response], 200);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the content list'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     *
     * @SWG\Post(
     *  path="/content/activeContentsByKey",
     *  summary="Retrieves an Array of Content Keys to Show",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Content Method"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Array of Content Keys",
     *      required=false,
     *      @SWG\Schema(ref="#/definitions/getActiveContentKeys")
     *  ),
     *
     *  @SWG\Response(
     *      response="200",
     *      description="Show the Content Data Keys Array",
     *      @SWG\Schema(ref="#/definitions/contentArrayKeysReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve the content",
     *      @SWG\Schema(ref="#/definitions/contentsMethodErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveContentKeys(Request $request)
    {
        try {
            $contentKeysArray = [];
            if(!empty($request->json('content_keys'))){

                $type = (!empty($request->json('type'))) ? $request->json('type') : 'news';

                $nItems = (!empty($request->json('n_items'))) ? $request->json('n_items') : count($request->json('content_keys'));
                
                $query = Content::whereIn('content_key', $request->json('content_keys'))
                    ->wherePublished(1)
                    ->where('publish_date', '<=', Carbon::now());

                if ($type == "events") {
                    $query->orderBy('start_date', 'desc')
                       ->orderBy('publish_date', 'desc');
                }
                if ($type == "news") {
                    $query->orderBy('publish_date', 'desc')
                        ->orderBy('start_date', 'desc')
                        ->orderBy('end_date', 'desc');
                }
                $content = $query->take($nItems)
                    ->get();
                if(!empty($content))
                    $contentKeysArray = collect($content)->pluck('content_key')->toArray();


                return response()->json(["data" => $contentKeysArray], 200);
            }
            return response()->json(["data" => $contentKeysArray], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the content list'], 500);
        }
    }

}