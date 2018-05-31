<?php

namespace App\Http\Controllers;

use Exception;
use App\One\One;
use App\ShortLink;
use App\BEEntityMenu;
use App\BEMenuElement;
use App\BEEntityMenuElement;
use Illuminate\Http\Request;
use App\BEMenuElementParameter;
use Illuminate\Support\Facades\DB;
use App\BEEntityMenuElementParameter;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ShortLinksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $entityKey = One::getEntity($request)->entity_key;

            $query = ShortLink::whereEntityKey($entityKey);

            $recordsTotal = $query->count();
            $tableData = $request->input('tableData') ?? null;

            if (!empty($tableData["order"]["value"]) && !empty($tableData["order"]["dir"]) )
                $query = $query->orderBy($tableData['order']['value'], $tableData['order']['dir']);

            if(!empty($tableData['search']['value'])) {
                $query = $query
                    ->where(function($q) use ($tableData) {
                        $q
                            ->where('short_link_key', 'like', '%'.$tableData['search']['value'].'%')
                            ->orWhere('name', 'like', '%'.$tableData['search']['value'].'%')
                            ->orWhere('code', 'like', '%'.$tableData['search']['value'].'%')
                            ->orWhere('url', 'like', '%'.$tableData['search']['value'].'%')
                            ->orWhere('hits', '=', $tableData['search']['value']);
                    });
            }

            $recordsFiltered = $query->count();

            if (!empty($tableData["start"]))
                $query = $query->skip($tableData['start']);

            if (!empty($tableData["length"]))
                $query = $query->take($tableData['length']);

            $shortLinks = $query->get();

            $data['shortLinks'] = $shortLinks;
            $data['recordsTotal'] = $recordsTotal;
            $data['recordsFiltered'] = $recordsFiltered;

            return response()->json($data, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Failed to retrieve the Short Links list'], 500);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Short Links list'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $userKey = ONE::verifyToken($request);
        
        try {
            $entityKey = One::getEntity($request)->entity_key;
            
            /* Entity has Short Link with the same code? */
            if (ShortLink::whereEntityKey($entityKey)->whereCode($request->get("code"))->exists())
                return response()->json(['error' => 'Code Already used'], 400);

            do {
                $key = '';
                $rand = str_random(32);
                if (!($exists = ShortLink::whereShortLinkKey($rand)->exists()))
                    $key = $rand;
            } while ($exists);

            $shortLink = ShortLink::create([
                "short_link_key" => $key,
                "name" => $request->get("name"),
                "code" => $request->get("code"),
                "entity_key" => $entityKey,
                "url" => $request->get("url"),
            ]);

            return response()->json($shortLink, 201);
        }  catch(QueryException $e){
            return response()->json(['error' => 'Failed to store new Short Link'], 500);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Short Link'], 500);
        }

        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $key
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $key)
    {
        try {
            $entityKey = One::getEntity($request)->entity_key;

            $shortLink = ShortLink::whereEntityKey($entityKey)->whereShortLinkKey($key)->firstOrFail();
            return response()->json($shortLink, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the BE Menu Element'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $key
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $key)
    {
        $userKey = ONE::verifyToken($request);
        
        try {
            $entityKey = One::getEntity($request)->entity_key;
            
            /* Entity has Short Link with the same code? */
            $shortLink = ShortLink::whereEntityKey($entityKey)->whereShortLinkKey($key)->firstOrFail();
            $shortLink->name = $request->get("name");
            $shortLink->code = $request->get("code");
            $shortLink->url = $request->get("url");
            $shortLink->save();

            return response()->json($shortLink, 201);
        }  catch(QueryException $e){
            return response()->json(['error' => 'Failed to update Short Link'], 500);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Short Link'], 500);
        }

        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $key
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $key) {
        $userKey = ONE::verifyToken($request);
        
        try {
            $entityKey = One::getEntity($request)->entity_key;
            
            /* Entity has Short Link with the same code? */
            $shortLink = ShortLink::whereEntityKey($entityKey)->whereShortLinkKey($key)->firstOrFail();
            $shortLink->delete();

            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Short Link not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete the Short Link'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function resolve(Request $request, $code) {
        try {
            $entityKey = One::getEntity($request)->entity_key;

            $shortLink = ShortLink::whereEntityKey($entityKey)->whereCode($code)->firstOrFail();
            $shortLink->hits++;
            $shortLink->save();

            return response()->json($shortLink->url, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the BE Menu Element'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
