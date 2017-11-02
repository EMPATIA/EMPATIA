<?php

namespace App\Http\Controllers;

use App\Cb;
use App\ComModules\Notify;
use App\EntityGroup;
use App\One\One;
use App\TechnicalAnalysis;
use App\TechnicalAnalysisNotification;
use App\TechnicalAnalysisQuestion;
use App\TechnicalAnalysisQuestionAnswer;
use App\Topic;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Entity;

class TechnicalAnalysesController extends Controller
{
    protected $required = [
        'store'   => ['topicKey'],
        'show'    => ['cbKey'],
        'active'  => ['active']
    ];

    /**
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTechnicalAnalysis($topicKey)
    {
        try {
            $topic = Topic::with('technicalAnalysis')->whereTopicKey($topicKey)->firstOrFail();
            return response()->json($topic);

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Technical Analysis'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $technicalAnalysis = TechnicalAnalysis::all();
            return response()->json(['data' => $technicalAnalysis], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Post Comment Types'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * The method show, build an object to send to WUI with these content: Technical Analysis with
     * all Questions of that CB. Each Question could have answer or not but the ones with an answer
     * are the answer of this particular Technical Analysis (which was got with Topic key). An array
     * with all versions and their date of creation is also put on the array. The topic title is also
     * coming in the response.
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $topicKey)
    {
        try{
            $cb = Cb::whereCbKey($request->cbKey)->firstOrFail();             /* get CB with cb key received on request */
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();        /* get Topic with topic key received on argument */

            if(!$request->version){
                $technicalAnalysis = $topic->technicalAnalysis()->where('active', true)->first(); /* get the active Technical Analysis of correspondent Topic */
            }else {
                $technicalAnalysis = $topic->technicalAnalysis()->where('version', $request->version)->first(); /* get the active Technical Analysis of correspondent Topic */
            }

            /* get all Questions with or without answers from correspondent Technical Analysis (gotten before). The answers are only from this TA */
            $technicalAnalysisQuestions = $cb->technicalAnalysisQuestions()
                ->with(['technicalAnalysisQuestionAnswers' => function ($query) use ($technicalAnalysis) {
                    $query->where('technical_analysis_id','=',$technicalAnalysis->id);
                }])->get();

            /* Attribute language to the question */
            foreach($technicalAnalysisQuestions as $technicalAnalysisQuestion){
                if (!($technicalAnalysisQuestion->translation($request->header('LANG-CODE')))) {
                    if (!$technicalAnalysisQuestion->translation($request->header('LANG-CODE-DEFAULT'))) {
                        if (!$technicalAnalysisQuestion->translation('en')) {
                            return response()->json(['error' => 'No translation found'], 404);
                        }
                    }
                }
            }

            $technicalAnalysis->technical_analysis_questions = $technicalAnalysisQuestions; /* add the questions (and answers) to the Technical Analysis */

            $technicalAnalysisVersionData = $topic->technicalAnalysis()->select('version','created_at','active')->get();    /* get all technical analysis versions */

            $data['technicalAnalysisActive']            = $technicalAnalysis;
            $data['technicalAnalysisVersionData']       = $technicalAnalysisVersionData;
            $data['topic_title']                        = $topic->title;                        /* get topic title to put in form header */

            return response()->json( $data,200);

        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Technical Analysis not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve Technical Analysis'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * This method receive values on request to be stored on the tables. Such as the 4
     * values of Technical Analysis and the answers. Which answer come with a respective
     * question key so that question id could be stored within the answer. Since this is
     * a create the version will start with 1.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $userKey = ONE::verifyToken($request);

        ONE::verifyKeysRequest($this->required['store'], $request);

        try {
            /* ------------------ Technical Analysis ------------------- */

            $topicKey = $request->json('topicKey');                        /* receive $topicKey from request */
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();            /* find correspondent key on topic table */
            $accepted = $request->json('accepted') ?? null;

            $technicalAnalysis = $topic->technicalAnalysis()->first();

            if(empty($technicalAnalysis)){
                /* Generate technical_analysis_key */
                $key = '';
                do {
                    $rand = str_random(32);
                    /* Check if key already exists */
                    if (!($exists = TechnicalAnalysis::whereTechnicalAnalysisKey($rand)->exists())) {
                        $key = $rand;
                    }
                } while ($exists);

                /* Create Technical Analysis on table  */
                $technicalAnalysis = $topic->technicalAnalysis()->create(                   /* topic_id come here from $topic */
                    [
                        'technical_analysis_key'    => $key,
                        'impact'                    => $request->json('impact') ?? null,
                        'budget'                    => $request->json('budget') ?? null,
                        'execution'                 => $request->json('execution') ?? null,
                        'sustainability'            => $request->json('sustainability') ?? null,
                        'decision'                  => $request->json('decision') ?? null,
                        'created_by'                => $userKey,
                        'updated_by'                => $userKey,
                        'active'                    => true,
                        'version'                   => 1
                    ]
                );

                /* -------------- Technical Analysis Question Answer --------------- */
                if(!empty($request->technicalAnalysisQuestionsAndAnswers)){
                    foreach($request->json('technicalAnalysisQuestionsAndAnswers') as $technicalAnalysisQuestionKey => $technicalAnalysisQuestionAnswerValue){
                        /* receive key from technicalAnalysisQuestion to identify */
                        $technicalAnalysisQuestion = TechnicalAnalysisQuestion::whereTechAnalysisQuestionKey($technicalAnalysisQuestionKey)
                            ->first();

                        if(!empty($technicalAnalysisQuestion) && !empty($technicalAnalysis)) {
                            if (isset($technicalAnalysisQuestion->id) && isset($technicalAnalysis->id)) {
                                /* Compare Answer foreign keys with respectably tables */           //TODO REVIEW IS THIS VERIFICATION NEEDED?
                                $existTechnicalAnalysisQuestionAnswer = TechnicalAnalysisQuestionAnswer::whereTechnicalAnalysisId($technicalAnalysis->id)
                                    ->whereTechnicalAnalysisQuestionId($technicalAnalysisQuestion->id)
                                    ->first();

                                if (empty($existTechnicalAnalysisQuestionAnswer)){
                                    /* Generate technical_analysis_quest_ans_key */
                                    do {
                                        $rand = str_random(32);
                                        /* Check if key already exists */
                                        if (!($exists = TechnicalAnalysisQuestionAnswer::whereTecAQAnsKey($rand)->exists())) { //check if singular or plural
                                            $key = $rand;
                                        }
                                    } while ($exists);

                                    /* technicalAnalysisQuestionAnswers creation */
                                    $technicalAnalysisQuestionAnswer = $technicalAnalysis->technicalAnalysisQuestionsAnswers()->create(   /* equivalent to Many to Many */
                                        [                                                                                                 /* technical_analysis_id come from $technicalAnalysis */
                                            'tec_a_q_ans_key' => $key,
                                            'value' => $technicalAnalysisQuestionAnswerValue,                                             /* came from $request and is declared in foreach */
                                            'created_by' => $userKey,
                                            'updated_by' => $userKey,
                                            'technical_analysis_question_id' => $technicalAnalysisQuestion->id,                          /* key came from $request declared in foreach    */
                                            'accepted' => is_null($accepted) ? 0 : (in_array($technicalAnalysisQuestionKey, $accepted) ? 1 : 0)
                                        ]                                                                                                 /* got respective object with key then use is id */
                                    );
                                }
                            }
                        }
                    }
                }
                return response()->json($technicalAnalysis,201);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Technical Analysis not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store Technical Analysis'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }

    /**
     * This method receive values on request to create a new version of TA. Such as
     * the 4 values of Technical Analysis and the answers. Each answer come with a
     * respective question key from $request. This way will be easy to associate
     * question id with answers. Lastly is necessary to know the last created
     * version so it could be increment to create the new version.
     *
     *
     * @param Request $request
     * @param $version
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $version)
    {

        /* token verification */
        $userKey = ONE::verifyToken($request);

        try {

            $topicKey = $request->json('topicKey');                        /* receive $topicKey from request */
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();            /* find correspondent key on topic table */
            $accepted = $request->json('accepted') ?? null;

            $technicalAnalysisLastVersion = $topic->technicalAnalysis()->orderBy('version', 'desc')->first(); /* get last version of technical analysis */

            /* ------------------ Technical Analysis ------------------- */

            /* Generate technical_analysis_key */
            $key = '';
            do {
                $rand = str_random(32);
                /* Check if key already exists */
                if (!($exists = TechnicalAnalysis::whereTechnicalAnalysisKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            /* Create Technical Analysis on table  */
            $technicalAnalysis = $topic->technicalAnalysis()->create(                   /* topic_id come here from $topic */
                [
                    'technical_analysis_key'    => $key,
                    'impact'                    => $request->json('impact') ?? null,
                    'budget'                    => $request->json('budget') ?? null,
                    'execution'                 => $request->json('execution') ?? null,
                    'sustainability'            => $request->json('sustainability') ?? null,
                    'decision'                  => $request->json('decision') ?? null,
                    'created_by'                => $userKey,
                    'updated_by'                => $userKey,
                    'active'                    => false,                                           //TODO TRUE OR FALSE???
                    'version'                   => $technicalAnalysisLastVersion->version + 1
                ]
            );

            /* -------------- Technical Analysis Question Answer --------------- */
            if(!empty($request->technicalAnalysisQuestionsAndAnswers)){
                foreach($request->json('technicalAnalysisQuestionsAndAnswers') as $technicalAnalysisQuestionKey => $technicalAnalysisQuestionAnswerValue){
                    /* receive key from technicalAnalysisQuestion to identify */
                    $technicalAnalysisQuestion = TechnicalAnalysisQuestion::whereTechAnalysisQuestionKey($technicalAnalysisQuestionKey)->first();

                    if(!empty($technicalAnalysisQuestion) && !empty($technicalAnalysis)) {
                        if (isset($technicalAnalysisQuestion->id) && isset($technicalAnalysis->id)) {
                            /* Compare Answer foreign keys with respectably tables */           //TODO REVIEW IS THIS VERIFICATION NEEDED?
                            $existTechnicalAnalysisQuestionAnswer = TechnicalAnalysisQuestionAnswer::whereTechnicalAnalysisId($technicalAnalysis->id)
                                ->whereTechnicalAnalysisQuestionId($technicalAnalysisQuestion->id)
                                ->first();

                            if (empty($existTechnicalAnalysisQuestionAnswer)){
                                /* Generate technical_analysis_quest_ans_key */
                                do {
                                    $rand = str_random(32);
                                    /* Check if key already exists */
                                    if (!($exists = TechnicalAnalysisQuestionAnswer::whereTecAQAnsKey($rand)->exists())) {
                                        $key = $rand;
                                    }
                                } while ($exists);

                                /* technicalAnalysisQuestionAnswers creation */
                                $technicalAnalysisQuestionAnswer = $technicalAnalysis->technicalAnalysisQuestionsAnswers()->create(
                                    [
                                        'tec_a_q_ans_key' => $key,
                                        'value' => $technicalAnalysisQuestionAnswerValue,
                                        'created_by' => $userKey,
                                        'updated_by' => $userKey,
                                        'technical_analysis_question_id' => $technicalAnalysisQuestion->id,
                                        'accepted' => is_null($accepted) ? 0 : (in_array($technicalAnalysisQuestionKey, $accepted) ? 1 : 0)
                                    ]
                                );


                            }
                        }
                    }
                }
            }


            $topic->technicalAnalysis()->update(['active' => false]);

            /* get technicalAnalysis with the received version and put active at true*/
            $technicalAnalysis->active = true;
            $technicalAnalysis->save();

            return response()->json($technicalAnalysis,200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic Review not Found'], 404);
        } catch (Exception $e) {
            return response()->json($e->getMessage());
//            return response()->json(['error' => 'Failed to store Topic Review'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }

    /**
     * Find the Technical Analysis with the receive key and safe delete it
     * and its correspondent Technical Analysis Question Answers
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     * @internal param $technicalAnalysisKey
     */
    public function destroy(Request $request, $topicKey)
    {

        ONE::verifyToken($request);

        try{

            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();            /* find correspondent key on topic table */

            $technicalAnalysisIds = $topic->technicalAnalysis()->pluck('id');
            TechnicalAnalysisQuestionAnswer::whereIn('technical_analysis_id',$technicalAnalysisIds)->delete();
            $topic->technicalAnalysis()->delete();

            return response()->json('Ok',200);

        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Technical Analysis not Found'], 404);
        }catch (Exception $e) {
            dd($e);
            return response()->json(['error' => 'Failed to delete Technical Analysis'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }

    /**
     * This method makes the Technical Analysis version activation and
     * remove the version that was active (in this case all versions
     * non active versions will be put to inactive for security)
     *
     * @param Request $request
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function activate(Request $request, $topicKey){

        try {
            $version = $request->json('version');
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();        /* get Topic with topic key received on argument */

            /* put all versions to inactive */
            $topic->technicalAnalysis()->update(['active' => false]);

            /* get technicalAnalysis with the received version and put active at true*/
            $technicalAnalysisToActivate = $topic->technicalAnalysis()->where('version', $version)->first();
            $technicalAnalysisToActivate->active = true;
            $technicalAnalysisToActivate->save();

            return response()->json($technicalAnalysisToActivate, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Technical Analysis not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to activate Technical Analysis'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Get Technical Analysis of Topic (with $topicKey) and if exists
     * return true else return false
     *
     * @param $topicKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyIfTopicHasTechnicalAnalysis($topicKey){

        try {
            $topic = Topic::whereTopicKey($topicKey)->firstOrFail();              /* find correspondent key on topic table */
            $technicalAnalysisExists = $topic->technicalAnalysis()->exists();     /* get existence of technical analysis from respective topic */

            $data['technicalAnalysisExists'] = $technicalAnalysisExists;
            $data['topicTitle']              = $topic->title;

            return response()->json($data,200);

        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve Technical Analysis'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function sendNotification(Request $request, $technicalAnalysisKey){
        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first();
            $groupsKey = !empty($request->input('groups')) ? explode(',',$request->input('groups')) : [];
            $managersKey = !empty($request->input('managers')) ? explode(',',$request->input('managers')) : [];

            $technicalAnalysis = TechnicalAnalysis::whereTechnicalAnalysisKey($technicalAnalysisKey)->firstOrFail();

            if(!empty($groupsKey)){
                $groups = [];
                foreach ($groupsKey as $key){
                    $groups[] = $technicalAnalysis->technicalAnalysisNotifications()->create([
                        'group_key' => $key,
                    ]);
                }

                $entityGroups = EntityGroup::whereEntityId($entity->id)->whereIn('entity_group_key', $groupsKey)->get();
                foreach ($entityGroups as $entityGroup){
                    $groupsUserKey = $entityGroup->users()->get()->pluck('user_key');
                }

                $groupsEmail = (User::whereIn('user_key', $groupsUserKey)->get()->pluck('email'))->toArray();
            }

            if(!empty($managersKey)){
                $managers = [];
                foreach ($managersKey as $key){
                    $managers[] = $technicalAnalysis->technicalAnalysisNotifications()->create([
                        'manager_key' => $key,
                    ]);
                }
                $managersEmail = (User::whereIn('user_key', $managersKey)->get()->pluck('email'))->toArray();
            }


            if(!empty($groupsKey) && !empty($managersKey)){
                $mails = array_merge($groupsEmail, $managersEmail);
            }elseif (!empty($groupsKey) && empty($managersKey)){
                $mails = $groupsEmail;
            }elseif (empty($groupsKey) && !empty($managersKey)){
                $mails = $managersKey;
            }

            if(!empty($mails)){
                $emailTemplate = Notify::getEmailTemplate($request->header('X-SITE-KEY'), 'technical_analysis_notification');
                if(!empty($emailTemplate)){
                    $response = Notify::sendEmailByTemplateKey($request, (object)$request->input('site'), $emailTemplate->email_template_key, $mails, $request->input('userKey'), null);
                }else{
                    $response = 'Notification not send';
                }
            }else{
                $response = 'Notification not send';
            }

            return response()->json($response,200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Technical Analysis not Found'], 404);
        }catch(Exception $e){
            return response()->json(['error' => 'Failed to retrieve Technical Analysis'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
