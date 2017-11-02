<?php

namespace App\Http\Controllers;


use App\Action;
use App\ActionTranslation;
use App\One\One;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Collection;
use Exception;



class ActionController extends Controller
{

  public function index(Request $request)
  {
      try {
          $actions = Action::all();

          foreach ($actions as $action) {
              if (!($action->translation($request->header('LANG-CODE')))) {
                  if (!$action->translation($request->header('LANG-CODE-DEFAULT'))){
                      $translation = $action->actionTranslations()->first();
                      $action->translation($translation->language_code);
                  }
              }
          }

          return response()->json(['data' => $actions], 200);
      } catch (Exception $e) {

          return response()->json(['error' => 'Failed to retrieve the Action list'], 500);
      }
      return response()->json(['error' => 'Unauthorized'], 401);
  }


  public function show(Request $request, $id)
  {
      try {
          $action = Action::findOrFail($id);

          if (!($action->translation($request->header('LANG-CODE')))) {
              if (!$action->translation($request->header('LANG-CODE-DEFAULT'))){
                  $translation = $action->actionTranslations()->first();
                  $action->translation($translation->language_code);
              }
          }

          return response()->json($action, 200);
      } catch (ModelNotFoundException $e) {
          return response()->json(['error' => 'Action not Found'], 404);
      }
      return response()->json(['error' => 'Unauthorized'], 401);
  }

}
