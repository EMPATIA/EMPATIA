<?php

namespace App\ComModules;
use App\One\One;
use Exception;
use Illuminate\Http\Request;

class Vote
{
    /**
     * get users list
     *
     * @param $userKeys
     * @return mixed
     * @throws Exception
     */
    public static function listUser($userKeys) {
        $response = ONE::post([
            'component' => 'auth',
            'api'       => 'auth',
            'method'    => 'listUser',
            'params'    => [
                'userList' => $userKeys
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception('Error retrieving user information');
        }
        return $response->json();
    }

    /**
     * @param $keys
     * @return mixed
     * @throws Exception
     */
    public static function allVoteResults($keys) {
        $response = One::post([
            'component' => 'vote',
            'api'       => 'event',
            'method'    => 'allVoteResults',
            'params'    => [
                'keys' => $keys,
            ]
        ]);

        if ($response->statusCode() != 200)
            throw new Exception(trans("comModulesVote.errorRetrievingAllVoteResults"));

        return $response->json();
    }

    /**
     * @param Request $request
     * @param $eventKeys
     * @return mixed
     * @throws Exception
     */
    public static function manualUpdateTopicVotesInfo(Request $request, $eventKeys) {
        $response = One::put([
            'component' => 'vote',
            'api'       => 'event',
            'method'    => 'manualUpdateTopicVotesInfo',
            'params'    => [
                'event_keys' => $eventKeys,
            ],
            'headers' =>  [
                "X-AUTH-TOKEN: ". $request->header('X-AUTH-TOKEN')
            ]
        ]);

        if ($response->statusCode() != 200)
            throw new Exception(trans("comModulesVote.errorManualUpdateTopicVotesInfo"));

        return $response->json();
    }

    /**
     * @param $eventKey
     * @return mixed
     * @throws Exception
     */
    public static function getVoteEvent($eventKey) {
        $response = ONE::get([
            'component' => 'vote',
            'api'       => 'event',
            'attribute' => $eventKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception('Error getting vote event');
        }
        return $response->json();
    }

    public static function getAllEventLevelsByCbKey($cbKey) {
        $response = One::get([
            'component' => 'vote',
            'api' => 'eventlevels',
            'method' => 'getAllEventLevelsByCbKey',
            'params'=>[
                'cb_key'=> $cbKey
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingEventLevel"));
        }
        return $response->json();
    }

    public static function getVoteResults($voteKey) {
        $response = One::get([
            'component' => 'vote',
            'api' => 'event',
            'api_attribute' => $voteKey,
            'method' => 'voteResults'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetrievingVoteResults"));
        }
        return $response->json();
    }

    public static function getEventAndVotes($eventKey) {
        $response = One::get([
            'component' => 'vote',
            'api' => 'event',
            'api_attribute' => $eventKey,
            'method' => 'getEventAndVotes',
            'params' => [
                'openDataNoUser' => true
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesVote.errorRetreivingEventVotes"));
        }
        return $response->json();
    }
}