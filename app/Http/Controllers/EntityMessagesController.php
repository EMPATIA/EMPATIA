<?php

namespace App\Http\Controllers;

use App\Entity;
use App\Message;
use App\OrchUser;
use App\One\One;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Manager;
use Illuminate\Support\Facades\DB;

class EntityMessagesController extends Controller
{
    public function index(Request $request){
        ONE::verifyToken($request);

        try{
            $entityKey = $request->header('X-ENTITY-KEY');
            $userKey = $request->input('user_key');

            $entity = Entity::whereEntityKey($entityKey)->firstOrFail();
            $user  = OrchUser::whereUserKey($userKey)->first();

            $tableData = $request->input('tableData') ?? null;
            $messages = $user->messages()->whereEntityId($entity->id)->orderBy($tableData['order']['value'], $tableData['order']['dir']);




            $recordsTotal = $messages->count();

            if(!empty($tableData['search']['value'])) {
                $messages = $messages
                    ->orWhere('value', 'like', '%'.$tableData['search']['value'].'%');
            }

            $messages = $messages
                ->skip($tableData['start'])
                ->take($tableData['length'])
                ->get();

            foreach ($messages as $message){
                if(!empty(User::whereUserKey($message->to)->first())){
                    $userTo = User::whereUserKey($message->to)->first();
                    $message->user_name = $userTo['name'];
                }
            }
            $recordsFiltered = $messages->count();

            $data['messages'] = $messages;
            $data['recordsTotal'] = $recordsTotal;
            $data['recordsFiltered'] = $recordsFiltered;

            return response()->json($data, 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to get Messages'], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Failed to get Messages'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function getEntityMessages(Request $request){
        ONE::verifyToken($request);

        try{
            $entity = One::getEntity($request);
            $filters = $request->get("filters");
            $tableData = $request->input('tableData') ?? null;

            $managersKeys = $entity->users()->where('entity_user.role', '=', 'manager')->get()->pluck("user_key");
            $managersKeys = $managersKeys->merge(OrchUser::whereAdmin(1)->pluck("user_key"))->toArray();

            $query = $entity->messages()->with([
                "sender.orchUser.entities" => function($q) use ($entity) {
                    $q->where("entity_id","=",$entity->id);
                },
                "receiver.orchUser.entities" => function($q) use ($entity) {
                    $q->where("entity_id","=",$entity->id);
                }
            ]);

            if ((!empty($filters["received"]) && !empty($filters["sent"])) || (empty($filters["received"]) && empty($filters["sent"]))) {
                // All messages
                $query = $query;
            } else if (!empty($filters["received"])) {
                // Only Received
                $query = $query->whereTo($entity->entity_key);
            } else if (!empty($filters["sent"])) {
                // Only Sent
                $query = $query->whereIn("from",$managersKeys);
            }

            $query = $query->orderBy($tableData['order']['value'], $tableData['order']['dir']);

            $recordsTotal = $query->count();

            if(!empty($tableData['search']['value']))
                $query = $query->where('value', 'like', '%'.$tableData['search']['value'].'%');

            $recordsFiltered = $query->count();
            $messages = $query
                ->skip($tableData['start'])
                ->take($tableData['length'])
                ->get();

            foreach ($messages as $message) {
                if (in_array($message->from,$managersKeys)) {
                    $message->type = "sent";
                } else {
                    $message->type = "received";
                }
            }

            $data['messages'] = $messages;
            $data['recordsTotal'] = $recordsTotal;
            $data['recordsFiltered'] = $recordsFiltered;
            $data['entityName'] = $entity->name;

            return response()->json($data, 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to get Messages'], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Failed to get Messages'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function getSentMessages(Request $request){
        ONE::verifyToken($request);

        try{
            $entity = One::getEntity($request);
            $tableData = $request->input('tableData') ?? null;

            $managersKeys = $entity->users()->where('entity_user.role', '=', 'manager')->get()->pluck("user_key");
            $managersKeys = $managersKeys->merge(OrchUser::whereAdmin(1)->pluck("user_key"))->toArray();

            $query = $entity->messages()->with([
                "sender.orchUser.entities" => function($q) use ($entity) {
                    $q->where("entity_id","=",$entity->id);
                },
                "receiver.orchUser.entities" => function($q) use ($entity) {
                    $q->where("entity_id","=",$entity->id);
                }
            ]);

            // Only Sent
            $query = $query->whereIn("from",$managersKeys);

            $query = $query->orderBy($tableData['order']['value'], $tableData['order']['dir']);

            $recordsTotal = $query->count();

            if(!empty($tableData['search']['value']))
                $query = $query->where('value', 'like', '%'.$tableData['search']['value'].'%');

            $recordsFiltered = $query->count();
            $messages = $query
                ->skip($tableData['start'])
                ->take($tableData['length'])
                ->get();

            foreach ($messages as $message) {
                if (in_array($message->from,$managersKeys)) {
                    $message->type = "sent";
                } else {
                    $message->type = "received";
                }
            }

            $data['messages'] = $messages;
            $data['recordsTotal'] = $recordsTotal;
            $data['recordsFiltered'] = $recordsFiltered;
            $data['entityName'] = $entity->name;

            return response()->json($data, 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to get Messages'], 500);
        } catch (ModelNotFoundException $e) {
    return response()->json(['error' => 'Failed to get Messages'], 404);
}
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function getReceivedMessages(Request $request){
        ONE::verifyToken($request);

        try{
            $entity = One::getEntity($request);
            $tableData = $request->input('tableData') ?? null;

            $managersKeys = $entity->users()->where('entity_user.role', '=', 'manager')->get()->pluck("user_key");
            $managersKeys = $managersKeys->merge(OrchUser::whereAdmin(1)->pluck("user_key"))->toArray();

            $query = $entity->messages()->with([
                "sender.orchUser.entities" => function($q) use ($entity) {
                    $q->where("entity_id","=",$entity->id);
                },
                "receiver.orchUser.entities" => function($q) use ($entity) {
                    $q->where("entity_id","=",$entity->id);
                }
            ]);

            // Only Received
            $query = $query->whereTo($entity->entity_key);

            $query = $query->orderBy($tableData['order']['value'], $tableData['order']['dir']);

            $recordsTotal = $query->count();

            if(!empty($tableData['search']['value']))
                $query = $query->where('value', 'like', '%'.$tableData['search']['value'].'%');

            $recordsFiltered = $query->count();
            $messages = $query
                ->skip($tableData['start'])
                ->take($tableData['length'])
                ->get();

            foreach ($messages as $message) {
                if (in_array($message->from,$managersKeys)) {
                    $message->type = "sent";
                } else {
                    $message->type = "received";
                }
            }

            $data['messages'] = $messages;
            $data['recordsTotal'] = $recordsTotal;
            $data['recordsFiltered'] = $recordsFiltered;
            $data['entityName'] = $entity->name;

            return response()->json($data, 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to get Messages'], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Failed to get Messages'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function countEntitySentMessages(Request $request){
    ONE::verifyToken($request);

    try{
        $entity = One::getEntity($request);

        $managersKeys = $entity->users()->where('entity_user.role', '=', 'manager')->get()->pluck("user_key");
        $managersKeys = $managersKeys->merge(OrchUser::whereAdmin(1)->pluck("user_key"))->toArray();

        $query = $entity->messages()->with([
            "sender.orchUser.entities" => function($q) use ($entity) {
                $q->where("entity_id","=",$entity->id);
            },
            "receiver.orchUser.entities" => function($q) use ($entity) {
                $q->where("entity_id","=",$entity->id);
            }
        ]);

        // Only Sent
        $query = $query->whereIn("from",$managersKeys);

        $messages = $query
            ->count();

//            dd($messages);

//            $data['messages'] = $messages;

        return response()->json($messages, 200);
    } catch (QueryException $e) {
        return response()->json(['error' => 'Failed to get Messages'], 500);
    } catch (ModelNotFoundException $e) {
        return response()->json(['error' => 'Failed to get Messages'], 404);
    }
    return response()->json(['error' => 'Unauthorized'], 401);
}

    public function countEntityReceivedMessages(Request $request){
    ONE::verifyToken($request);

    try{
        $entity = One::getEntity($request);

        $managersKeys = $entity->users()->where('entity_user.role', '=', 'manager')->get()->pluck("user_key");
        $managersKeys = $managersKeys->merge(OrchUser::whereAdmin(1)->pluck("user_key"))->toArray();

        $query = $entity->messages()->with([
            "sender.orchUser.entities" => function($q) use ($entity) {
                $q->where("entity_id","=",$entity->id);
            },
            "receiver.orchUser.entities" => function($q) use ($entity) {
                $q->where("entity_id","=",$entity->id);
            }
        ]);

        // Only Received
        $query = $query->whereTo($entity->entity_key);

        $messages = $query
            ->count();

        return response()->json($messages, 200);
    } catch (QueryException $e) {
        return response()->json(['error' => 'Failed to get Messages'], 500);
    } catch (ModelNotFoundException $e) {
        return response()->json(['error' => 'Failed to get Messages'], 404);
    }
    return response()->json(['error' => 'Unauthorized'], 401);
}

    public function getEntitySentMessages(Request $request){
    ONE::verifyToken($request);


//        dd($request);
    try{
        $entity = One::getEntity($request);
        $tableData = $request->input('tableData') ?? null;

        $managersKeys = $entity->users()->where('entity_user.role', '=', 'manager')->get()->pluck("user_key");
        $managersKeys = $managersKeys->merge(OrchUser::whereAdmin(1)->pluck("user_key"))->toArray();

        $query = $entity->messages()->with([
            "sender.orchUser.entities" => function($q) use ($entity) {
                $q->where("entity_id","=",$entity->id);
            },
            "receiver.orchUser.entities" => function($q) use ($entity) {
                $q->where("entity_id","=",$entity->id);
            }
        ]);

        // Only Sent
        $query = $query->whereIn("from",$managersKeys);

        $messages = $query->selectRaw('count(created_at) as total_sent_messages, DAY(created_at) as day, MONTH(created_at) as month, YEAR(created_at) as year')
            ->whereDate('created_at', '>=' ,$request->startDate)
            ->whereDate('created_at', '<=', $request->endDate)
            ->groupBy(DB::raw('DAY(created_at)'))
            ->get();

        return response()->json($messages, 200);
    } catch (QueryException $e) {
        return response()->json(['error' => 'Failed to get Messages'], 500);
    } catch (ModelNotFoundException $e) {
        return response()->json(['error' => 'Failed to get Messages'], 404);
    }
    return response()->json(['error' => 'Unauthorized'], 401);
}

    public function getEntityReceivedMessages(Request $request){
    ONE::verifyToken($request);

    try{
        $entity = One::getEntity($request);

        $managersKeys = $entity->users()->where('entity_user.role', '=', 'manager')->get()->pluck("user_key");
        $managersKeys = $managersKeys->merge(OrchUser::whereAdmin(1)->pluck("user_key"))->toArray();

        $query = $entity->messages()->with([
            "sender.orchUser.entities" => function($q) use ($entity) {
                $q->where("entity_id","=",$entity->id);
            },
            "receiver.orchUser.entities" => function($q) use ($entity) {
                $q->where("entity_id","=",$entity->id);
            }
        ]);

        // Only Received
        $query = $query->whereTo($entity->entity_key);

        $messages = $query->selectRaw('count(created_at) as total_received_messages, DAY(created_at) as day, MONTH(created_at) as month, YEAR(created_at) as year')
            ->whereDate('created_at', '>=' ,$request->startDate)
            ->whereDate('created_at', '<=', $request->endDate)
            ->groupBy(DB::raw('DAY(created_at)'))
            ->get();

        return response()->json($messages, 200);
    } catch (QueryException $e) {
        return response()->json(['error' => 'Failed to get Messages'], 500);
    } catch (ModelNotFoundException $e) {
        return response()->json(['error' => 'Failed to get Messages'], 404);
    }
    return response()->json(['error' => 'Unauthorized'], 401);
}

    public function getCountTotalSentEmails30DPersonalized(Request $request){
    ONE::verifyToken($request);

    try{
        $entityKey = $request->header('X-ENTITY-KEY');
        $totalEmails= Email::whereEntityKey($entityKey)
            ->selectRaw('count(created_at) as total_sent_emails, DAY(created_at) as day, MONTH(created_at) as month, YEAR(created_at) as year') // use your field for count
            ->whereDate('created_at', '>=' ,$request->startDate)
            ->whereDate('created_at', '<=', $request->endDate)
            ->where('sent', '=' ,'1')
            ->groupBy(DB::raw('DAY(created_at)'))
            ->get();

        return response()->json($totalEmails, 200);

    } catch (QueryException $e) {
//            print_r($e);
        return response()->json(['error' => 'Failed to get Entity Sms'], 500);
    } catch (ModelNotFoundException $e) {
        return response()->json(['error' => 'Sms not Found'], 404);
    }

    return response()->json(['error' => 'Unauthorized'], 401);
}
}
