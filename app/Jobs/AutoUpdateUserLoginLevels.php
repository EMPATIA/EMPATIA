<?php

namespace App\Jobs;

use App\OrchUser;
use App\ParameterUserType;
use App\User;
use App\UserLoginLevel;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AutoUpdateUserLoginLevels implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $entityUsers;
    protected $entity;


    /**
     * Create a new job instance.
     *
     * @param $entity
     * @param $entityUsers
     */
    public function __construct($entity,$entityUsers)
    {
        $this->entityUsers = $entityUsers;
        $this->entity = $entity;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $count = 0;
        if($this->entityUsers){

            foreach ($this->entityUsers as $entityUser){
                $user = OrchUser::whereUserKey($entityUser->user_key)->firstOrFail();
                if($this->autoUpdateUserLoginLevels($user,$this->entity,'SYSTEM')){
                    $count++;
                }
            }
            \Log::info("[LOGIN LEVELS] USERS UPDATED: ".$count);
        }
    }

    public function autoUpdateUserLoginLevels($user,$entity,$responsibleKey)
    {

        try {
            //get user with parameters
            $authUser = User::whereUserKey($user->user_key)->with('userParameters')->first();
            if(empty($authUser)){
                return false;
            }
            $userParameters = $authUser->userParameters;
            $userHasEmailConfirmed = $authUser->confirmed;
            $userHasSmsConfirmed = $authUser->sms_token == null ? true : false;

            //get all entity login levels with parameter user types
            $entityLoginLevels = $entity->loginLevels()->with('parameters')->get();


            $loginLevelsCompleted = [];
            //verify all the entity login levels
            //TODO:improve double foreach
            foreach ($entityLoginLevels as $loginLevel) {

                //dd(empty($loginLevel->manual_verification),$loginLevel);
                $parameterUserTypes = $loginLevel->parameters;
                if (!$parameterUserTypes->isEmpty()) {
                    // verify if user as complete all parameters form login level
                    foreach ($parameterUserTypes as $parameter) {
                        $parameterKey = ParameterUserType::find($parameter->parameter_user_type_id)->parameter_user_type_key;

                        switch ($parameterKey) {
                            case 'name':
                            case 'surname':
                            case 'email':
                                if (empty($authUser->{$parameter->parameter_user_type_key})) {
                                    //exit from switch and foreach of parameter user types
                                    break 2;
                                }
                                break;
                            default:
                                $checkRelation = $userParameters->where('parameter_user_key', '=', $parameterKey)->first();
                                if (empty($checkRelation) || empty($checkRelation->value)) {
                                    break 2;
                                }
                                break;
                        }
                        // verify if it's the last element and update the user login level
                        if ($parameter === $parameterUserTypes->last()) {

                            if ((empty($loginLevel->email_verification) || $userHasEmailConfirmed) && (empty($loginLevel->sms_verification) || $userHasSmsConfirmed)){
                                $loginLevelsCompleted[] = $loginLevel->id;
                            }

//                            if (empty($loginLevel->email_verification)) {
//                                $loginLevelsCompleted[] = $loginLevel->id;
//                            } else {
//                                if ($userHasEmailConfirmed) {
//                                    $loginLevelsCompleted[] = $loginLevel->id;
//                                }
//                            }

                        }
                    }
                } elseif (empty($loginLevel->manual_verification) && (empty($loginLevel->email_verification) || $userHasEmailConfirmed) && (empty($loginLevel->sms_verification) || $userHasSmsConfirmed)) {

                    $loginLevelsCompleted[] = $loginLevel->id;

//                    if (empty($loginLevel->email_verification)) {
//                        $loginLevelsCompleted[] = $loginLevel->id;
//                    } else {
//                        if ($userHasEmailConfirmed) {
//                            $loginLevelsCompleted[] = $loginLevel->id;
//                        }
//                    }
                }

            }

            $oldUserLoginLevels = UserLoginLevel::whereUserId($user->id)->whereManual('0')->get();
            /*dd($oldUserLoginLevels);*/

            //verify old login levels if they are complete
            foreach ($oldUserLoginLevels as $oldUserLoginLevel) {
                $loginLevelOld = $oldUserLoginLevel->loginLevel()->first();

                if (!in_array($oldUserLoginLevel->login_level_id, $loginLevelsCompleted) && empty($loginLevelOld->manual_verification) && empty($loginLevelOld->sms_verification)) {
                    $oldUserLoginLevel->updated_by = $responsibleKey;
                    $oldUserLoginLevel->save();
                    $oldUserLoginLevel->delete();
                    continue;
                }
                $dependencies = $loginLevelOld->loginLevelDependencies()->pluck('dependency_login_level_id');
                if ($dependencies->isEmpty()) {
                    continue;
                }
                //intersect the dependencies with login levels completed to verify if all the levels are completed
                $intersectionArray = $dependencies->intersect($loginLevelsCompleted);
                if (count($intersectionArray) != count($dependencies)) {
                    $oldUserLoginLevel->updated_by = $responsibleKey;
                    $oldUserLoginLevel->save();
                    $oldUserLoginLevel->delete();


                }
            }

            //verify new login levels dependencies of completed levels
            foreach ($entityLoginLevels as $loginLevel) {
                $userAsLevel = UserLoginLevel::whereLoginLevelId($loginLevel->id)->whereUserId($user->id)->exists();
                if (in_array($loginLevel->id, $loginLevelsCompleted) && empty($userAsLevel)) {
                    $dependencies = $loginLevel->loginLevelDependencies()->pluck('dependency_login_level_id');
                    if ($dependencies->isEmpty()) {

                        UserLoginLevel::create([
                            'user_id'        => $user->id,
                            'login_level_id' => $loginLevel->id,
                            'created_by'     => $responsibleKey,
                            'updated_by'     => $responsibleKey,
                        ]);
                    } else {
                        //intersect the dependencies with login levels completed to verify if all the levels are completed
                        $intersectionArray = $dependencies->intersect($loginLevelsCompleted);
                        if (count($intersectionArray) == count($dependencies)) {
                            UserLoginLevel::create([
                                'user_id'        => $user->id,
                                'login_level_id' => $loginLevel->id,
                                'created_by'     => $responsibleKey,
                                'updated_by'     => $responsibleKey,
                            ]);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}
