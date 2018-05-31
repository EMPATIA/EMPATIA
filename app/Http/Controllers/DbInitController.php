<?php

namespace App\Http\Controllers;

use App\Cb;
use App\ComModules\Vote;
use App\Post;
use App\Topic;
use Exception;
use Foo\CBar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DbInitController extends Controller
{
    /**
     *
     */
    public function manualUpdateTopicParametersCache(){
        try {
            $topics = Topic::all();

            foreach ($topics as $topic) {

                $versions = $topic->topicVersions()->get()->unique("version")->map(function ($item, $key) {
                    return [
                        "version" => $item->version,
                        "active" => $item->active,
                        "created_at" => $item->created_at
                    ];
                });

                if (!$versions->isEmpty()) {

                    if ($topic->topicVersions()->whereActive(1)->count() == 1) {
                        $lastVersion = $topic->topicVersions()->whereActive(1)->firstOrFail();
                    } else {
                        $lastVersion = $topic->topicVersions()->orderBy("version", "desc")->firstOrFail();
                    }

                    //Get Topic Cached Data
                    $cachedData = empty($topic->_cached_data) ? json_decode(json_encode(array('parameters'=>[]), JSON_FORCE_OBJECT)) : json_decode($topic->_cached_data);

                    $topic->title = $lastVersion->title;
                    $topic->summary = $lastVersion->summary;
                    $topic->contents = $lastVersion->contents;
                    $topic->description = $lastVersion->description;
                    $topic->active = $lastVersion->active;
                    $topic->version = $lastVersion->version;

                    $parameters = $lastVersion->topicParameters()->get();
                    $parametersCache = [];

                    foreach ($parameters as $parameter) {
                        $parameterTranslations = [];

                        $topicParameter = $topic->parameters()
                            ->wherePivot("topic_version_id","=",$lastVersion->id)
                            ->with(['type', 'parameterTranslations', 'options.parameterOptionTranslations', 'options.parameterOptionFields'])->find($parameter->parameter_id);

                        if (isset($topicParameter->parameterTranslations)){
                            foreach ($topicParameter->parameterTranslations as $parameterTranslation) {
                                $parameterTranslations[$parameterTranslation->language_code] = array('parameter' => $parameterTranslation->parameter, 'description' => $parameterTranslation->description);
                            }
                        }

                        unset($topicParameter->parameterTranslations);

                        if(!empty($topicParameter)) {
                            $parametersCache[$parameter->parameter_id] = $topicParameter;
                        }

                        if(!empty($parameterTranslations)){
                            $parametersCache[$parameter->parameter_id]['translations'] = $parameterTranslations;
                        }

                        if (isset($parametersCache[$parameter->parameter_id]->options)){
                            foreach ($parametersCache[$parameter->parameter_id]->options as $parameterOption) {

                                $optionTranslations = [];
                                $parameterOptionFields = [];

                                foreach ($parameterOption->parameterOptionTranslations as $parameterOptionTranslation) {
                                    $optionTranslations[$parameterOptionTranslation->language_code] = array('label' => $parameterOptionTranslation->label);
                                }

                                $parameterOption->translations = $optionTranslations;
                                unset($parameterOption->parameterOptionTranslations);

                                foreach ($parameterOption->parameterOptionFields as $parameterOptionField) {
                                    $parameterOptionFields[] = array($parameterOptionField->code => $parameterOptionField->value);
                                }

                                $parameterOption->fields = $parameterOptionFields;
                                unset($parameterOption->parameterOptionFields);
                            }
                        }
                    }

                    //Cache Parameters
                    $cachedData->parameters = json_decode(json_encode(array_values($parametersCache)));

                    $following = false;
                    if (!empty($userKey)) {
                        $following = $topic->followers()->whereUserKey($userKey)->exists();
                    }

                    $cachedData->following = $following;

                    $topic->_cached_data = json_encode($cachedData);
                    $topic->save();

                } else {

                    $parametersCache = [];

                    //Get Topic Cached Data
                    $cachedData = empty($topic->_cached_data) ? json_decode(json_encode(array('parameters'=>[]), JSON_FORCE_OBJECT)) : json_decode($topic->_cached_data);
                    $parameters = $topic->parameters()->with(['type', 'parameterTranslations', 'options.parameterOptionTranslations', 'options.parameterOptionFields'])->get();

                    foreach ($parameters as $parameter) {
                        $parameterTranslations = [];

                        if (isset($parameter->parameterTranslations)){
                            foreach ($parameter->parameterTranslations as $parameterTranslation) {
                                $parameterTranslations[$parameterTranslation->language_code] = array('parameter' => $parameterTranslation->parameter, 'description' => $parameterTranslation->description);
                            }
                        }

                        unset($parameter->parameterTranslations);

                        if(!empty($parameter)) {
                            $parametersCache[$parameter->id] = $parameter;
                        }

                        if(!empty($parameterTranslations)) {
                            $parametersCache[$parameter->id]['translations'] = $parameterTranslations;
                        }


                        if (isset($parametersCache[$parameter->id]->options)){
                            foreach ($parametersCache[$parameter->id]->options as $parameterOption) {

                                $optionTranslations = [];
                                $parameterOptionFields = [];

                                foreach ($parameterOption->parameterOptionTranslations as $parameterOptionTranslation) {
                                    $optionTranslations[$parameterOptionTranslation->language_code] = array('label' => $parameterOptionTranslation->label);
                                }

                                $parameterOption->translations = $optionTranslations;
                                unset($parameterOption->parameterOptionTranslations);

                                foreach ($parameterOption->parameterOptionFields as $parameterOptionField) {
                                    $parameterOptionFields[] = array($parameterOptionField->code => $parameterOptionField->value);
                                }

                                $parameterOption->fields = $parameterOptionFields;
                                unset($parameterOption->parameterOptionFields);
                            }
                        }
                    }

                    //Cache Parameters
                    $cachedData->parameters = json_decode(json_encode(array_values($parametersCache)));

                    $following = false;
                    if (!empty($userKey)) {
                        $following = $topic->followers()->whereUserKey($userKey)->exists();
                    }

                    $cachedData->following = $following;

                    $topic->_cached_data = json_encode($cachedData);
                    $topic->save();
                }
            }
        }catch(Exception $e){
            dd($e->getMessage(), $e->getLine());
        }
    }

    /**
     *
     */
    public function manualUpdateTopicCommentsCount(){
        try {
            \DB::unprepared('UPDATE `posts` SET `deleted_at` = NULL WHERE `deleted_at` IS NULL');
        }catch(Exception $e){
            dd($e->getMessage(), $e->getLine());
        }
    }

    /**
     *
     */
    public function manualUpdateVoteStatistics(){
        try {
            $cbs = Cb::all();

            foreach ($cbs as $cb) {
                $voteEvents = $cb->votes()->get();

                if ($voteEvents->isNotEmpty()) {

                    foreach ($voteEvents as $voteEvent) {
                        $eventKey = $voteEvent->vote_key;

                        try {
                            $event = Vote::getVoteEvent($eventKey);
                            $voteCount = json_decode($event->_count_votes);

                            if ($voteCount) {
                                $totalVotes = $voteCount->count->total;
                                $totalUsers = $voteCount->count->total_users ?? 0;

                                $voteStatistics = json_decode($cb->_vote_statistics);

                                if (!isset($voteStatistics->votes_by_event)) {
                                    if (is_null($voteStatistics)) {
                                        $voteStatistics['votes_by_event'] = collect([$eventKey => $totalVotes]);
                                        $cb->_vote_statistics = json_encode($voteStatistics);
                                    } else {
                                        $voteStatistics->votes_by_event = collect([$eventKey => $totalVotes]);
                                        $cb->_vote_statistics = json_encode($voteStatistics);
                                    }
                                } else {
                                    $voteStatistics->votes_by_event->{$eventKey} = $totalVotes;
                                    $cb->_vote_statistics = json_encode($voteStatistics);
                                }

                                if (!isset($voteStatistics->voters_by_event)) {
                                    if (is_null($voteStatistics)) {
                                        $voteStatistics['voters_by_event'] = collect([$eventKey => $totalUsers]);
                                        $cb->_vote_statistics = json_encode($voteStatistics);
                                    } else {
                                        $voteStatistics->voters_by_event = collect([$eventKey => $totalUsers]);
                                        $cb->_vote_statistics = json_encode($voteStatistics);
                                    }
                                } else {
                                    $voteStatistics->voters_by_event->{$eventKey} = $totalUsers;
                                    $cb->_vote_statistics = json_encode($voteStatistics);
                                }



                            }
                        } catch (Exception $e) {
                        }
                    }
                    $cb->save();
                }
            }
            return 'OK';
        }catch(Exception $e){
            dd($e->getMessage(), $e->getLine());
        }
    }

    /**
     *
     */
    public function updateCachedData(){
        try{
            $cbs = Cb::all();

            foreach ($cbs as $cb){
                CbsController::updateCachedData($cb->cb_key);
            }
        } catch (Exception $e){}
    }
}
