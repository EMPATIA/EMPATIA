<?php

namespace App\Http\Controllers;

use App\CbType;
use App\Entity;
use App\EntityModule;
use App\EntityModuleType;
use App\Module;
use App\ModuleType;
use App\One\One;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Class EntityModulesController
 * @package App\Http\Controllers
 */

/**
 *  @SWG\Definition(
 *      definition="entityModuleErrorDefault",
 *      required={"error"},
 *      @SWG\Property(property="error", type="string", format="string")
 *  )
 *
 * @SWG\Tag(
 *   name="Entity Module",
 *   description="Everything about Entity Modules",
 * )
 *
 *  @SWG\Definition(
 *   definition="entityModuleCreate",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"entity_key", "module_key", "modules"},
 *           @SWG\Property(property="entity_key", format="string", type="string"),
 *           @SWG\Property(property="module_key", format="string", type="string"),
 *           @SWG\Property(
 *              property="modules",
 *              type="array",
 *              @SWG\Items(
 *                  @SWG\Property(property="module_key", format="string", type="string"),
 *                  @SWG\Property(
 *                      property="module_type_keys",
 *                      type="array",
 *                      @SWG\Items(
 *                          @SWG\Property(property="module_type_key", format="string", type="string")
 *                      )
 *                  )
 *              )
 *          )
 *      )
 *   }
 * )
 *
 *  @SWG\Definition(
 *   definition="entityModuleReply",
 *   type="object",
 *   allOf={
 *      @SWG\Schema(
 *           @SWG\Property(property="id", format="integer", type="integer"),
 *           @SWG\Property(property="entity_module_key", format="string", type="string"),
 *           @SWG\Property(property="entity_id", format="integer", type="integer"),
 *           @SWG\Property(property="module_id", format="integer", type="integer"),
 *           @SWG\Property(property="created_at", format="date", type="string"),
 *           @SWG\Property(property="updated_at", format="date", type="string"),
 *           @SWG\Property(property="deleted_at", format="date", type="string")
 *       )
 *   }
 * )
 */

class EntityModulesController extends Controller
{
    protected $keysRequired = [
        'modules'
    ];

    /**
     *
     * @SWG\Post(
     *  path="/entityModule",
     *  summary="Create an Entity Module",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"Entity Module"},
     *
     *  @SWG\Parameter(
     *      name="entityModule",
     *      in="body",
     *      description="Entity Module Data",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/entityModuleCreate")
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
     *      description="the newly created Entity Module",
     *      @SWG\Schema(ref="#/definitions/entityModuleReply")
     *  ),
     *
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/entityModuleErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="Model not found",
     *      @SWG\Schema(ref="#/definitions/entityModuleErrorDefault")
     *   ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store Entity Module",
     *      @SWG\Schema(ref="#/definitions/entityModuleErrorDefault")
     *  )
     * )
     *
     */

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try{
            $entity = Entity::whereEntityKey($request->json('entity_key'))->firstOrFail();
            
            $oldModules = EntityModule::whereEntityId($entity->id)->pluck('module_id');
            $newModules = [];

            $entityModule = new EntityModule();

            foreach ($request->json('modules') as $entityModuleRequest){
                $key = '';
                do {
                    $rand = str_random(32);
                    if (!($exists = EntityModule::whereEntityModuleKey($rand)->exists())) {
                        $key = $rand;
                    }
                } while ($exists);

                $module = Module::whereModuleKey($entityModuleRequest['module_key'])->firstOrFail();
                $newModules[] = $module->id;

                $entityModule = EntityModule::whereModuleId($module->id)->whereEntityId($entity->id)->first();
                if(is_null($entityModule)){
                    $entityModule = EntityModule::create([
                        'entity_module_key' => $key,
                        'entity_id' => $entity->id,
                        'module_id' => $module->id
                    ]);
                }

                $oldTypes = EntityModuleType::whereEntityModuleId($entityModule->id)->pluck('module_type_id');
                $newTypes = [];

                foreach ($entityModuleRequest['module_type_keys'] as $moduleTypeKey) {
                    $moduleType = ModuleType::whereModuleTypeKey($moduleTypeKey)->firstOrFail();
                    $newTypes[] = $moduleType->id;

                    $entityModuleType = EntityModuleType::whereEntityModuleId($entityModule->id)->whereModuleTypeId($moduleType->id)->first();

                    if(is_null($entityModuleType)){
                        $entityModuleType = EntityModuleType::create([
                            'entity_module_id' => $entityModule->id,
                            'module_type_id' => $moduleType->id,
                        ]);
                    }
                }
                $typesToDelete = array_diff($oldTypes->toArray(), $newTypes);

                foreach ($typesToDelete as $typeToDelete){
                    EntityModuleType::whereEntityModuleId($entityModule->id)->whereModuleTypeId($typeToDelete)->delete();
                }
            }

            $modulesToDelete = array_diff($oldModules->toArray(), $newModules);

            foreach ($modulesToDelete as $moduleToDelete){
                EntityModule::whereEntityId($entity->id)->whereModuleId($moduleToDelete)->delete();
            }

            return response()->json($entityModule, 201);
        } catch(Exception $e){
            return response()->json(['error' => 'Failed to store Entity Module'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function setModuleTypeForCurrentEntity(Request $request) {
        try {
            $moduleKey = $request->get("moduleKey");
            $moduleTypeKey = $request->get("moduleTypeKey");

            $entity = ONE::getEntity($request);
            $module = Module::whereModuleKey($moduleKey)->firstOrFail();
            $moduleType = ModuleType::whereModuleTypeKey($moduleTypeKey)->firstOrFail();

            if (!EntityModule::whereEntityId($entity->id)->whereModuleId($module->id)->exists()) {
                $key = '';
                do {
                    $rand = str_random(32);
                    if (!($exists = EntityModule::whereEntityModuleKey($rand)->exists())) {
                        $key = $rand;
                    }
                } while ($exists);

                EntityModule::create([
                    "entity_id" => $entity->id,
                    "module_id" => $module->id,
                    "entity_module_key" => $key
                ]);
            }
            $entityModule = EntityModule::whereEntityId($entity->id)->whereModuleId($module->id)->firstOrFail();


            if (!EntityModuleType::whereEntityModuleId($entityModule->id)->whereModuleTypeId($moduleType->id)->exists()) {
                EntityModuleType::create([
                    "entity_module_id" => $entityModule->id,
                    "module_type_id" => $moduleType->id
                ]);
            }

            return response()->json(["success" => true]);
        } catch (Exception $e) {
            return response()->json(["success" => false, "error" => $e->getMessage()],500);
        }
    }

    /**
     * @param $entityKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveEntityModules($entityKey)
    {
        try{
            $entity = Entity::whereEntityKey($entityKey)->firstOrFail();
            $entityModules = EntityModule::whereEntityId($entity->id)->get();

            $modules = [];
            foreach ($entityModules as $entityModule) {

                $module = $entityModule->module()->first();
                $entityModuleTypes = $entityModule->entityModuleTypes()->get();

                $ModuleTypes = [];

                foreach ($entityModuleTypes as $entityModuleType) {
                    $ModuleTypes[] = $entityModuleType->moduleType()->first();
                }

                $ModuleTypes = collect($ModuleTypes)->keyBy('module_type_key');
                $module['types'] = $ModuleTypes;

                $modules[] = $module;
            }

            $modules = collect($modules)->keyBy('module_key');

            return response()->json(['data' => $modules]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get the Module of the Entity'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSidebarMenu(Request $request)
    {
        try{
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

            $entityModules = EntityModule::whereEntityId($entity->id)->get();

            $modules = [];
            foreach ($entityModules as $entityModule) {

                $module = $entityModule->module()->first();
                $entityModuleTypes = $entityModule->entityModuleTypes()->get();

                $ModuleTypes = [];

                foreach ($entityModuleTypes as $entityModuleType) {
                    $ModuleTypes[] = $entityModuleType->moduleType()->first();
                }

                $ModuleTypes = collect($ModuleTypes)->keyBy('code');
                $module['types'] = $ModuleTypes;

                $modules[] = $module;
            }

            $modules = collect($modules)->keyBy('code');

            return response()->json(['data' => $modules]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to get the Module of the Entity'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
