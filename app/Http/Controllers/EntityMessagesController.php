<?php

namespace App\Http\Controllers;

use App\Entity;
use App\Message;
use App\OrchUser;
use App\One\One;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Manager;

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
            $entityKey = $request->header('X-ENTITY-KEY');
            $entity = Entity::whereEntityKey($entityKey)->firstOrFail();
            $flag = $request->input('flag');
            $tableData = $request->input('tableData') ?? null;

            $managers = $entity->users()->where('entity_user.role', '=', 'manager')->get();
            $messages = null;
            if($managers){
                $managersKeys = $managers->pluck('user_key');
                if($flag == 'sentMessages'){
                    $messages = Message::whereIn('from',$managersKeys)->orderBy($tableData['order']['value'], $tableData['order']['dir']);
                }
                else{
                    $managersKeys = $managersKeys->push($entityKey);
                    $messages = Message::whereIn('to', $managersKeys)->orderBy($tableData['order']['value'], $tableData['order']['dir']);
                }
            }

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
                if($flag == 'sentMessages')
                    $userTo = User::whereUserKey($message->to)->first();
                 else
                     $userTo = User::whereUserKey($message->from)->first();
                $message->user_name = $userTo['name'];
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
}
