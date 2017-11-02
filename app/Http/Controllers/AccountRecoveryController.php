<?php

namespace App\Http\Controllers;

use App\AccountRecoveryParameter;
use App\AccountRecoveryToken;
use App\Entity;
use App\One\One;
use App\User;
use App\UserParameter;
use Carbon\Carbon;
use Exception;
use function foo\func;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;


class AccountRecoveryController extends Controller {

    public function index(Request $request) {
        try{
            $entityKey = $request->header('X-ENTITY-KEY','');

            $entity = Entity::with("accountRecoveryParameters.parameterUserType")->whereEntityKey($entityKey)->firstOrFail();

            foreach ($entity->accountRecoveryParameters as $accountRecoveryParameter) {
                if ($accountRecoveryParameter->parameter_user_type_key!="email")
                    $accountRecoveryParameter->parameterUserType->newTranslation($request->header('LANG-CODE'), $request->header('LANG-CODE-DEFAULT'));
            }

            return response()->json($entity->accountRecoveryParameters, 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Account Recovery Parameters list'], 500);
        }
    }

    public function store(Request $request) {
        $entityKey = $request->header('X-ENTITY-KEY','');
        try{
            $entity = Entity::whereEntityKey($entityKey)->firstOrFail();

            if ($request->json("send_token",0)!=0)
                AccountRecoveryParameter::whereEntityKey($entityKey)->update(["send_token"=>0]);

            do {
                $rand = str_random(32);

                if (!($exists = AccountRecoveryParameter::whereTableKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $newAccountRecoveryParameter = $entity->accountRecoveryParameters()->create([
                "table_key"                 => $key,
                "parameter_user_type_key"   => $request->json("parameter_user_type_key",""),
                "send_token"                => $request->json("send_token",0)
            ]);

            return response()->json($newAccountRecoveryParameter, 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Object not Found'], 404);
        }catch(QueryException $e){
            return response()->json(['error' => 'Failed to create Account Recovery Parameter'], 500);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to store Account Recovery Parameter'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function show(Request $request, $accountRecoveryParameterKey) {
        $entityKey = $request->header('X-ENTITY-KEY','');
        try {
            $accountRecoveryParameter = AccountRecoveryParameter::with("parameterUserType")->whereTableKey($accountRecoveryParameterKey)->whereEntityKey($entityKey)->firstOrFail();
            if ($accountRecoveryParameter->parameter_user_type_key!="email")
                $accountRecoveryParameter->parameterUserType->newTranslation($request->header('LANG-CODE'), $request->header('LANG-CODE-DEFAULT'));

            return response()->json($accountRecoveryParameter, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Account Recovery Parameter not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Account Recovery Parameter'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function update(Request $request, $accountRecoveryParameterKey) {
        $entityKey = $request->header('X-ENTITY-KEY','');
        try{
            if ($request->json("send_token",0)!=0)
                AccountRecoveryParameter::whereEntityKey($entityKey)->update(["send_token"=>0]);

            $accountRecoveryParameter = AccountRecoveryParameter::whereTableKey($accountRecoveryParameterKey)->whereEntityKey($entityKey)->firstOrFail();
            $accountRecoveryParameter->send_token = $request->json("send_token",0);
            $accountRecoveryParameter->save();

            return response()->json($accountRecoveryParameter, 201);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Object not Found'], 404);
        }catch(QueryException $e){
            return response()->json(['error' => 'Failed to update Account Recovery Parameter'], 500);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to update Account Recovery Parameter'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function destroy(Request $request, $accountRecoveryParameterKey) {
        try{
            $entityKey = $request->header('X-ENTITY-KEY','');
            AccountRecoveryParameter::whereTableKey($accountRecoveryParameterKey)->whereEntityKey($entityKey)->delete();
            return response()->json('Ok', 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to delete Account Recovery Parameter'], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Account Recovery Parameter not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized'], 401);

    }


    public function getParametersForForm(Request $request) {
        try{
            $entityKey = $request->header('X-ENTITY-KEY','');
            $entity = Entity::with([
                "accountRecoveryParameters.parameterUserType.parameterType",
                "accountRecoveryParameters.parameterUserType.parameterUserOptions"
            ])->whereEntityKey($entityKey)->firstOrFail();

            foreach ($entity->accountRecoveryParameters as $accountRecoveryParameter) {
                if ($accountRecoveryParameter->parameter_user_type_key!="email") {
                    $accountRecoveryParameter->parameterUserType->newTranslation($request->header('LANG-CODE'), $request->header('LANG-CODE-DEFAULT'));
                    unset($accountRecoveryParameter->parameterUserType->parameterUserTypeTranslations);

                    foreach ($accountRecoveryParameter->parameterUserType->parameterUserOptions as $parameterUserOption) {
                        $parameterUserOption->newTranslation($request->header('LANG-CODE'), $request->header('LANG-CODE-DEFAULT'));
                    }
                }
            }

            return response()->json($entity->accountRecoveryParameters, 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve the Account Recovery Parameter'], 500);
        }
    }
    public function validateRecoveryRequest(Request $request) {
        try{
            $entityKey = $request->header('X-ENTITY-KEY','');
            $entity = Entity::with([
                "accountRecoveryParameters.parameterUserType.parameterType"
            ])->whereEntityKey($entityKey)->firstOrFail();

            $validationParameter = null;

            if ($entity->accountRecoveryParameters->where("parameter_user_type_key","email")->count()){
                //É utilizado o email.
                $userEmail = $request->json("email","");

                $user = User::with('userParameters')
                    ->whereHas("orchUser.entities",function ($q) use ($entityKey) {
                            $q->where("entity_key","=",$entityKey);
                    })->whereEmail($userEmail)->firstOrFail();

                foreach ($entity->accountRecoveryParameters as $accountRecoveryParameter) {
                    if ($accountRecoveryParameter->parameter_user_type_key!="email" && !empty($request->json($accountRecoveryParameter->parameter_user_type_key,""))) {
                        $parameter = $user->userParameters->where("parameter_user_key",$accountRecoveryParameter->parameter_user_type_key)->first();
                        if (empty($parameter) || $parameter->value!=$request->json($accountRecoveryParameter->parameter_user_type_key,""))
                            return response()->json(['error' => 'No account match'], 400);
                    }

                    if ($accountRecoveryParameter->send_token=="1") {
                        if ($accountRecoveryParameter->parameter_user_type_key=="email")
                            $validationParameter["parameter"] = "email";
                        else
                            $validationParameter["parameter"] = $accountRecoveryParameter->parameterUserType->parameterType->code ?? null;

                        $validationParameter["value"] = $request->json($accountRecoveryParameter->parameter_user_type_key,"");
                        $validationParameter["token"] = mt_rand(10000,99999);
                    }
                }
            } else {
                $firstParameter = $entity->accountRecoveryParameters->first();
                $userIds = UserParameter::whereParameterUserKey($firstParameter->parameter_user_type_key)->whereValue($request->json($firstParameter->parameter_user_type_key,""))->get()->pluck("user_id");
                $users = User::with("userParameters")->whereIn("id",$userIds)->get();
                foreach ($entity->accountRecoveryParameters as $accountRecoveryParameter) {
                    if ($accountRecoveryParameter!=$firstParameter) {
                        $currentParameterValue = $request->json($accountRecoveryParameter->parameter_user_type_key,"");
                        $currentParameterKey = $accountRecoveryParameter->parameter_user_type_key;
                        foreach ($users as $collectionKey => $user) {
                            if ($user->userParameters->where("parameter_user_key",$currentParameterKey)->where("value",$currentParameterValue)->count()==0)
                                $users->forget($collectionKey);
                        }

                        if ($users->count()<1)
                            return response()->json(['error' => 'No account match'], 400);

                        if ($accountRecoveryParameter->send_token=="1") {
                            $validationParameter["parameter"] = $accountRecoveryParameter->parameterUserType->parameterType->code ?? null;
                            $validationParameter["value"] = $currentParameterValue;
                            $validationParameter["token"] = mt_rand(10000,99999);
                        }
                    }
                }

            }

            if ($users->count()==1) {
                $user = $users->first();
                if (!empty($validationParameter) && !empty($validationParameter["parameter"]) && !empty($validationParameter["value"]) && !empty($validationParameter["token"])) {
                    $user->accountRecoveryTokens()->create([
                        "token" => $validationParameter["token"]
                    ]);
                }

                return response()->json(["user" => $user, "parameters" => $entity->accountRecoveryParameters, "validation" => $validationParameter], 200);
            } else
                return response()->json(['error' => 'No account match'], 400);
        } catch(Exception $e) {
            return response()->json(['error' => 'Failed to recover account'], 500);
        }
    }
    public function recoverAccount(Request $request) {
        try {
            $query = User::whereUserKey($request->json("userKey",""));

            if ($request->json("hasCode",false) && !empty($request->json("code",""))) {
                $userToken = $request->json("code","");

                $query = $query->with(['accountRecoveryTokens' => function($q) use ($userToken) {
                    $q
                        ->where("token","=",$userToken)
                        ->where("consumed","!=",1)
                        ->where("created_at",">=",Carbon::now()->subHour(1));
                }])->whereHas('accountRecoveryTokens',function($q) use ($userToken) {
                    $q
                        ->where("token","=",$userToken)
                        ->where("consumed","!=",1)
                        ->where("created_at",">=",Carbon::now()->subHour(1));
                });
            }

            $user = $query->firstOrFail();

            if ($user->accountRecoveryTokens->count()>0) {
                $user->accountRecoveryTokens->first()->consumed = 1;
                $user->accountRecoveryTokens->first()->save();
            }

            $user->email = $request->json("email","");
            $user->password = bcrypt($request->json("password",""));

            /* Restart email confirmation */
            $user->confirmed = 0;
            $user->confirmation_code = str_random(64);
            $user->save();

            return response()->json($user, 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to recover account'], 500);
        }
    }
    public function isActive(Request $request) {
        try{
            $entityKey = $request->header('X-ENTITY-KEY','');
            $entity = Entity::with("accountRecoveryParameters")->whereHas("accountRecoveryParameters")->whereEntityKey($entityKey)->first();

            return response()->json(!empty($entity), 200);
        }catch(Exception $e){
            return response()->json(false,500);
        }
    }
}
