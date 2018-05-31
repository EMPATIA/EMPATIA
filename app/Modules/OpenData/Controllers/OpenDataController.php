<?php

namespace App\Modules\OpenData\Controllers;

use Exception;
use App\One\One;
use App\ComModules\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Modules\OpenData\Models\OpenDataEntity;
use App\Entity;

class OpenDataController extends Controller {
    public function index() {
        try {
            $openDataEntity = OpenDataEntity::with("entity","creator")->get();

            return response()->json($openDataEntity);
        } catch(Exception $e) {
            return response()->json(["error" => $e->getMessage()],500);
        }
    }

    public function show(Request $request, $entityKey) {
        try {
            $entity = Entity::whereEntityKey($entityKey)->firstOrFail();

            $language = $request->header('LANG-CODE');
            $defaultLanguage = $request->header('LANG-CODE-DEFAULT');

            $parameterUserTypes = $entity->parameterUserTypes()
                ->with([
                    "parameterType",
                    "parameterUserOptions" => function($q){
                        $q->withCount("userParameters");
                    }
                ])
                ->get();

            foreach ($parameterUserTypes as $parameterUserType) {
                $parameterUserType->newTranslation($language,$defaultLanguage);
            }

            $cbs = $entity->entityCbs()
                ->with([
                    "cb.parameters" => function($q) {
                        $q->withCount("options");
                    },
                    "cb.votes"
                ])
                ->get()
                ->pluck("cb");

            foreach ($cbs as $cb) {
                foreach ($cb->parameters as $parameter) {
                    $parameter->newTranslation($language,$defaultLanguage);
                }
            }

            $openData = $entity->openData()
                ->with("creator","userParameters","cbParameters.parameter","voteEvents.vote")
                ->first();
            
            if(!empty($openData))
                $openData->last_export_date = !empty($openData->currentExport->created_at) ? $openData->currentExport->created_at->toDateTimeString() : null;
            
            $dataToReturn = array(
                "entity"             => $entity,
                "openData"           => $openData,
                "parameterUserTypes" => $parameterUserTypes,
                "cbs"                => $cbs
            );

            return response()->json($dataToReturn);
        } catch(Exception $e) {
            return response()->json(["error" => $e->getMessage()],500);
        }
    }

    public function update(Request $request, $entityKey) {
        $userKey = ONE::verifyToken($request);
        try {
            $entity = Entity::whereEntityKey($entityKey)->firstOrFail();
            
            $entity->openData()->delete();
        
            DB::beginTransaction();
            
            $openDataToken = null;
            do {
                $rand = str_random(32);

                if (!($exists = OpenDataEntity::whereToken($rand)->exists())) {
                    $openDataToken = $rand;
                }
            } while ($exists);

            OpenDataEntity::create([
                "created_by" => $userKey,
                "entity_key" => $entity->entity_key,
                "token"      => $openDataToken
            ]);
            $entityOpenData = $entity->openData()->first();

            /* Save User Parameters */
            $requestedUserParameters = $request->get("user_parameters");

            $entityParameterUserTypes = $entity->parameterUserTypes()
                ->with([
                    "parameterUserOptions" => function($q) {
                        $q->withCount("userParameters");
                    }
                ])
                ->whereIn("parameter_user_type_key",$requestedUserParameters)
                ->where("anonymizable","!=","1")
                ->get();

            foreach ($entityParameterUserTypes as $userParameter) {
                $canSave = true;
                if ($userParameter->minimum_users>0) {
                    foreach ($userParameter->parameterUserOptions as $option) {
                        if ($option->user_parameters_count < $userParameter->minimum_users) {
                            $canSave = false;
                            break;
                        }
                    }
                }
                
                if ($canSave) {
                    $entityOpenData->userParameters()->create([
                        "parameter_user_type_key" => $userParameter->parameter_user_type_key,
                        "created_by" => $userKey
                    ]);
                }
            }

            
            /* Save Cb Parameters and Vote Events */
            $requestedCbsData = $request->get("cbs");
            $requestedCbs = array_keys($requestedCbsData);
            $entityCbs = $entity->entityCbs()
                ->whereIn("cb_key",$requestedCbs)
                ->with("cb.parameters","cb.votes")
                ->get();
            
            foreach ($entityCbs as $cb) {
                $cb = $cb->cb;

                /* Save Cb Parameters */
                if (!empty($requestedCbsData[$cb->cb_key]["parameters"])) {
                    foreach ($requestedCbsData[$cb->cb_key]["parameters"] as $cbParameterId) {
                        $cbParameter = $cb->parameters->where("id","=",$cbParameterId)->first();
                        if (!empty($cbParameter)) {
                            $entityOpenData->cbParameters()->create([
                                "parameter_id" => $cbParameter->id,
                                "created_by" => $userKey
                            ]);
                        }
                    }
                }

                if (!empty($requestedCbsData[$cb->cb_key]["votes"])) {
                    foreach ($requestedCbsData[$cb->cb_key]["votes"] as $cbVoteKey) {
                        $cbVote = $cb->votes->where("vote_key","=",$cbVoteKey)->first();
                        if (!empty($cbVote)) {
                            $entityOpenData->voteEvents()->create([
                                "vote_event_key" => $cbVote->vote_key,
                                "created_by"     => $userKey
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            
            return response()->json(["success" => true]);
        } catch(Exception $e) {
            return response()->json(["error" => $e->getMessage()],500);
        }
    }

    public function export(Request $request, $token) {
        try {
            $currentExport = OpenDataEntity::whereToken($token)
                ->firstOrFail()
                ->currentExport()
                ->firstOrFail();

            return response()->json(json_decode($currentExport->export));
        } catch(Exception $e) {
            return response()->json(["error" => $e->getMessage()],500);
        }
    }

    public static function exportToDb() {
        function obfuscateString($string) {
            return md5($string);
        }
        try {
            $openDatas = OpenDataEntity::get();

            foreach($openDatas as $openData) {
                try {
                    $exportStartTime = microtime(true);
                    /* Get User Parameters */
                    $openData->load([
                        "userParameters.parameterUserType.parameterUserTypeTranslations",
                        "userParameters.parameterUserType.parameterType",
                        "userParameters.parameterUserType.parameterUserOptions.parameterUserOptionTranslations"
                    ]);

                    $userParameters = array();
                    foreach ($openData->userParameters as $openDataUserParameterType) {
                        if (!empty($openDataUserParameterType->parameterUserType)) {
                            $userParameterType = $openDataUserParameterType->parameterUserType;

                            $currentUserParameter = array(
                                "id"           => obfuscateString($userParameterType->parameter_user_type_key),
                                "type"         => array(
                                    "id"       => $userParameterType->parameterType->code,
                                    "title"    => $userParameterType->parameterType->name
                                ),
                                "translations" => array(),
                                "options"      => array()
                            );
                            foreach ($userParameterType->parameterUserTypeTranslations as $userParameterTranslation) {
                                $currentUserParameter["translations"][] = array(
                                    "language_code" => $userParameterTranslation->language_code,
                                    "name"          => $userParameterTranslation->name,
                                    "description"   => $userParameterTranslation->description,
                                );
                            }
                            foreach ($userParameterType->parameterUserOptions as $userParameterOption) {
                                $currentOption = array(
                                    "id"           => obfuscateString($userParameterOption->parameter_user_option_key),
                                    "translations" => array()
                                );

                                foreach ($userParameterOption->parameterUserOptionTranslations as $userParameterOptionTranslation) {
                                    $currentOption["translations"][] = array(
                                        "language_code" => $userParameterOptionTranslation->language_code,
                                        "name"          => $userParameterOptionTranslation->name
                                    );
                                }

                                $currentUserParameter["options"][] = $currentOption;
                            }

                            $userParameters[$currentUserParameter["id"]] = $currentUserParameter;
                        }
                    }
                    unset($openDataUserParameterType, $userParameterType, $currentUserParameter, $userParameterTranslation, 
                        $userParameterOption, $currentOption, $userParameterOptionTranslation);
                    
                    /* Get Users and respective Parameters */
                    $users = array();
                    if (count($userParameters)>0) {
                        $openData->load([
                            "entity.users.user.userParameters" => function($q) use ($openData) {
                                $q->whereIn("parameter_user_key",$openData->userParameters->pluck("parameter_user_type_key"));
                            }
                        ]);

                        foreach ($openData->entity->users as $user) {
                            if (!empty($user->user)) {
                                $currentUser = array(
                                    "id"         => obfuscateString($user->user_key),
                                    "parameters" => array()
                                );

                                $currentUserParameters = array();
                                foreach ($user->user->userParameters as $userParameter) {
                                    $currentUserParameters[obfuscateString($userParameter->parameter_user_key)][] = $userParameter->value;
                                }

                                foreach ($currentUserParameters as $currentUserParameterKey => $currentUserParameter) {
                                    $currentUser["parameters"][] = array(
                                        "id"     => $currentUserParameterKey,
                                        "values" => $currentUserParameter
                                    );
                                }
                                $users[$currentUser["id"]] = $currentUser;
                            }
                        }

                        unset($openData->entity);
                    }
                    unset($openData->userParameters, $currentUserParameter, $user, $currentUser, $currentUserParameters, $userParameter, $currentUserParameterKey);


                    /* Get Cbs, topics and Parameters */
                    $openData->load([
                        "cbParameters.parameter.type",
                        "cbParameters.parameter.parameterTranslations",
                        "cbParameters.parameter.options.parameterOptionTranslations",
                        "cbParameters.parameter.cbs.topics.topicVersions" => function ($q) use ($openData) {
                            $q
                                ->where("active","=","1")
                                ->with([
                                    "topicParameters" => function($q) use ($openData) {
                                        $q->whereIn("id",$openData->cbParameters->pluck("parameter_id"));
                                    }
                                ]);
                        }
                    ]);

                    $cbs = array();
                    foreach ($openData->cbParameters as $cbParameter) {
                        if (!empty($cbParameter->parameter)) {
                            /* Get Parameter */
                            $parameter = $cbParameter->parameter;
                            
                            $cbId = obfuscateString($parameter->cbs->cb_key);

                            $currentCBParameter = array(
                                "id"           => obfuscateString($parameter->id),
                                "type"         => array(
                                    "id"       => $parameter->type->code,
                                    "title"    => $parameter->type->name
                                ),
                                "translations" => array(),
                                "options"      => array()
                            );

                            foreach ($parameter->parameterTranslations as $parameterTranslation) {
                                $currentCBParameter["translations"][] = array(
                                    "language_code" => $parameterTranslation->language_code,
                                    "name"          => $parameterTranslation->parameter,
                                    "description"   => $parameterTranslation->description,
                                );
                            }
                            foreach ($parameter->options as $parameterOption) {
                                $currentOption = array(
                                    "id"           => obfuscateString($parameterOption->id),
                                    "translations" => array()
                                );
                                
                                foreach ($parameterOption->parameterOptionTranslations as $parameterOptionTranslation) {
                                    $currentOption["translations"][] = array(
                                        "language_code" => $parameterOptionTranslation->language_code,
                                        "name"          => $parameterOptionTranslation->label
                                    );
                                }

                                $currentCBParameter["options"][] = $currentOption;
                            }

                            /* Get CB */
                            if (empty($cbs[$cbId])) {
                                $cbs[$cbId] = array(
                                    "id"          => $cbId,
                                    "name"        => $parameter->cbs->title,
                                    "description" => $parameter->cbs->contents,
                                    "start"       => $parameter->cbs->start_date,
                                    "end"         => $parameter->cbs->end_date,
                                    "parameters"  => array(),
                                    "topics"      => array(),
                                    "voteEvents"  => array()
                                );
                            }
                            $cbs[$cbId]["parameters"][] = $currentCBParameter;

                            /* Get Topics */
                            foreach ($parameter->cbs->topics as $topic) {
                                if ($topic->topicVersions->count()==1 && empty($cbs[$cbId]["topics"][obfuscateString($topic->topic_key)])) {
                                    $topicVersion = $topic->topicVersions->first();
                                    
                                    $currentTopic = array(
                                        "id"          => obfuscateString($topic->topic_key),
                                        "title"       => $topicVersion->title,
                                        "description" => $topicVersion->contents,
                                        "parameters"  => array()
                                    );
                                    

                                    $currentTopicParameters = array();
                                    foreach ($topicVersion->topicParametersPivot as $topicParameter) {
                                        $currentTopicParameters[obfuscateString($topicParameter->id)][] = $topicParameter->pivot->value;
                                    }

                                    foreach ($currentTopicParameters as $currentTopicParameterKey => $currentTopicParameter) {
                                        $currentTopic["parameters"][] = array(
                                            "id"     => $currentTopicParameterKey,
                                            "values" => $currentTopicParameter
                                        );
                                    }

                                    $cbs[$cbId]["topics"][$currentTopic["id"]] = $currentTopic;
                                }
                            }
                        }
                    }
                    unset($openData->cbParameters, $currentOption, $cbParameter, $parameter, $cbId, $currentCBParameter, $parameterTranslation, $parameterOption, 
                        $parameterOptionTranslation, $topic, $topicVersion, $currentTopic, $currentTopicParameters, $topicParameter, $currentTopicParameter, 
                        $currentTopicParameterKey);

                    /* Get Vote Events */
                    $openData->load([
                        "voteEvents.vote.cb"
                    ]);

                    foreach ($openData->voteEvents as $voteEvent) {
                        if (!empty($voteEvent->vote)) {
                            $cbVote = $voteEvent->vote;
                            
                            $cbId = obfuscateString($cbVote->cb->cb_key);

                            $currentVoteEvent = array(
                                "id"    => obfuscateString($cbVote->vote_key),

                                "name"  => $cbVote->name,
                                "votes" => array()
                            );

                            $voteEventData = Vote::getEventAndVotes($cbVote->vote_key);
                            
                            foreach($voteEventData->votes??[] as $vote) {
                                $currentVoteEvent["votes"][] = array(
                                    "user_id"  => obfuscateString($vote->user_key),
                                    "topic_id" => obfuscateString($vote->vote_key),
                                    "value"    => $vote->value,
                                    "submited" => $vote->submitted
                                );
                            }

                            /* Get CB */
                            if (empty($cbs[$cbId])) {
                                $cbs[$cbId] = array(
                                    "id"          => $cbId,
                                    "name"        => $parameter->cbs->title,
                                    "description" => $parameter->cbs->contents,
                                    "start"       => $parameter->cbs->start_date,
                                    "end"         => $parameter->cbs->end_date,
                                    "parameters"  => array(),
                                    "topics"      => array(),
                                    "voteEvents"  => array()
                                );
                            }
                            
                            $cbs[$cbId]["voteEvents"][$currentVoteEvent["id"]] = $currentVoteEvent;
                        }
                    }
                    unset($openData->voteEvents, $cbId, $voteEvent, $cbVote, $currentVoteEvent, $voteEventData, $vote);
                    
                    foreach ($cbs as $key => $cb) {
                        $cbs[$key]["parameters"] = array_values($cb["parameters"]);
                        $cbs[$key]["topics"] = array_values($cb["topics"]);
                        $cbs[$key]["voteEvents"] = array_values($cb["voteEvents"]);
                    }
                    unset($cb, $key);

                    $openDataExport = array(
                        "userParameters" => array_values($userParameters),
                        "users"          => array_values($users),
                        "cbs"            => array_values($cbs),
                        "export_date"    => $exportStartTime
                    );

                    $openData->exports()->create([
                        "export" => json_encode($openDataExport)
                    ]);

                    unset($exportStartTime, $userParameters, $users, $cbs, $cb, $openDataExport);
                } catch(Exception $e) {}
            }
        } catch(Exception $e) {
            return response()->json(["error" => $e->getMessage()],500);
        }
    }
}
