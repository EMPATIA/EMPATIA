<?php

namespace App\Http\Controllers;

use App\Cb;
use App\Configuration;
use App\One\One;
use App\Status;
use App\StatusType;
use App\Topic;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PadTopicsController extends Controller
{
    protected $required = [
        'store' => ['cb_key', 'title'],
        'update' => ['title'],
        'updateStatus' => ['blocked']
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyLogin($request);
        ONE::verifyKeysRequest($this->required["store"], $request);
        try {
            $data = [];

            //Get CB
            try {
                $data['cb'] = Cb::whereCbKey($request->json('cb_key'))->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'CB not Found'], 404);
            }

            $lastId = Topic::max('id');

            $data['id'] =  empty($lastId) ? 1 : $lastId + 1;
            $data['userKey'] = $userKey ?? 'anonymous';
            $data['summary'] = empty($request->json('summary')) ? null : clean($request->json('summary'));
            $data['contents'] = empty($request->json('contents')) ? '' : clean($request->json('contents'));
            $data['description'] = empty($request->json('description')) ? null : clean($request->json('description'));
            $data['lastTopic'] = Topic::whereCbId($data['cb']->id)->orderBy('created_at', 'desc')->first();
            $data['version'] = '1';

            //Generate Topic Key
            $data['key'] = '';
            do {
                $rand = str_random(32);
                if (!($exists = Topic::whereTopicKey($rand)->exists())) {
                    $data['key'] = $rand;
                }
            } while ($exists);

            //Create Topic
            try {
                $topic = $this->create($request, $data);
            } catch (Exception $e) {
                return response()->json(['error' => 'Failed to create new Topic'], 500);
            }

            //Store Topic Parameters
            try {
                if (!empty($request->json('parameters'))){
                    $topicParameters = $request->json('parameters');
                    $this->storeParameters($topic, $topicParameters);
                }
            } catch (Exception $e) {
                return response()->json(['error' => 'Failed to store topic parameters'], 500);
            }

            //Topic Status
            try {
                $config = Configuration::whereCode('topic_need_moderation')->first();
                if(!is_null($config)) {
                    $this->topicStatus($data['cb'], $config, $topic, $userKey);
                }
            } catch (Exception $e) {
                return response()->json(['error' => 'Failed to store topic status'], 500);
            }

            //Response
            $data = Topic::with('parameters.type')->findOrFail($topic->id);

            return response()->json(['topic' => $data], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Model not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to store new Topic'], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function update(Request $request, $topicKey)
    {
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required["update"], $request);

        try {
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();

            //Get CB
            try {
                $data['cb'] = $topic->cb()->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Failed to get CB'], 404);
            }

            $lastVersion = Topic::whereTopicKey($topicKey)->max('version');

            $data['id'] =  $topic->id;
            $data['version'] = empty($lastVersion) ? 1 : $lastVersion + 1;
            $data['key'] = $topic->topic_key;
            $data['userKey'] = $userKey ?? 'anonymous';
            $data['summary'] = empty($request->json('summary')) ? null : clean($request->json('summary'));
            $data['contents'] = empty($request->json('contents')) ? '' : clean($request->json('contents'));
            $data['description'] = empty($request->json('description')) ? null : clean($request->json('description'));
            $data['lastTopic'] = Topic::whereCbId($data['cb']->id)->orderBy('created_at', 'desc')->first();

            //Create Topic
            try {
                $topic = $this->create($request, $data);
            } catch (Exception $e) {
                return response()->json(['error' => 'Failed to create new Topic'], 500);
            }

            //Store Topic Parameters
            try {
                if (!empty($request->json('parameters'))){
                    $topicParameters = $request->json('parameters');
                    $this->storeParameters($topic, $topicParameters);
                }
            } catch (Exception $e) {
                return response()->json(['error' => 'Failed to store topic parameters'], 500);
            }

            //Response
            $data = Topic::with('parameters.type')->findOrFail($topic->id);

            return response()->json(['topic' => $data], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Topic'], 400);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $data
     * @return mixed
     */
    private function create(Request $request, $data)
    {
        //create Topic
        $topic = $data['cb']->topics()->create(
            [
                'id' => $data['id'],
                'version' => $data['version'],
                'topic_key' => $data['key'],
                'created_by' => $data['userKey'],
                'created_on_behalf' => $request->json('created_on_behalf') ?? null,
                'title' => clean($request->json('title')),

                //Possible conflict between Contents(summary) and Summary(summary)
                'contents' => $data['contents'],
                'summary' => $data['summary'],

                'blocked' => $request->json('blocked') ?? 0,
                'q_key' => $request->json('q_key') ?? 0,
                'topic_number' => isset($data['lastTopic']->topic_number) ? $data['lastTopic']->topic_number + 1 : 1,
                'start_date' => empty($request->json('start_date')) ? null : Carbon::createFromFormat('Y-m-d', clean($request->json('start_date')))->toDateTimeString(),
                'end_date' => empty($request->json('end_date')) ? null : Carbon::createFromFormat('Y-m-d', clean($request->json('end_date')))->toDateTimeString(),
                'description' => $data['description'],
                'language_code' => $request->header('LANG-CODE'),
                'active' => $request->json('active') ?? 0,
                'moderate' => $request->json('moderate') ?? 0,
                'moderated_by' => null,
            ]
        );
        return $topic;
    }

    /**
     * @param $topic
     * @param $topicParameters
     * @return bool
     */
    private function storeParameters($topic, $topicParameters)
    {
        $manualSyncParameters = [];
        $parameters = [];

        foreach ($topicParameters as $parameter) {
            if (is_array($parameter['value'])) {
                $manualSyncParameters[$parameter['parameter_id']] = implode(",", $parameter['value']);
            } else {
                $parameters[$parameter['parameter_id']] = array_merge(clean($parameter), ['version' => $topic->version]);
            }
        }

        $topic->parameters()->attach($parameters);

        foreach ($manualSyncParameters as $id => $value) {
            $topic->parameters()->attach($id, [
                'value' => clean($value),
                'version' => $topic->version,
            ]);
        }
        return true;
    }

    /**
     * @param $cb
     * @param $config
     * @param $topic
     * @param $userKey
     * @return bool
     */
    private function topicStatus($cb, $config, $topic, $userKey)
    {
        try {
            //check pivot table if topics need config in current CB
            $cb_config = $cb->configurations()->whereConfigurationId($config->id)->first();

            if(is_null($cb_config)){

                $statusType = StatusType::whereCode('moderated')->first();
                if (!is_null($statusType)){

                    //"disable" previous statuses
                    $statusUpdate = Status::whereTopicId($topic->id)->update(['active' => 0]);

                    //new key for status
                    $key = '';
                    do {
                        $rand = str_random(32);
                        if (!($exists = Status::whereStatusKey($rand)->exists())) {
                            $key = $rand;
                        }
                    } while ($exists);

                    $statusType->status()->create(
                        [
                            'status_key' => $key,
                            'status_type_id' => $statusType->id,
                            'topic_id' => $topic->id,
                            'active' => 1,
                            'created_by' => is_null($userKey) ? 'anonymous' : $userKey
                        ]
                    );
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
