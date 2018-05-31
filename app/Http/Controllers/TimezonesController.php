<?php

namespace App\Http\Controllers;

use App\One\One;
use App\Timezone;
use App\OrchUser;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class TimezonesController
 * @package App\Http\Controllers
 */


/**
 * @SWG\Tag(
 *   name="Timezone",
 *   description="Everything about Timezone",
 * )
 *
 *  @SWG\Definition(
 *      definition="timezoneErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  ),
 *
 *  @SWG\Definition(
 *   definition="Timezone",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"country_code", "name"},
 *           @SWG\Property(property="country_code", format="string", type="string"),
 *           @SWG\Property(property="name", format="string", type="string")
 * )
 *   }
 * )
 *
 */

class TimezonesController extends Controller
{
    protected $keysRequired = [
        'name',
        'country_code'
    ];

    /**
     * Request of one Timezone
     * Returns the attributes of the Timezone
     * @param Request $request
     * @return list of all
     * @internal param $
     */
    public function index(Request $request)
    {
        try{
            $timezones = Timezone::query();
            $tableData = $request->input('tableData') ?? null;

            if (!empty($tableData)) {
                $recordsTotal = $timezones->count();

                $query = $timezones
                    ->orderBy($tableData['order']['value'], $tableData['order']['dir']);

                if(!empty($tableData['search']['value'])) {
                    $query = $query
                        ->where('country_code', 'like', '%'.$tableData['search']['value'].'%')
                        ->orWhere('name', 'like', '%'.$tableData['search']['value'].'%');
                }

                $recordsFiltered = $query->count();

                $timezones = $query
                    ->skip($tableData['start'])
                    ->take($tableData['length'])
                    ->get();

                $data['timezones'] = $timezones;
                $data['recordsTotal'] = $recordsTotal;
                $data['recordsFiltered'] = $recordsFiltered;
            } else
                $data = $timezones->orderBy('name')->get();

            return response()->json(["data" => $data], 200);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Timezones'], 500);
        }

    }

    /**
     *
     *
     * @SWG\Definition(
     *    definition="replyTimezone",
     *    required={"data"},
     *    @SWG\Property(
     *      property="data",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Timezone")
     *      })
     *  )
     *
     * @SWG\Get(
     *  path="/timezone/{timezone_id}",
     *  summary="Show a Timezone",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Timezone"},
     *
     * @SWG\Parameter(
     *      name="timezone_id",
     *      in="path",
     *      description="Timezone Id",
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
     *      description="Show the Timezone data",
     *      @SWG\Schema(ref="#/definitions/replyTimezone")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Timezone not Found",
     *      @SWG\Schema(ref="#/definitions/timezoneErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Timezone",
     *      @SWG\Schema(ref="#/definitions/timezoneErrorDefault")
     *  )
     * )
     *
     */


    /**
     * Store a new Timezone in the database
     * Return the Attributes of the Timezone created
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
                $timezone = Timezone::findOrFail($id);
                return response()->json($timezone, 200);
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Timezone not Found'], 404);
            }catch (Exception $e) {
                return response()->json(['error' => 'Failed to retrieve Timezone'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Definition(
     *    definition="createTimezone",

     *    @SWG\Property(
     *      property="timezone",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Timezone")
     *      })
     *  )
     *
     * @SWG\Definition(
     *    definition="replyCreateTimezone",
     *    @SWG\Property(
     *      property="timezone",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Timezone")
     *      })
     *  )
     *
     * @SWG\Post(
     *  path="/timezone",
     *  summary="Creation of a Timezone",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Timezone"},
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Timezone data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/createTimezone")
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
     *      description="the newly created timezone",
     *      @SWG\Schema(ref="#/definitions/replyCreateTimezone")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/timezoneErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Timezone",
     *      @SWG\Schema(ref="#/definitions/timezoneErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a new Timezone in the database
     * @param Request $request
     *
     * @return static
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->keysRequired, $request);
        if (OrchUser::verifyRole($userKey, "admin")){
            try{
                $timezone = Timezone::create( 
                    [
                        'name'      => $request->json('name'),
                        'country_code'      => $request->json('country_code')
                    ]
                );
                return response()->json($timezone, 201);
            }catch(Exception $e){
                return response()->json(['error' => 'Failed to store new Timezone'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     * @SWG\Definition(
     *    definition="updateTimezone",
     *    @SWG\Property(
     *      property="timezone",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Timezone")
     *      })
     *  )
     *
     * @SWG\Definition(
     *    definition="replyUpdatedTimezone",
     *    @SWG\Property(
     *      property="timezone",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Timezone")
     *      })
     *  )
     *
     * @SWG\Put(
     *  path="/timezone/{timezone_id}",
     *  summary="Update a Timezone",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Timezone"},
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Timezone Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/updateTimezone")
     *  ),
     *
     * @SWG\Parameter(
     *      name="timezone_id",
     *      in="path",
     *      description="Timezone Id",
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
     *      description="The updated Timezone",
     *      @SWG\Schema(ref="#/definitions/replyUpdatedTimezone")
     *  ),
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/timezoneErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Timezone not Found",
     *      @SWG\Schema(ref="#/definitions/timezoneErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Timezone",
     *      @SWG\Schema(ref="#/definitions/timezoneErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Update a existing Timezone
     * Return the Attributes of the Timezone Updated
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
                $timezone = Timezone::findOrFail($id);
                $timezone->name = $request->json('name');
                $timezone->country_code = $request->json('country_code');
                $timezone->save();

                return response()->json($timezone, 200);
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Timezone not Found'], 404);
            }catch (Exception $e) {
                return response()->json(['error' => 'Failed to update Timezone'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     *  @SWG\Definition(
     *     definition="replyDeleteTimezone",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/timezone/{timezone_id}",
     *  summary="Delete a Timezone",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Timezone"},
     *
     * @SWG\Parameter(
     *      name="timezone_id",
     *      in="path",
     *      description="Timezone Id",
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteTimezone")
     *  ),
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Timezone",
     *      @SWG\Schema(ref="#/definitions/timezoneErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/timezoneErrorDefault")
     *   ),
     *  @SWG\Response(
     *      response="404",
     *      description="Timezone not Found'",
     *      @SWG\Schema(ref="#/definitions/timezoneErrorDefault")
     *  ),
     * )
     *
     */

    /**
     * Delete existing Timezone
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $userKey = ONE::verifyToken($request);
        if (OrchUser::verifyRole($userKey, "admin")){
            try{
                Timezone::destroy($id);
                return response()->json('Ok', 200);
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Timezone not Found'], 404);
            }catch (Exception $e) {
                return response()->json(['error' => 'Failed to delete Timezone'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
