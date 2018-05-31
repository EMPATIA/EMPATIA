<?php

namespace App\Http\Controllers;

use App\CbChecklist;
use App\One\One;
use App\Site;
use Exception;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;


/**
 * Class CbChecklistsController
 * @package App\Http\Controllers
 */

class CbChecklistsController extends Controller
{
    protected $keysRequired = [
        'checklist_key',
        'title',
        'comments',
        'position',
        'state'
    ];

    /**
     * Requests a list of Cbs check list.
     * Returns the list of  Cbs check list.
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            if(!is_null($request->cb_key)){
                $checklists = CbChecklist::where('cb_key', '=', $request->cb_key)
                                            ->orderBy('state', 'desc')
                                            ->orderBy('title', 'asc')
                                            ->get();
            }
            else{
                $checklists = CbChecklist::where('cb_key', '=', null)
                                            ->orderBy('state', 'desc')
                                            ->orderBy('title', 'asc')
                                            ->get();
            }

            return response()->json(['data' => $checklists], 200);

        }
        catch(Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Cbs check list'], 500);
        }

        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Store a newly created Cb check list in storage.
     * Returns the details of the newly created Cb check list.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {

            foreach ($request->text as $key_newCb => $newCb) {
                $key = null;
                do {
                    $rand = str_random(32);

                    if (!($exists = Site::where('key', $rand)->exists())) {
                        $key = $rand;
                    }
                } while ($exists);

                $checklist = CbChecklist::create([
                    'checklist_key' => $key,
                    'title' => trim($newCb),
                    'position' => 0,
                    'state' => 'none',
                    'entity_key' => !empty($request->json('entityKey')) ? $request->json('entityKey') : null,
                    'cb_key' => !empty($request->json('cbKey')) ? $request->json('cbKey') : null,
                ]);

                foreach ($request->checked as $checked_key => $checked) {
                    if ($key_newCb == $checked_key && strcmp($checked, 'none') != 0) {
                        $checklist->state = $checked;
                        $checklist->save();
                    }
                }

                foreach ($request->state as $state_key => $state) {
                    if ($key_newCb == $state_key && strcmp($state, 'none') != 0) {
                        $checklist->state = $state;
                        $checklist->save();
                    }
                }
            }

            return response()->json(['data' => $checklist], 201);
        }
        catch(Exception $e){
            return response()->json(['error' => 'Failed to store new cb check list'], 500);
        }

        return response()->json(['error' => 'Unauthorized' ], 401);
    }

    /**
     * Delete existing Cb check list
     * @param Request $request
     * @param $checklistKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $checklist_Key)
    {
        try{
            $checklist = CbChecklist::where('checklist_Key','=', $checklist_Key)->first();

            if(!is_null($checklist))
                $checklist->delete();

            return response()->json('Ok', 200);

        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Cb check list not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Cb check list'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function update(Request $request, $checklistKey)
    {
        try {
            $checklist = CbChecklist::where('checklist_key', '=', $checklistKey)->firstOrFail();

            $checklist->state = collect($request->all())->first()['state'];
            $checklist->save();

            return response()->json('Ok', 200);
        }catch (Exception $e){

        }
    }
}
