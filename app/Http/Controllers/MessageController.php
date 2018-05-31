<?php

namespace App\Http\Controllers;

use App\Entity;
use App\EntityCb;
use App\Message;
use App\One\One;
use App\OrchUser;
use App\Topic;
use App\User;
use App\ParameterUserType;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MessageController extends Controller
{
    /**
     * lists all of the user messages
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        try {
            $user = OrchUser::whereUserKey($userKey)->firstOrFail();
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

            $messages = $user->messages()->whereEntityId($entity->id)->get();

            foreach ($messages as $message){
                if (!is_null($message->topic_id)){
                    $topic = Topic::with('cb')->find($message->topic_id);

                    if (!is_null($topic)) {
                        $type = EntityCb::with('cbType')->whereCbKey($topic->cb->cb_key)->whereEntityId($entity->id)->first();
                    }

                    if (!is_null($topic) && !is_null($type)){
                        $message->link = ['topic_key' => $topic->topic_key ?? null, 'cb_key' => $topic->cb->cb_key ?? null, 'type' => $type->cbType->code ?? null];

                    }
                }
            }

            return response()->json($messages, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User or Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Messages'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function showMessage(Request $request){
        try{
        $message = Message::whereMessageKey($request->messageKey)->first();

        return response()->json($message, 200);

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Message'], 500);
        }
    }

    /**
     * get all users with unread messages
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function usersWithMessages(Request $request)
    {


        try {
            $userKeys = Message::whereTo($request->header('X-ENTITY-KEY'))->where('from', '!=', $request->header('X-ENTITY-KEY'))->whereViewed(false)->groupby('from')->distinct()->get(['from']);
            $users = [];
            if($userKeys){
                foreach ($userKeys as $userKey){
                    $users[] = $userKey['from'];
                }
            }
            return response()->json($users, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User or Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Messages'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * get all users with unread messages
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function usersWithMessages2(Request $request)
    {
        try {
            $arguments = $request->input('arguments') ?? null;

            $userKeys = Message::whereTo($request->header('X-ENTITY-KEY'))
                        ->where('from', '!=', $request->header('X-ENTITY-KEY'))
                        ->whereViewed(false)
                        ->take($arguments['numberOfRecords'] ?? 20)
                        ->groupby('from')
                        ->distinct()
                        ->get(['from']);

            $users = [];
            if($userKeys){
                foreach ($userKeys as $userKey){
                    $users[] = $userKey['from'];
                }
            }
            return response()->json($users, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User or Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Messages'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }





    /**
     * Stores a new message
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            if($request['register_message']){
                $user = $request['email'];
                $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
                $to = $request->header('X-ENTITY-KEY');
                
                $parameter = ParameterUserType::whereParameterUserTypeKey($request['parameter_user_key'])->firstOrFail();
                $parameter->newTranslation();


                $textMessage = "Name: $request->name <br>
                                Email: $request->email <br>
                                Mobile Phone: $request->mobile_phone <br>
                                $parameter->name: $request->parameter_value <br>
                                Text: $request->message";

                $key = "";
                    do {
                        $rand = str_random(32);
                        if (!($exists = Message::whereMessageKey($rand)->exists())) {
                            $key = $rand;
                        }
                    } while ($exists);

                $message = $entity->messages()->create([
                    'message_key' => $key,
                    'entity_id' => $entity->id,
                    'topic_id' => null,
                    'value' => $textMessage,
                    'to' => $to,
                    'from' => $user,
                    'viewed' => 0,
                    'viewed_at' => null,
                    'viewed_by' => null
                ]);
                
                $tags = [
                    "message"   => $textMessage
                ];
                
                ONE::sendNotificationEmail($request, 'new_messages', $tags);

                return response()->json($message, 201);

            }else{
                $userKey = ONE::verifyToken($request);

                $contents = $request->json('message') ?? null;
                if ($contents && (strlen($contents) > 0 && strlen(trim($contents)) > 0)) {

                    $user = OrchUser::whereUserKey($userKey)->firstOrFail();
                    $from = $user->user_key;
                    $topic = null;

                    if ($request->json('to')) {
                        $user = OrchUser::whereUserKey($request->json('to'))->firstOrFail();
                    }
                    $to = $request->json('to') ?? $request->header('X-ENTITY-KEY');
                    $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

                    if ($request->json('topic_key')) {
                        $topic = Topic::whereTopicKey($request->json('topic_key'))->first();
                    }

                    $key = "";
                    do {
                        $rand = str_random(32);
                        if (!($exists = Message::whereMessageKey($rand)->exists())) {
                            $key = $rand;
                        }
                    } while ($exists);

                    $message = $user->messages()->create([
                        'message_key' => $key,
                        'entity_id' => $entity->id,
                        'topic_id' => is_null($topic) ? null : $topic->id,
                        'value' => $request->json('message'),
                        'to' => $to,
                        'from' => $from,
                        'viewed' => 0,
                        'viewed_at' => null,
                        'viewed_by' => null
                    ]);

    //            Sends Notification Emails to the selected groups of users
                    ONE::sendNotificationEmail($request, 'new_messages');

                    return response()->json($message, 201);
                }
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User or Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Message'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Updates the status of a viewed message
     *
     * @param Request $request
     * @param $messageKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewed(Request $request, $messageKey)
    {
        $userKey = ONE::verifyToken($request);

        try {
            $message = Message::whereMessageKey($messageKey)->firstOrFail();

            $message->viewed = 1;
            $message->viewed_at = Carbon::now()->toDateTimeString();
            $message->viewed_by = $userKey;
            $message->save();

            return response()->json($message, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Message not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Message'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Updates the status of all messages sent to the user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsSeen(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        try {
            if ($request->json('from')){

                $fromUserKey = $request->json('from');
                $toEntity = $request->header('X-ENTITY-KEY');

                Message::whereTo($toEntity)->whereFrom($fromUserKey)->update([
                    'viewed' => 1,
                    'viewed_at' => Carbon::now()->toDateTimeString(),
                    'viewed_by' => $userKey,
                ]);

            } else {
                Message::whereTo($userKey)->update([
                    'viewed' => 1,
                    'viewed_at' => Carbon::now()->toDateTimeString(),
                    'viewed_by' => $userKey,
                ]);
            }

            return response()->json('OK', 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete messages'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Updates the status of all messages sent to the user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsUnseen(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        try {
            if ($request->json('from')){
                $message = Message::whereMessageKey($request->messageKey)->first();
                $fromUserKey = $request->json('from');
                $toEntity = $request->header('X-ENTITY-KEY');

                $message = $message->update([
                    'viewed' => 0,
                    'viewed_at' => NULL,
                    'viewed_by' => NULL,
                ]);

            } else {
                $message = Message::whereTo($userKey)->update([
                    'viewed' => 0,
                    'viewed_at' => NULL,
                    'viewed_by' => NULL,
                ]);
            }

            return response()->json($message, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete messages'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $messageKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $messageKey)
    {
        $userKey = ONE::verifyToken($request);

        try {
            $user = OrchUser::whereUserKey($userKey)->firstOrFail();
            $message = $user->messages()->whereMessageKey($messageKey)->firstOrFail();

            return response()->json($message, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Message not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Message'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $messageKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $messageKey)
    {
        ONE::verifyToken($request);
        try {
            Message::whereMessageKey($messageKey)->firstOrFail()->delete();

            return response()->json('Ok', 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Message not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete Message'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $userKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function messagesFrom(Request $request, $userKey)
    {
        ONE::verifyToken($request);
        try {
            $user = OrchUser::whereUserKey($userKey)->firstOrFail();
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();

            $messages = $user->messages()->whereEntityId($entity->id)->get();

            foreach ($messages as $message){
                if (!is_null($message->topic_id)){
                    $topic = Topic::with('cb')->find($message->topic_id);

                    if (!is_null($topic)) {
                        $type = EntityCb::with('cbType')->whereCbKey($topic->cb->cb_key)->whereEntityId($entity->id)->first();
                    }

                    if (!is_null($topic) && !is_null($type)){
                        $message->link = ['topic_key' => $topic->topic_key ?? null, 'cb_key' => $topic->cb->cb_key ?? null, 'type' => $type->cbType->code ?? null];
                    }
                }
            }

            return response()->json($messages, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User or Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Messages'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function sendToAll(Request $request) {
        $userKey = ONE::verifyToken($request);

        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
            $entityUsers = $entity->users()->get();
            $counters = array(
                "success" => 0,
                "failed" => 0,
            );
            $user = User::whereUserKey($userKey)->firstOrFail();
            $from = $user->user_key;

            foreach ($entityUsers as $entityUser) {
                try {
                    $key = "";
                    do {
                        $rand = str_random(32);
                        if (!($exists = Message::whereMessageKey($rand)->exists())) {
                            $key = $rand;
                        }
                    } while ($exists);


                    Message::create([
                        'message_key'   => $key,
                        'entity_id'     => $entity->id,
                        'value'         => $request->json('message'),
                        'to'            => $entityUser->user_key,
                        'from'          => $from,
                        'viewed'        => 0,
                        'viewed_at'     => null,
                        'viewed_by'     => null
                    ]);

                    $counters["success"]++;
                } catch (Exception $exception) {
                    $counters["failed"]++;
                }
            }

            return response()->json(["success"=>$counters["success"],"failed"=>$counters["failed"]], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User or Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Message'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

}
