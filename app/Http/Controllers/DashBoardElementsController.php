<?php

namespace App\Http\Controllers;

use App\Cb;
use App\CbType;
use App\DashboardElement;
use App\DashBoardElementConfiguration;
use App\DashBoardElementUser;
use App\Entity;
use App\EntityCb;
use App\EntityDashBoardElement;
use App\One\One;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashBoardElementsController extends Controller
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

            $dashBoardElements = DashboardElement::get();
            foreach ($dashBoardElements as $dashBoardElement) {
                $dashBoardElement->newTranslation($request->header('LANG-CODE'),$request->header('LANG-CODE-DEFAULT'));
            }

            return response()->json(['data' => $dashBoardElements], 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * set translation of resource
     * @param $dashBoardElement
     * @param $translations
     */
    public function setTranslations($dashBoardElement, $translations)
    {
        if (!empty($dashBoardElement->translations()->get())) {
            $dashBoardElement->translations()->delete();
        }

        foreach ($translations as $translation) {
            $dashBoardElement->translations()->create(
                [
                    'language_code' => $translation['language_code'],
                    'title'         => $translation['title'],
                    'description'   => empty($translation['description']) ? null : $translation['description']
                ]
            );

        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $dashBoardElement = DashboardElement::create(['code' => $request->json('code'),'default_position' => $request->json('default_position')]);

            if ($request->json('translations')) {
                $translations = $request->json('translations');

                $this->setTranslations($dashBoardElement, $translations);
            }

            $dashBoardElement->translations = $dashBoardElement->translations()->get()->keyBy("language_code")->toArray();

            return response()->json(['data' => $dashBoardElement], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new DashBoard Element'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            $dashBoardElement = DashboardElement::with("configurations","translations")->findOrFail($id);
            $dashBoardElement->translations = $dashBoardElement->translations->keyBy("language_code")->toArray();

            foreach ($dashBoardElement->configurations as $configuration) {
                $configuration->newTranslation($request->header('LANG-CODE'),$request->header('LANG-CODE-DEFAULT'));
            }

            $dashBoardElement->available_configurations = DashBoardElementConfiguration::get();
            foreach ($dashBoardElement->available_configurations as $available_configuration) {
                $available_configuration->newTranslation($request->header('LANG-CODE'),$request->header('LANG-CODE-DEFAULT'));
            }

            return response()->json(['data' => $dashBoardElement], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'DashBoard Element not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the DashBoard Element'], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {


            $dashBoardElement = DashboardElement::findOrFail($id);

            if ($request->json('code')) {
                $dashBoardElement->code = $request->json('code');
            }

            if ($request->json('default_position')) {
                $dashBoardElement->default_position = $request->json('default_position');
            }

            $dashBoardElement->save();

            if ($request->json('translations')) {
                $translations = $request->json('translations');
                $this->setTranslations($dashBoardElement, $translations);
            }

            $dashBoardElement->configurations()->detach();
            if($request->json('configurations')){
                foreach ($request->json('configurations') as $configuration){
                    $dashBoardElement->configurations()->attach($configuration,['default_value' => DashBoardElementConfiguration::findOrFail($configuration)->default_value]);
                }
            }


            $dashBoardElement->translations = $dashBoardElement->translations()->get()->keyBy("language_code")->toArray();

            return response()->json(['data' => $dashBoardElement], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'DashBoard Element not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the DashBoard Element'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DashboardElement::destroy($id);
            return response()->json('Ok', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'DashBoard Element not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete DashBoard Element'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function getConfigurations(Request $request,$id)
    {
        try {

            $dashBoardElement = DashboardElement::find($id);

            $configurations = $dashBoardElement->configurations()->get();

            return response()->json(['data' => $configurations], 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    public function getEntityDashBoardElements(Request $request)
    {

        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

            $entity->entityDashBoardElements = EntityDashBoardElement::whereEntityId($entity->id)->get();
            $entity->availableDashBoardElements = DashboardElement::get();
            foreach ($entity->availableDashBoardElements as $availableDashBoardElement) {
                $availableDashBoardElement->newTranslation($request->header('LANG-CODE'), $request->header('LANG-CODE-DEFAULT'));
            }

            return response()->json(['data' => $entity], 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    public function updateEntityDashBoardElements(Request $request,$dashBoardElementId)
    {

        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

            if(!empty($element = EntityDashBoardElement::whereEntityId($entity->id)->whereDashboardElementId($dashBoardElementId)->first())){
                $element->delete();
            }else{
                EntityDashBoardElement::create([
                    'entity_id' => $entity->id,
                    'dashboard_element_id' => $dashBoardElementId,
                    'position' => DashboardElement::find($dashBoardElementId)->default_position,
                ]);
            }
            return response()->json('OK', 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getAvailableDashBoardElementsWithConfigurations(Request $request)
    {
        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            $userKey = ONE::verifyToken($request);
            $user = User::whereUserKey($userKey)->firstOrFail();

            $entityDashBoardElements = EntityDashBoardElement::whereEntityId($entity->id)->get();

            $dashBoardElements = [];
            foreach ($entityDashBoardElements as $entityDashBoardElement){
                $currentDashBoardElement = DashboardElement::with("configurations")->findOrFail($entityDashBoardElement->dashboard_element_id);

                $currentDashBoardElement->newTranslation($request->header('LANG-CODE'),$request->header('LANG-CODE-DEFAULT'));

                foreach ($currentDashBoardElement->configurations as $configuration) {
                    $configuration->newTranslation($request->header('LANG-CODE'),$request->header('LANG-CODE-DEFAULT'));
                }

                $dashBoardElements[] = $currentDashBoardElement;
            }

            $userDashBoardElements = DashBoardElementUser::with('configurations')->whereUserId($user->id)->whereEntityId($entity->id)->orderBy("position")->get();

            $data['available_entity_elements'] = $dashBoardElements;

            $data['current_user_elements'] = $userDashBoardElements;

            if($userDashBoardElements->isEmpty()){
                $this->attachDashBoardElementsToUser($dashBoardElements,$user,$entity,$request);
                $data['current_user_elements'] = DashBoardElementUser::with('configurations')->whereUserId($user->id)->whereEntityId($entity->id)->get();
            }

            return response()->json(['data' => $data], 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function attachDashBoardElementsToUser($dashBoardElements,$user,$entity,$request)
    {
        try {
            if(!empty($dashBoardElements)){
                foreach ($dashBoardElements as $dashBoardElement){
                    $dashBoardElementUser = DashBoardElementUser::create([
                        'dashboard_element_id' => $dashBoardElement->id,
                        'user_id' => $user->id,
                        'entity_id' => $entity->id,
                        'position' => $dashBoardElement->default_position
                    ]);
                    $dashBoardElementUser->configurations()->detach();
                    foreach ($dashBoardElement->configurations as $configuration){
                        if(($configuration->code === 'pad_type' || $configuration->code === 'pad_key') && $configuration->default_value == 'all'){
                            $defaultValues = $this->getDefaultParticipationKey($request);
                            $configuration->default_value = $defaultValues[0][$configuration->code];
                        } else if ($configuration->code == "title" && !empty($dashBoardElement->title)) {
                            $configuration->default_value = $dashBoardElement->title;
                        } else if ($configuration->code == "description" && !empty($dashBoardElement->description)) {
                            $configuration->default_value = $dashBoardElement->description;
                        }

                        $dashBoardElementUser->configurations()->attach($configuration->id,['value' => $configuration->default_value]);

                    }
                }
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }

    public function getDefaultParticipationKey($request)
    {
        try{
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            $entityCb = EntityCb::with('cbType')->whereEntityId($entity->id)->first();
            if(empty($entityCb)){
                return null;
            }
            $cbType = CbType::find($entityCb->cb_type_id);
            $cb = CB::whereCbKey($entityCb->cb_key)->with('configurations')->firstOrFail();
            $cb->type = $cbType->code;

            $info = array([
                'pad_key' => $cb->cb_key,
                'pad_type' => $cb->type
            ]);

            return $info;

        } catch (Exception $e) {
            return null;
        }
    }
    
    public function setUserDashBoardElement(Request $request)
    {
        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

            $user = User::whereUserKey($request['userKey'])->firstOrFail();
            $attributes = $request['attributes'];

            $dashBoarElement = $attributes['dash_board_element'];

            $entityDashBoardElements = EntityDashBoardElement::whereEntityId($entity->id)->get();

            if(EntityDashBoardElement::whereEntityId($entity->id)->whereDashboardElementId($dashBoarElement)->exists()){
                $dashBoardElementUser = DashBoardElementUser::create([
                    'dashboard_element_id' => $dashBoarElement,
                    'user_id' => $user->id,
                    'entity_id' => $entity->id,
                    'position' => $attributes['position'] ?? DashboardElement::findOrFail($dashBoarElement)->default_position
                ]);
                unset($attributes['dash_board_element']);

                $dashBoardElementUser->configurations()->detach();
                foreach ($attributes as $key => $attribute){
                    if(empty($attribute)){
                        $attribute = DashBoardElementConfiguration::findOrFail($key)->default_value;
                    }
                    $dashBoardElementUser->configurations()->attach($key,['value' => $attribute]);
                }
            }

            /*
            foreach ($entityDashBoardElements as $entityDashBoardElement){
                $dashBoardElements[] = DashboardElement::with(
                    ["currentLanguageTranslation" =>
                         function ($q) use ($request) {
                             $q->where('language_code', '=', $request->header('LANG-CODE'));
                         },
                        "configurations",
                     "configurations.currentLanguageTranslation" =>
                         function ($q) use ($request) {
                             $q->where('language_code', '=', $request->header('LANG-CODE'));
                         },
                    ])->findOrFail($entityDashBoardElement->id);
            }
            */

            $data = DashBoardElementUser::find($dashBoardElementUser->id)->with('configurations')->get();
            return response()->json(['data' => $data], 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    public function updateUserDashBoardElement(Request $request, $id)
    {
        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

            $user = User::whereUserKey($request['userKey'])->firstOrFail();
            $attributes = $request['attributes'];

            // $dashBoarElement = $attributes['dash_board_element'];

            $entityDashBoardElements = EntityDashBoardElement::whereEntityId($entity->id)->get();

            //if(EntityDashBoardElement::whereEntityId($entity->id)->whereDashboardElementId($dashBoarElement)->exists()){

                $dashBoardElementUser = DashBoardElementUser::findOrFail($id);

                unset($attributes['dash_board_element']);

                $dashBoardElementUser->configurations()->detach();
                foreach ($attributes as $key => $attribute){
                    if(empty($attribute)){
                        $attribute = DashBoardElementConfiguration::findOrFail($key)->default_value;
                    }
                    $dashBoardElementUser->configurations()->attach($key,['value' => $attribute]);
                }

                $dashBoardElementUser->save();
            // }


            $data = DashBoardElementUser::find($id)->with('configurations')->get();
            return response()->json(['data' => $data], 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }



    public function unsetUserDashBoardElement(Request $request)
    {
        try {
            $dashBoardElementUser = DashBoardElementUser::whereId($request['id'])->firstOrFail();
            $dashBoardElementUser->delete();
            return response()->json('OK', 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }



    public function reorderUserDashBoardElements(Request $request)
    {
        try {
            $positions = $request->json('positions');

            foreach($positions as $elementPosition=>$elementId){
                $dashBoardElementUser = DashBoardElementUser::find($elementId);

                if (!empty($dashBoardElementUser)) {
                    $dashBoardElementUser->position = $elementId;
                    $dashBoardElementUser->save();
                }
            }

            return response()->json(['data' => "OK"], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

}
