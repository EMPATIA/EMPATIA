<?php

namespace App\Http\Controllers;

use App\Country;
use App\One\One;
use App\OrchUser;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class CountriesController
 * @package App\Http\Controllers
 */


/**
 * @SWG\Tag(
 *   name="Country",
 *   description="Everything about Countries",
 * )
 *
 *  @SWG\Definition(
 *      definition="countryErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="Country",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"id", "name", "code"},
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 *
 */

class CountriesController extends Controller
{

    protected $keysRequired = [
        'name',
        'code'
    ];

    /**
     * Request list of all Countries
     * Returns the list of all Countries
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function index(Request $request)
    {
        try{
            $countries = Country::all();
            return response()->json(['data' => $countries], 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Countries list'], 500);
        }
    }

    /**
     *
     *
     * @SWG\Definition(
     *    definition="replyCountry",
     *    required={"data"},
     *    @SWG\Property(
     *      property="data",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Country")
     *      })
     *  )
     *
     * @SWG\Get(
     *  path="/country/{country_id}",
     *  summary="List a Country",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Country"},
     *
     * @SWG\Parameter(
     *      name="country_id",
     *      in="path",
     *      description="Country Id",
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
     *      description="Show the Country data",
     *      @SWG\Schema(ref="#/definitions/replyCountry")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Country not Found",
     *      @SWG\Schema(ref="#/definitions/countryErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Country",
     *      @SWG\Schema(ref="#/definitions/countryErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Request of one Country
     * Returns the attributes of the Country
     * @param Request $request
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function show(Request $request, $id)
    {
        $userKey = ONE::verifyToken($request);
        if (OrchUser::verifyRole($userKey, "admin")){
            try {
                $country = Country::findOrFail($id);
                return response()->json($country, 200);
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Country not Found'], 404);
            }catch (Exception $e) {
                return response()->json(['error' => "Failed to retrieve Country"], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Definition(
     *     definition="createCountry",
     *
     *     required={"name","code"},
     *
     *     @SWG\Property(
     *     property="name",
     *     type="string",
     *     format="string"
     * ),
     *     @SWG\Property(
     *     property="code",
     *     type="string",
     *     format="string"
     * )
     * )
     *
     * @SWG\Definition(
     *    definition="replyCreateCountry",
     *    @SWG\Property(
     *      property="country",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Country")
     *      })
     *  )
     *
     * @SWG\Post(
     *  path="/country",
     *  summary="Creation of a Country",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Country"},
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Country data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/createCountry")
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
     *      description="the newly created country",
     *      @SWG\Schema(ref="#/definitions/replyCreateCountry")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/countryErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Country",
     *      @SWG\Schema(ref="#/definitions/countryErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a new Country in the database
     * Return the Attributes of the Country created
     * @param Request $request
     * @return static
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->keysRequired, $request);
        if (OrchUser::verifyRole($userKey, "admin")){
            try{
                $country = Country::create(
                    [
                        'name' => $request->json('name'),
                        'code' => $request->json('code')
                    ]
                );
                return response()->json($country, 201);
            }
            catch(Exception $e){
                return response()->json(['error' => 'Failed to store new Country'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Definition(
     *     definition="updateCountry",
     *
     *     required={"name","code"},
     *
     *     @SWG\Property(
     *     property="name",
     *     type="string",
     *     format="string"
     * ),
     *     @SWG\Property(
     *     property="code",
     *     type="string",
     *     format="string"
     * )
     * )
     *
     * @SWG\Definition(
     *    definition="replyUpdatedCountry",
     *    @SWG\Property(
     *      property="country",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Country")
     *      })
     *  )
     *
     * @SWG\Put(
     *  path="/country/{country_id}",
     *  summary="Update a Country",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Country"},
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Country Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/updateCountry")
     *  ),
     *
     * @SWG\Parameter(
     *      name="country_id",
     *      in="path",
     *      description="Level Parameter Key",
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
     *      description="The updated Country",
     *      @SWG\Schema(ref="#/definitions/replyUpdatedCountry")
     *  ),
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/countryErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Country not Found",
     *      @SWG\Schema(ref="#/definitions/countryErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Country",
     *      @SWG\Schema(ref="#/definitions/countryErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Update a existing Country
     * Return the Attributes of the Country Updated
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
                $country        = Country::findOrFail($id);
                $country->name  = $request->json('name');
                $country->code  = $request->json('code');

                $country->save();

                return response()->json($country, 200);
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Country not Found'], 404);
            }catch (Exception $e) {
                return response()->json(['error' => 'Failed to update Country'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     *  @SWG\Definition(
     *     definition="replyDeleteCountry",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/country/{country_id}",
     *  summary="Delete a Country",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Country"},
     *
     * @SWG\Parameter(
     *      name="country_id",
     *      in="path",
     *      description="Country Id",
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteCountry")
     *  ),
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Country",
     *      @SWG\Schema(ref="#/definitions/countryErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/countryErrorDefault")
     *   ),
     *  @SWG\Response(
     *      response="404",
     *      description="Country not Found'",
     *      @SWG\Schema(ref="#/definitions/countryErrorDefault")
     *  ),
     * )
     *
     */

    /**
     * Delete existing Country
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $userKey = ONE::verifyToken($request);
        if (OrchUser::verifyRole($userKey, "admin")){
            try{
                Country::destroy($id);
                return response()->json('Ok', 200);
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Country not Found'], 404);
            }catch (Exception $e) {
                return response()->json(['error' => 'Failed to delete Country'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
