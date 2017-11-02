<?php

namespace App\Http\Controllers;

use App\Category;
use App\Entity;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class CategoriesController
 * @package App\Http\Controllers
 */

/**
 * @SWG\Tag(
 *   name="Category",
 *   description="Everything about Categories",
 * )
 *
 *  @SWG\Definition(
 *      definition="categoryErrorDefault",
 *      required={"error"},
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="category",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"entity_id", "name", "description", "created_by"},
 *           @SWG\Property(property="entity_id", format="integer", type="integer"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="description", format="string", type="string"),
 *           @SWG\Property(property="created_by", format="string", type="string")
 *       )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="categoryReply",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"entity_id", "name", "description", "created_by"},
 *           @SWG\Property(property="category_key", format="string", type="string"),
 *           @SWG\Property(property="entity_id", format="integer", type="integer"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="description", format="string", type="string"),
 *           @SWG\Property(property="created_by", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 */

class CategoriesController extends Controller
{
    protected $keysRequired = [
        'name',
        'description'
    ];

    /**
     * Request the list of Categories
     * Returns the list of all Categories
     * @param Request $request
     * @return list of all
     * @internal param $
     */
    public function index(Request $request)
    {
        try{
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            $categories = $entity->categories()->get();

            return response()->json(['data' => $categories], 200);

        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Categories'], 500);
        }
    }

    /**
     *
     * @SWG\Get(
     *  path="/category/{category_key}",
     *  summary="Show a Category",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Category"},
     *
     * @SWG\Parameter(
     *      name="category_key",
     *      in="path",
     *      description="Category Key",
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
     *      response="200",
     *      description="Show the Category data",
     *      @SWG\Schema(ref="#/definitions/categoryReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/categoryErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Category not Found",
     *      @SWG\Schema(ref="#/definitions/categoryErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Category",
     *      @SWG\Schema(ref="#/definitions/categoryErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a new Category in the database
     * Return the Attributes of the Category created
     * @param Request $request
     * @param $key
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function show(Request $request, $key)
    {
        try{
            $category = Category::whereCategoryKey($key)->firstOrFail();
            return response()->json($category, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Categorie'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Post(
     *  path="/category",
     *  summary="Create a Category",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Category"},
     *
     *  @SWG\Parameter(
     *      name="category",
     *      in="body",
     *      description="Category Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/category")
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
     *      name="X-ENTITY-KEY",
     *      in="header",
     *      description="Entity Key",
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
     *      description="the newly created Category",
     *      @SWG\Schema(ref="#/definitions/categoryReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/categoryErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Category not Found",
     *      @SWG\Schema(ref="#/definitions/categoryErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Category",
     *      @SWG\Schema(ref="#/definitions/categoryErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a new Category in the database
     * @param Request $request
     * @return static
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->keysRequired, $request);

        try{
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            do {
                $rand = str_random(32);

                if (!($exists = Category::whereCategoryKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);
            $category = $entity->categories()->create(
                [
                    'category_key'  => $key,
                    'name'          => $request->json('name'),
                    'description'   => $request->json('description'),
                    'created_by'    => $userKey
                ]
            );
            return response()->json($category, 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch(Exception $e){
            return response()->json(['error' => 'Failed to store new Category'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Put(
     *  path="/category/{category_key}",
     *  summary="Update a Category",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Category"},
     *
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Category Update Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/category")
     *  ),
     *
     * @SWG\Parameter(
     *      name="category_key",
     *      in="path",
     *      description="Category Key",
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
     *      description="The updated Category",
     *      @SWG\Schema(ref="#/definitions/categoryReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/categoryErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Category not Found",
     *      @SWG\Schema(ref="#/definitions/categoryErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Category",
     *      @SWG\Schema(ref="#/definitions/categoryErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Update a existing Category
     * Return the Attributes of the Category Updated
     * @param Request $request
     * @param $key
     * @return mixed
     */
    public function update(Request $request, $key)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);
        try{
            $category = Category::whereCategoryKey($key)->firstOrFail();
            $category->name         = $request->json('name');
            $category->description  = $request->json('description');

            $category->save();

            return response()->json($category, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Category'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *  @SWG\Definition(
     *     definition="replyDeleteCategory",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/category/{category_key}",
     *  summary="Delete a Category",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Category"},
     *
     * @SWG\Parameter(
     *      name="category_key",
     *      in="path",
     *      description="Category Key",
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteCategory")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/categoryErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="AccessMenu not Found",
     *      @SWG\Schema(ref="#/definitions/categoryErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete AccessMenu",
     *      @SWG\Schema(ref="#/definitions/categoryErrorDefault")
     *  )
     * )
     */

    /**
     * Delete existing Category
     * @param Request $request
     * @param $key
     * @return \Illuminate\Http\JsonResponse
     * @internal param $id
     */
    public function destroy(Request $request, $key)
    {
        $userKey = ONE::verifyToken($request);
        try{
            $category = Category::whereCategoryKey($key)->firstOrFail();
            Category::destroy($category->id);
            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Category'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    /**
     * Store an array of Category in the database
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createCategories(Request $request)
    {

        $userKey = ONE::verifyToken($request);

        try{
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            foreach ($request->json('categories') as $category) {
                if (isset($category['name']) && isset($category['description'])) {

                    do {
                        $rand = str_random(32);

                        if (!($exists = Category::whereCategoryKey($rand)->exists())) {
                            $key = $rand;
                        }
                    } while ($exists);

                    $categories = $entity->categories()->create(
                        [
                            'category_key'  => $key,
                            'name'          => $category['name'],
                            'description'   => $category['description'],
                            'created_by'    => $userKey
                        ]
                    );
                    $categoriesCreated[]=$categories;
                }
            }
            return response()->json($categoriesCreated, 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to store new Category'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
