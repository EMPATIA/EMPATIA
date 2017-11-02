<?php

namespace App\Http\Controllers;

use App\One\One;
use App\TopicQuestionaryReply;
use Exception;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class TopicQuestionaryReplyController extends Controller
{
    protected $keysRequired = [
        'topic_key',
        'form_reply_key'
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        ONE::verifyKeysRequest($this->keysRequired, $request);

        try{
            $topicFormReply = TopicQuestionaryReply::create(
                [
                    'topic_key' => $request->json('topic_key'),
                    'form_reply_key' => $request->json('form_reply_key')
                ]
            );
            return response()->json($topicFormReply, 201);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to store Topic FormReply Relation'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param $topicKey
     * @return array
     */
    public function getReplyByTopic($topicKey)
    {
        try {
            $replies = TopicQuestionaryReply::whereTopicKey($topicKey)->get();

            return response()->json(['data' => $replies], 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve Replies'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function getTopicByReply($replyKey)
    {
        try {
            $topics = TopicQuestionaryReply::whereFormReplyKey($replyKey)->get();

            return response()->json(['data' => $topics], 200);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve Topics'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
