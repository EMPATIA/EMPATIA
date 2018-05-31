<?php

namespace App\Http\Controllers;

use App\Currency;
use App\One\One;
use App\OrchUser;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
/**
 * Class CurrenciesController
 * @package App\Http\Controllers
 */


/**
 * @SWG\Tag(
 *   name="Currency",
 *   description="Everything about Currency",
 * )
 *
 *  @SWG\Definition(
 *      definition="currencyErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="Currency",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"currency", "symbol_left", "symbol_right", "code", "decimal_place", "decimal_point", "thousand_point"},
 *           @SWG\Property(property="currency", format="string", type="string"),
 *           @SWG\Property(property="symbol_left", format="string", type="string"),
 *           @SWG\Property(property="symbol_right", format="string", type="string"),
 *           @SWG\Property(property="code", format="string", type="string"),
 *           @SWG\Property(property="decimal_place", format="integer", type="integer"),
 *           @SWG\Property(property="decimal_point", format="string", type="string"),
 *           @SWG\Property(property="thousand_point", format="string", type="string")
 *       )
 *   }
 * )
 *
 */
class CurrenciesController extends Controller
{

    protected $keysRequired = [
        'currency',
        'symbol_left',
        'symbol_right',
        'code',
        'decimal_place',
        'decimal_point',
        'thousand_point'
    ];

    
    /**
     * Request list of all Currencies
     * Returns the list of all Currencies
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function index(Request $request)
    { 
        try{
            $currencies = Currency::query();
            $tableData = $request->input('tableData') ?? null;

            if (!empty($tableData)) {
                $recordsTotal = $currencies->count();

                $query = $currencies
                    ->orderBy($tableData['order']['value'], $tableData['order']['dir']);

                if(!empty($tableData['search']['value'])) {
                    $query = $query
                        ->where('code', 'like', '%'.$tableData['search']['value'].'%')
                        ->orWhere('currency', 'like', '%'.$tableData['search']['value'].'%')
                        ->orWhere('symbol_left', 'like', '%'.$tableData['search']['value'].'%')
                        ->orWhere('symbol_right', 'like', '%'.$tableData['search']['value'].'%');
                }

                $recordsFiltered = $query->count();

                $currencies = $query
                    ->skip($tableData['start'])
                    ->take($tableData['length'])
                    ->get();

                $data['currencies'] = $currencies;
                $data['recordsTotal'] = $recordsTotal;
                $data['recordsFiltered'] = $recordsFiltered;
            } else
                $data = $currencies->get();

            return response()->json(["data" => $data], 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Currencies list'], 500);
        }

    }

    /**
     *
     *
     * @SWG\Definition(
     *    definition="replyCurrency",
     *    required={"data"},
     *    @SWG\Property(
     *      property="data",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Currency")
     *      })
     *  )
     *
     * @SWG\Get(
     *  path="/currency/{currency_id}",
     *  summary="Show a Currency",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Currency"},
     *
     * @SWG\Parameter(
     *      name="currency_id",
     *      in="path",
     *      description="Currency Id",
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
     *      description="Show the Currency data",
     *      @SWG\Schema(ref="#/definitions/replyCurrency")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Currency not Found",
     *      @SWG\Schema(ref="#/definitions/currencyErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve Currency",
     *      @SWG\Schema(ref="#/definitions/currencyErrorDefault")
     *  )
     * )
     *
     */



    /**
     * Request of one Currency
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
                $currency = Currency::findOrFail($id);
                return response()->json($currency, 200);
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Currency not Found'], 404);
            }catch(Exception $e){
                return response()->json(['error' => 'Failed to retrieve the Currencie'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }

    /**
     *
    @SWG\Definition(
     *    definition="createCurrency",
     *    @SWG\Property(
     *      property="currency",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Currency")
     *      })
     *  )
     *
     * @SWG\Definition(
     *    definition="replyCreateCurrency",
     *    @SWG\Property(
     *      property="currency",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Currency")
     *      })
     *  )
     *
     * @SWG\Post(
     *  path="/currency",
     *  summary="Creation of a Currency",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Currency"},
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Currency data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/createCurrency")
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
     *      description="the newly created currency",
     *      @SWG\Schema(ref="#/definitions/replyCreateCurrency")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/currencyErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new Currency",
     *      @SWG\Schema(ref="#/definitions/currencyErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a new Currency in the database
     * Return the Attributes of the Currency created
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
                $currency = Currency::create(
                    [
                        'currency'          => $request->json('currency'),
                        'symbol_left'       => $request->json('symbol_left'),
                        'symbol_right'      => $request->json('symbol_right'),
                        'code'              => $request->json('code'),
                        'decimal_place'     => $request->json('decimal_place'),
                        'decimal_point'     => $request->json('decimal_point') ,
                        'thousand_point'    => $request->json('thousand_point')
                    ]
                );
                return response()->json($currency, 201);
            } catch(Exception $e){
                return response()->json(['error' => 'Failed to store Currency'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
    @SWG\Definition(
     *    definition="updateCurrency",
     *    @SWG\Property(
     *      property="currency",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Currency")
     *      })
     *  )
     *
     * @SWG\Definition(
     *    definition="replyUpdatedCurrency",
     *    @SWG\Property(
     *      property="currency",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/Currency")
     *      })
     *  )
     *
     * @SWG\Put(
     *  path="/currency/{currency_id}",
     *  summary="Update a Currency",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Currency"},
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="Currency Update data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/updateCurrency")
     *  ),
     *
     * @SWG\Parameter(
     *      name="currency_id",
     *      in="path",
     *      description="Currency Key",
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
     *      description="The updated Currency",
     *      @SWG\Schema(ref="#/definitions/replyUpdatedCurrency")
     *  ),
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/currencyErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="Currency not Found",
     *      @SWG\Schema(ref="#/definitions/currencyErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to update Currency",
     *      @SWG\Schema(ref="#/definitions/currencyErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Update a existing Currency
     * Return the Attributes of the Currency Updated
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $userKey = ONE::verifyToken($request);
        if (OrchUser::verifyRole($userKey, "admin")){
            try {
                $currency = Currency::findOrFail($id);
                ONE::verifyKeysRequest($this->keysRequired, $request);

                $currency->currency         = $request->json('currency');
                $currency->symbol_left      = $request->json('symbol_left');
                $currency->symbol_right     = $request->json('symbol_right');
                $currency->code             = $request->json('code');
                $currency->decimal_place    = $request->json('decimal_place');
                $currency->decimal_point    = $request->json('decimal_point');
                $currency->thousand_point   = $request->json('thousand_point');

                $currency->save();

                return response()->json($currency, 200);
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Currency not Found'], 404);
            }catch (Exception $e) {
                return response()->json(['error' => "Failed to update Currency"], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     *
     *  @SWG\Definition(
     *     definition="replyDeleteCurrency",
     *     @SWG\Property(property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/currency/{currency_id}",
     *  summary="Delete a Currency",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Currency"},
     *
     * @SWG\Parameter(
     *      name="currency_id",
     *      in="path",
     *      description="Currency Id",
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
     *      @SWG\Schema(ref="#/definitions/replyDeleteCurrency")
     *  ),
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete Currency",
     *      @SWG\Schema(ref="#/definitions/currencyErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/currencyErrorDefault")
     *   ),
     *  @SWG\Response(
     *      response="404",
     *      description="Currency not Found'",
     *      @SWG\Schema(ref="#/definitions/currencyErrorDefault")
     *  ),
     * )
     *
     */

    /**
     * Delete existing Currency
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $userKey = ONE::verifyToken($request);
        if (OrchUser::verifyRole($userKey, "admin")){
            try{
                Currency::destroy($id);

                return response()->json('Ok', 200);
            }catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Currency not Found'], 404);
            }catch (Exception $e) {
                return response()->json(['error' => 'Failed to delete Currency'], 500);
            }
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }
}
