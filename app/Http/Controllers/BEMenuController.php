<?php

namespace App\Http\Controllers;

use App\BEEntityMenu;
use App\BEEntityMenuElement;
use App\BEEntityMenuElementParameter;
use App\BEMenuElement;
use App\BEMenuElementParameter;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BEMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $menuUserKey = $request->get("userKey",null);

            $entityMenu = BEEntityMenu::with("orderedElements.parameters.parameter","orderedElements.menuElement.parameters")
                ->whereEntityId(ONE::getEntity($request)->id)
                ->whereUserKey($menuUserKey)
                ->first();

            if(!empty($entityMenu->orderedElements)) {
                $language = $request->header('LANG-CODE');
                $defaultLanguage = $request->header('LANG-CODE-DEFAULT');

                foreach ($entityMenu->orderedElements as $ordered_element) {
                    foreach ($ordered_element->menuElement->parameters as $menuElementParameter) {
                        $elementId = $menuElementParameter->id;

                        if ($ordered_element->parameters->where("be_menu_element_parameter_id", $elementId)->count() > 0)
                            $ordered_element->parameters->where("be_menu_element_parameter_id", $elementId)->first()
                                ->setAttribute("position", $menuElementParameter->pivot->position)
                                ->setAttribute("element_code", $menuElementParameter->pivot->code);

                    }

                    $ordered_element->setRelation("parameters", $ordered_element->parameters->sortBy("position"));
                    $ordered_element->newTranslation($language, $defaultLanguage);
                }
            }

            return response()->json($entityMenu, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Back Office Menu Element list'], 500);
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
            DB::beginTransaction();
            $menuUserKey = $request->get("userKey",null);
            $entityMenu = BEEntityMenu::firstOrCreate([
                "entity_id" => ONE::getEntity($request)->id,
                "user_key"  => $menuUserKey
            ]);

            if ($entityMenu->wasRecentlyCreated) {
                $key = null;
                do {
                    $rand = str_random(32);

                    if (!($exists = BEEntityMenu::whereMenuKey($rand)->exists())) {
                        $entityMenu->menu_key = $rand;
                        $entityMenu->save();
                    }
                } while ($exists);
            }

            $menuItem = BEMenuElement::where('key',$request->json('element'))->firstOrFail();

            $newPosition = 0;
            if ($entityMenu->elements()->orderBy("position","DESC")->exists()) {
                $newPosition = ($entityMenu->elements()->orderBy("position","DESC")->first()->position) + 1;
                if ($newPosition<=0)
                    $newPosition = 0;
            }

            $key = null;
            do {
                $rand = str_random(32);

                if (!($exists = BEMenuElementParameter::where('key',$rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $entityMenuElement = $entityMenu->elements()->create([
                "menu_key" => $key,
                "be_menu_element_id" => $menuItem->id,
                "position" => $newPosition
            ]);

            $parameters = $request->json("parameters");
            if (!is_null($parameters)) {
                foreach ($parameters as $parameterKey => $parameterValue) {
                    $parameter = BEMenuElementParameter::where('key', $parameterKey)->first();
                    if (!empty($parameter)) {
                        $entityMenuElement->parameters()->create([
                            "value" => $parameterValue,
                            "be_menu_element_parameter_id" => $parameter->id
                        ]);
                    }
                }
            }

            $translations = $request->json('translations');
            //Create The BE Menu Element Parameter Translations
            if (!is_null($translations)){
                foreach ($translations as $translation){
                    if (isset($translation['language_code']) && !empty($translation['name'])){
                        $entityMenuElement->translations()->create([
                            'language_code' => $translation['language_code'],
                            'name'          => $translation['name'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json($entityMenuElement, 201);
        }  catch(QueryException $e){
            DB::rollBack();
            return response()->json(['error' => 'Failed to store new BE Menu Element'], 500);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to store new BE Menu Element'], 500);
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
            $menuUserKey = $request->get("userKey",null);

            $entityMenu = BEEntityMenu::whereEntityId(ONE::getEntity($request)->id)->whereUserKey($menuUserKey)->firstOrFail();

            $menuElement = $entityMenu->elements()->with("parameters.parameter","menuElement","translations")->whereMenuKey($key)->firstOrFail();

            $language = $request->header('LANG-CODE');
            $defaultLanguage = $request->header('LANG-CODE-DEFAULT');

            $menuElement->newTranslation($language,$defaultLanguage);
            $menuElement->menuElement->newTranslation($language,$defaultLanguage);
            foreach ($menuElement->parameters as $parameter) {
                $parameter->parameter->newTranslation($language,$defaultLanguage);
            }

            return response()->json($menuElement, 200);
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
            DB::beginTransaction();
            $menuUserKey = $request->get("userKey",null);

            $entityMenu = BEEntityMenu::whereEntityId(ONE::getEntity($request)->id)->whereUserKey($menuUserKey)->firstOrFail();

            $entityMenuElement = $entityMenu->elements()->whereMenuKey($key)->first();

            $parameters = $request->json("parameters");
            if (!is_null($parameters)) {
                foreach ($parameters as $parameterKey => $parameterValue) {
                    $parameter = BEMenuElementParameter::where('key', $parameterKey)->first();
                    if (!empty($parameter)) {
                        $newParameter = $entityMenuElement->parameters()->firstOrCreate([
                            "be_menu_element_parameter_id" => $parameter->id
                        ]);

                        $newParameter->value = $parameterValue;
                        $newParameter->save();
                    }
                }
            }

            $translations = $request->json('translations');
            //Create The BE Menu Element Parameter Translations
            if (!is_null($translations)){
                foreach ($translations as $translation){
                    if (isset($translation['language_code']) && !empty($translation['name'])){
                        $newTranslation = $entityMenuElement->translations()->firstOrCreate([
                            'language_code' => $translation['language_code']
                        ]);

                        $newTranslation->name = $translation['name'];
                        $newTranslation->save();
                    }
                }
            }

            DB::commit();

            return response()->json($entityMenuElement, 200);
        }  catch(QueryException $e){
            DB::rollBack();
            return response()->json(['error' => 'Failed to store new BE Menu Element'], 500);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to store new BE Menu Element'], 500);
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
        ONE::verifyToken($request);

        try {
            $menuUserKey = $request->get("userKey",null);

            $entityMenu = BEEntityMenu::whereEntityId(ONE::getEntity($request)->id)->whereUserKey($menuUserKey)->firstOrFail();

            $entityMenuElement = $entityMenu->elements()->with("childs")->whereMenuKey($key)->first();

            foreach ($entityMenuElement->childs as $entityMenuElementChild) {
                $entityMenuElementChild->delete();
            }

            $entityMenuElement->delete();
            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'BE Menu Element not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete the BE Menu Element'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    public function menuRenderData(Request $request) {
        $userKey = ONE::verifyToken($request);
        try {
            $entityMenu = BEEntityMenu::whereEntityId(ONE::getEntity($request)->id)->whereUserKey($userKey)->first();

            if (empty($entityMenu))
                $entityMenu = BEEntityMenu::whereEntityId(ONE::getEntity($request)->id)->first();

            if (!empty($entityMenu)) {
                $entityMenu->load("orderedElements.parameters.parameter","orderedElements.menuElement.parameters");
                foreach ($entityMenu->orderedElements as $ordered_element) {
                    foreach ($ordered_element->menuElement->parameters as $menuElementParameter) {
                        $elementId = $menuElementParameter->id;

                        if ($ordered_element->parameters->where("be_menu_element_parameter_id",$elementId)->count()>0)
                            $ordered_element->parameters->where("be_menu_element_parameter_id",$elementId)->first()
                                ->setAttribute("position",$menuElementParameter->pivot->position)
                                ->setAttribute("element_code",$menuElementParameter->pivot->code);

                    }

                    $ordered_element->setRelation("parameters",$ordered_element->parameters->sortBy("position"));
                }

                $language = $request->header('LANG-CODE');
                $defaultLanguage = $request->header('LANG-CODE-DEFAULT');
                foreach ($entityMenu->orderedElements as $element) {
                    $element->newTranslation($language,$defaultLanguage);
                }
                return response()->json($entityMenu, 200);
            }

            return response()->json([],400);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the Back Office Menu Element list'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function reorder(Request $request, $key) {
        ONE::verifyToken($request);
        try {
            $menuUserKey = $request->get("userKey",null);
            $positions = $request->json('positions');
            
            $entityMenu = BEEntityMenu::whereEntityId(ONE::getEntity($request)->id)->whereUserKey($menuUserKey)->firstOrFail();

            if(!empty($request->json('parent_key',''))){
                $menu = $entityMenu->elements()->whereMenuKey($key)->firstOrFail();
                $parentMenu = $entityMenu->elements()->whereMenuKey($request->json('parent_key'))->first();
                $menu->parent_id = $parentMenu->id;
                $menu->save();
            }

            foreach($positions as $position => $menuKey){
                $menuTmp = $entityMenu->elements()->whereMenuKey($menuKey)->firstOrFail();

                if (empty($request->json('parent_key')))
                    $menuTmp->parent_id = 0;

                $menuTmp->position = $position;
                $menuTmp->save();
            }

            return response()->json("success",200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to reorder Menu'], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Menu not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized' ], 401);
    }


    public function import(Request $request) {
        function saveBEEntityMenuElement($element, $BEEntityMenuId, $parentId = 0) {
            $element = $element->replicate();

            $element->be_entity_menu_id = $BEEntityMenuId;
            $element->parent_id = $parentId;

            do {
                $rand = str_random(32);
                if (!($exists = BEEntityMenuElement::whereMenuKey($rand)->exists()))
                    $element->menu_key = $rand;
            } while ($exists);
            $element->save();

            foreach ($element->translations as $translation) {
                $translation = $translation->replicate();
                $translation->be_entity_menu_element_id = $element->id;
                $translation->save();
            }

            foreach ($element->parameters as $parameter) {
                $parameter = $parameter->replicate();
                $parameter->be_entity_menu_element_id = $element->id;
                $parameter->save();
            }

            foreach ($element->childs as $child) {
                saveBEEntityMenuElement($child,$BEEntityMenuId,$element->id);
            }
        }

        try {
            $menuUserKey = $request->get("userKey",null);
            DB::beginTransaction();

            $entityId = ONE::getEntity($request)->id;
            BEEntityMenu::whereEntityId($entityId)->whereUserKey($menuUserKey)->delete();

            if (!empty($menuUserKey))
                $defaultMenuClone = BEEntityMenu::whereEntityId($entityId)->whereNull("user_key")->first();
            
            if(empty($defaultMenuClone)) 
                $defaultMenuClone = BEEntityMenu::whereMenuKey("defaultEntityMenu")->first();

            $defaultMenuClone = $defaultMenuClone
                ->load("elements.childs.translations", "elements.childs.parameters", "elements.translations", "elements.parameters")
                ->replicate();

            $defaultMenuClone->entity_id = $entityId;
            $defaultMenuClone->user_key = $menuUserKey;

            do {
                $rand = str_random(32);
                if (!($exists = BEEntityMenu::whereMenuKey($rand)->exists())) {
                    $defaultMenuClone->menu_key = $rand;
                    $defaultMenuClone->save();
                }
            } while ($exists);

            foreach ($defaultMenuClone->elements as $element) {
                if ($element->parent_id==0)
                    saveBEEntityMenuElement($element, $defaultMenuClone->id);
            }

            DB::commit();
            return response()->json(["success"=>true], 200);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to retreive default Menu'], 500);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to import default Menu'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
