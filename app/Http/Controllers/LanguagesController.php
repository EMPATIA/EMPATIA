<?php

namespace App\Http\Controllers;

use App\Entity;
use App\Language;
use App\One\One;
use App\OrchUser;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

/**
 * Class LanguagesController
 * @package App\Http\Controllers
 */


/**
 * @SWG\Tag(
 *   name="Language",
 *   description="Everything about Language",
 * )
 *
 *  @SWG\Definition(
 *      definition="languageErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="Language",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"name", "code"},
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="code", format="integer", type="integer")
 * )
 *   }
 * )
 *
 */

class LanguagesController extends Controller
{

    protected $keysRequired = [
        'name',
        'code'
    ];

    /**
     *
     */
    public function __construct(Request $request)
    {
        $validation = Cache::get($request->header('X-MODULE-TOKEN'));
        if(empty($validation)){
            $response = ONE::checkTokenOrchestrator($request->header('X-MODULE-TOKEN'));

            Cache::put($request->header('X-MODULE-TOKEN'),$response, 60);
            if(!$response){
                return response()->json(['error' => 'Failed to verify Authorization'], 500)->send();
            }
        }elseif($validation == false){
            return response()->json(['error' => 'Unauthorized'], 401)->send();
        }
    }

    /**
     * Request list of all Languages
     * Returns the list of all Languages
     * @param Request $request
     * @return list of all
     */
    public function index(Request $request)
    {
        try{
            if (!empty($request->header('X-ENTITY-KEY'))){
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
                $languages = $entity->languages()->orderBy('default', 'desc')->orderBy('id','asc')->get();

                foreach ($languages as $language){
                    if($language->pivot->default == "1"){
                        $language['default'] = true;
                    } else {
                        $language['default'] = false;
                    }
                    unset($language["pivot"]);
                }
            }
            else{
                $languages = Language::all();
            }

            return response()->json(['data' => $languages], 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Languages list'], 500);
        }

    }

    /**
     * Request list of all Languages
     * Returns the list of all Languages
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listAll(Request $request)
    {
        try{
            $languages = Language::all();
            return response()->json(['data' => $languages], 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Languages list'], 500);
        }
    }

    /**
     *
     *
     * @SWG\Definition(
     *    definition="replyLanguage",
     *    required={"data"},
     *    @SWG\Property(
     *      property="data",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Language")
     *      })
     *  )
     *
     * @SWG\Get(
     *  path="/language/{language_id}",
     *  summary="Show an Language",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Language"},
     *
     * @SWG\Parameter(
     *      name="language_id",
     *      in="path",
     *      description="Language Key",
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
     *
     *  @SWG\Response(
     *      response="200",
     *      description="Show the Language data",
     *      @SWG\Schema(ref="#/definitions/replyLanguage")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Language not Found",
     *      @SWG\Schema(ref="#/definitions/languageErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Language",
     *      @SWG\Schema(ref="#/definitions/languageErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Request of one Language
     * Returns the attributes of the Language
     * @param Request $request
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     * @internal param $
     */
    public function show(Request $request, $id)
    {
        $userKey = ONE::verifyToken($request);
        if (OrchUser::verifyRole($userKey, "admin")){
            try{
                $language = Language::findOrFail($id);
                return response()->json($language, 200);
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Language not Found'], 404);
            }catch(Exception $e){
                return response()->json(['error' => 'Failed to retrieve the Language'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Definition(
     *    definition="createLanguage",

     *    @SWG\Property(
     *      property="language",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Language")
     *      })
     *  )
     *
     * @SWG\Definition(
     *    definition="replyCreateLanguage",
     *    @SWG\Property(
     *      property="language",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Language")
     *      })
     *  )
     *
     * @SWG\Post(
     *  path="/language",
     *  summary="Creation of a Language",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Language"},
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Language data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/createLanguage")
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
     *      description="the newly created language",
     *      @SWG\Schema(ref="#/definitions/replyCreateLanguage")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/languageErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Language",
     *      @SWG\Schema(ref="#/definitions/languageErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a new Language in the database
     * Return the Attributes of the Language created
     * @param Request $request
     * @return static
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->keysRequired, $request);
        if (OrchUser::verifyRole($userKey, "admin")){
            try{
                $language = Language::create(
                    [
                        'name' => $request->json('name'),
                        'code' => $request->json('code')
                    ]
                );
                return response()->json($language, 201);
            } catch(Exception $e){
                return response()->json(['error' => 'Failed to store new Language'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Definition(
     *    definition="updateLanguage",
     *    @SWG\Property(
     *      property="language",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Language")
     *      })
     *  )
     *
     * @SWG\Definition(
     *    definition="replyUpdatedLanguage",
     *    @SWG\Property(
     *      property="language",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Language")
     *      })
     *  )
     *
     * @SWG\Put(
     *  path="/language/{language_id}",
     *  summary="Update a Language",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Language"},
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Language Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/updateLanguage")
     *  ),
     *
     * @SWG\Parameter(
     *      name="language_id",
     *      in="path",
     *      description="Language Id",
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
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *
     *  @SWG\Response(
     *      response=200,
     *      description="The updated Language",
     *      @SWG\Schema(ref="#/definitions/replyUpdatedLanguage")
     *  ),
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/languageErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Language not Found",
     *      @SWG\Schema(ref="#/definitions/languageErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Language",
     *      @SWG\Schema(ref="#/definitions/languageErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Update a existing Language
     * Return the Attributes of the Language Updated
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);
        if (OrchUser::verifyRole($userKey, "admin")){
            try{
                $language = Language::findOrFail($id);

                $language->name = $request->json('name');
                $language->code = $request->json('code');

                $language->save();

                return response()->json($language, 200);
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Language not Found'], 404);
            }catch (Exception $e) {
                return response()->json(['error' => 'Failed to update Language'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     *  @SWG\Definition(
     *     definition="replyDeleteLanguage",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/language/{language_id}",
     *  summary="Delete a Language",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Language"},
     *
     * @SWG\Parameter(
     *      name="language_id",
     *      in="path",
     *      description="Language Id",
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
     *      description="Authentication Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=200,
     *      description="OK",
     *      @SWG\Schema(ref="#/definitions/replyDeleteLanguage")
     *  ),
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Language",
     *      @SWG\Schema(ref="#/definitions/languageErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/languageErrorDefault")
     *   ),
     *  @SWG\Response(
     *      response="404",
     *      description="Language not Found'",
     *      @SWG\Schema(ref="#/definitions/languageErrorDefault")
     *  ),
     * )
     *
     */

    /**
     * Delete existing Language
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $userKey = ONE::verifyToken($request);
        if (OrchUser::verifyRole($userKey, "admin")){
            try{
                $language = Language::destroy($id);

                return response()->json('Ok', 200);
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Language not Found'], 404);
            }catch (Exception $e) {
                return response()->json(['error' => 'Failed to delete Language'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Get the default and all the other languages, specifically for the Administrator
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLanguages(Request $request)
    {
        try{
            if (!empty($request->json('entity_key'))){
                $entity = Entity::whereEntityKey($request->json('entity_key'))->firstOrFail();
                $languages = $entity->languages()->orderBy('default', 'desc')->orderBy('id','asc')->get();

                foreach ($languages as $language){
                    if($language->pivot->default == "1"){
                        $language['default'] = true;
                    } else {
                        $language['default'] = false;
                    }
                    unset($language["pivot"]);
                }
            }
            else{
                $languages = Language::all();
            }

            return response()->json(['data' => $languages], 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Languages list'], 500);
        }

    }
}
