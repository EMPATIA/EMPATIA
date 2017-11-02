<?php

namespace App\Http\Controllers;

use App\Text;
use App\TextTranslation;
use App\One\One;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Exception;

/**
 * Class TextsController
 * @package App\Http\Controllers
 */
class TextsController extends Controller
{
    
    protected $required = [
        'store'  => ['entity_id'],  
        'update' => ['entity_id'],
        'translation' => ['title','contents','tag']         
    ];

    /**
     * @SWG\Tag(
     *   name="Texts Method",
     *   description="Everything about Texts Method",
     * )
     *
     *  @SWG\Definition(
     *      definition="textsMethodErrorDefault",
     *      required={"error"},
     *      @SWG\Property( property="error", type="string", format="string")
     *  )
     *
     *  @SWG\Definition(
     *   definition="textsReply",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           @SWG\Property(property="id", format="integer", type="integer"),
     *           @SWG\Property(property="key", format="string", type="string"),
     *           @SWG\Property(property="created_at", format="date", type="string"),
     *           @SWG\Property(property="updated_at", format="date", type="string")
     *       )
     *   }
     * )
     *
     *
     *  @SWG\Definition(
     *   definition="textCreate",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           required={"entity_id"},
     *      @SWG\Property(property="entity_id", format="integer", type="integer"),
     *      @SWG\Property(property="translations", type="array", @SWG\Items(ref="#/definitions/textTranslations"))
     *       )
     *   }
     * )
     *
     *
     *  @SWG\Definition(
     *   definition="textUpdate",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           required={"entity_id"},
     *      @SWG\Property(property="entity_id", format="integer", type="integer"),
     *      @SWG\Property(property="translations", type="array", @SWG\Items(ref="#/definitions/textTranslations"))
     *       )
     *   }
     * )
     *
     *   @SWG\Definition(
     *   definition="textDeleteReply",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     *   @SWG\Definition(
     *   definition="textTranslations",
     *   type="object",
     *   allOf={
     *       @SWG\Schema(
     *           required={"language_code", "title"},
     *           @SWG\Property(property="language_code", format="string", type="string"),
     *           @SWG\Property(property="title", format="string", type="string"),
     *           @SWG\Property(property="content", format="string", type="string"),
     *           @SWG\Property(property="tag", format="string", type="string")
     *       )
     *   }
     * )
     */
    
    /**
     * Requests a list of texts.
     * Returns the list of texts.
     * 
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $texts = Text::all()->translations;
            return response()->json(['data' => $texts], 200);
        }
        catch(Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the texts list'], 500);
        }    
        
        return response()->json(['error' => 'Unauthorized'], 401);        
    }


    /**
     *
     * @SWG\Get(
     *  path="/text/{text_id}",
     *  summary="Show a text Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Text Method"},
     *
     * @SWG\Parameter(
     *      name="text_id",
     *      in="path",
     *      description="Text Method Id",
     *      required=true,
     *      type="integer"
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
     *      response="200",
     *      description="Show the Text data",
     *      @SWG\Schema(ref="#/definitions/textsReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/textsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Text not Found",
     *      @SWG\Schema(ref="#/definitions/textsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve the Text",
     *      @SWG\Schema(ref="#/definitions/textsMethodErrorDefault")
     *  )
     * )
     */


    /**
     * Request a specific text.
     * Returns the details of a specific text.
     * 
     * @param $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        try {
            $text = Text::findOrFail($id)->translations;            
            return response()->json($text, 200);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Text not Found'], 404);
        }
        catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Text'], 500);
        }           
        
        return response()->json(['error' => 'Unauthorized'], 401);                
    }


    /**
     *
     * @SWG\Post(
     *  path="/text",
     *  summary="Create a text Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Text Method"},
     *
     *  @SWG\Parameter(
     *      name="text",
     *      in="body",
     *      description="text Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/textCreate")
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
     *      description="the newly created text Method",
     *      @SWG\Schema(ref="#/definitions/textsReply")
     *  ),

     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/textsMethodErrorDefault")
     *   ),
     *
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new text",
     *      @SWG\Schema(ref="#/definitions/textsMethodErrorDefault")
     *  )
     * )
     *
     */


    /** 
     * Store a newly created text in storage. 
     * Returns the details of the newly created text.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        
        ONE::verifyKeysRequest($this->required['store'], $request);
        
        try {
            $text = Text::create(['entity_id' => $request->json('entity_id')]);              

            foreach ($request->json('translations') as $translation){
                TextTranslation::create(['text_id' => $text->id,
                                         'title' => $translation["title"], 
                                         'content' => $translation["content"],
                                         'tag' => $translation["tag"],
                                         'created_by' => $userKey,
                                         'language_code' => $translation["language_code"]]);
            }

            return response()->json(Text::findOrFail($text->id)->translations, 201); 
        }
        catch(Exception $e){
            return response()->json(['error' => $e], 500);
        }                
        
        return response()->json(['error' => 'Unauthorized'], 401);                
    }


    /**
     *
     * @SWG\Put(
     *  path="/text/{text_id}",
     *  summary="Update a text Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Text Method"},
     *
     *  @SWG\Parameter(
     *      name="text",
     *      in="body",
     *      description="Text Method data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/textUpdate")
     *  ),
     *
     * @SWG\Parameter(
     *      name="text_id",
     *      in="path",
     *      description="Text Id",
     *      required=true,
     *      type="integer"
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
     *      description="The updated text",
     *      @SWG\Schema(ref="#/definitions/textsReply")
     *  ),
     *
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/textsMethodErrorDefault")
     *   ),
     *
     *     @SWG\Response(
     *      response="404",
     *      description="Text not Found",
     *      @SWG\Schema(ref="#/definitions/textsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update text",
     *      @SWG\Schema(ref="#/definitions/textsMethodErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Update the text in storage.
     * Returns the details of the updated text.
     * 
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $userKey = ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->required['update'], $request);
        
        try {        
            $text = Text::findOrFail($id);                        
        
            foreach ($request->json('translations') as $translation){
                // create or update
            }
            
            return response()->json($text, 200);         
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Text not Found'], 404);
        }          
        catch(Exception $e){
            return response()->json(['error' => 'Failed to update a Text'], 500);
        }                  

        return response()->json(['error' => 'Unauthorized'], 401);                
    }

    /**
     *
     *
     * @SWG\Delete(
     *  path="/text/{text_id}",
     *  summary="Delete text Method",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Text Method"},
     *
     * @SWG\Parameter(
     *      name="text_id",
     *      in="path",
     *      description="Text Id",
     *      required=true,
     *      type="integer"
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
     *      @SWG\Schema(ref="#/definitions/textDeleteReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/textsMethodErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="text not Found",
     *      @SWG\Schema(ref="#/definitions/textsMethodErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete text",
     *      @SWG\Schema(ref="#/definitions/textsMethodErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Remove the specified text from storage.
     * 
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $userKey = ONE::verifyToken($request);

        try {          
            $text = Text::findOrFail($id);                              
            $text->delete();
            return response()->json('OK', 200);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Text not Found'], 404);
        }          
        catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete a Text'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);                
    }
}
