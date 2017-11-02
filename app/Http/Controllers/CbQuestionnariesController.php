<?php

namespace App\Http\Controllers;

use App\Action;
use App\Cb;
use App\CbQuestionnaireVote;
use App\CbQuestionnaries;
use App\CbQuestionnarieTranslation;
use App\ComModules\Q;
use App\One\One;
use App\OrchUser;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Requests;
use Carbon\Carbon;
use App\Http\Controllers\Controller;



use Illuminate\Support\Collection;
use Exception;



class CbQuestionnariesController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $cbQuestionnaries = CbQuestionnaries::all();

            foreach ($cbQuestionnaries as $cbQuestionnarie) {
                if (!($cbQuestionnarie->translation($request->header('LANG-CODE')))) {
                    if (!$cbQuestionnarie->translation($request->header('LANG-CODE-DEFAULT')))
                        return response()->json(['error' => 'No translation found'], 404);
                }
            }

            return response()->json(['data' => $cbQuestionnaries], 200);
        } catch (Exception $e) {

            return response()->json(['error' => 'Failed to retrieve the CbQuestionnaries list'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCbQuestionnaire(Request $request){
        try {
            $cb = Cb::whereCbKey($request->cbKey)->firstOrFail();
            $cbQuestionnairesCollection = $cb->cbQuestionnaire()->with('action', 'cbQuestionnaireTranslation', 'cbQuestionnaireVote')->get();

            $cbQuestionnaires = null;
            if  ($cbQuestionnairesCollection){

                $voteEvents = [];
                foreach ($cbQuestionnairesCollection as $item) {
                    if ($item->action->code == 'vote_event'){
                        $voteEvents[$item->cbQuestionnaireVote->vote_event_key] = $item;
                    }
                }

                if (!empty($voteEvents)){
                    $cbQuestionnaires = $cbQuestionnairesCollection->keyBy('action.code');
                    $cbQuestionnaires['vote_event'] = $voteEvents;
                } else {
                    $cbQuestionnaires = $cbQuestionnairesCollection->keyBy('action.code');
                }
            }

            return response()->json(['data' => $cbQuestionnaires]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CbQuestionnaires not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCbQuestionnaireTemplate(Request $request){
        try {
            $cb = Cb::whereCbKey($request->cbKey)->first();
            $action = Action::whereCode($request->actionCode)->first();
            $voteKey = $request->input('voteKey');

            $template = null;
            if(!empty($voteKey)){
                $questionnaireVote = CbQuestionnaireVote::whereVoteEventKey($voteKey)->first();
                if(!empty($questionnaireVote)){
                    $cbQuestionnaire = $questionnaireVote->cbQuestionnaire()->first();
                    if(!empty($cbQuestionnaire)){
                        $template = $cbQuestionnaire->cbQuestionnaireTranslation()->get();
                    }
                }
            }
            else{
                $cbQuestionnaire = CbQuestionnaries::whereActionId($action->id)->whereCbId($cb->id)->first();

                if(!empty($cbQuestionnaire)){
                    $template = $cbQuestionnaire->cbQuestionnaireTranslation()->get();
                }
            }

            return response()->json(['data' =>$template], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'QuestionnaireTemplate not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param $cb
     * @param $action
     * @param $configuration
     * @param null $options
     * @return null
     */
    private static function createOrUpdateCbQuestionnaire($cb, $action, $configuration, $options = null)
    {
        try {
            $cbQuestionnaire = is_null($options) ? $cb->cbQuestionnaire()->whereActionId($action->id)->first() : (is_null($options['cb_questionnarie_key']) ? null : $cb->cbQuestionnaire()->whereCbQuestionnarieKey($options['cb_questionnarie_key'])->first());

            if (is_null($cbQuestionnaire)) {

                $key = '';
                do {
                    $rand = str_random(32);
                    if (!($exists = CbQuestionnaries::whereCbQuestionnarieKey($rand)->exists())) {
                        $key = $rand;
                    }
                } while ($exists);

                $cbQuestionnaire = $cb->cbQuestionnaire()->create(
                    [
                        'cb_questionnarie_key' => $key,
                        'action_id' => $action->id,
                        'questionnarie_key' => $configuration['questionnaire_key'],
                        'notify_email' => $configuration['notify'] ?? 0,
                        'ignore' => $configuration['ignore'] ?? 0,
                        'days_to_ignore' => $configuration['days'] ?? 0,
                    ]
                );
            } else {
                $cbQuestionnaire->update([
                    'questionnarie_key' => $configuration['questionnaire_key'],
                    'notify_email' => $configuration['notify'],
                    'ignore' => $configuration['ignore'],
                    'days_to_ignore' => $configuration['days'],
                ]);
            }

            return $cbQuestionnaire;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param $questionnaire
     * @param $cbQuestionnaire
     * @return bool|null
     */
    private static function createOrUpdateTranslations($questionnaire, $cbQuestionnaire){
        try {
            $cbQuestionnaireTranslations = null;
            foreach ($questionnaire as $languageCode => $translation){
                $cbQuestionnaireTranslations = $cbQuestionnaire->cbQuestionnaireTranslation()->whereLanguageCode($languageCode)->first();

                if ($cbQuestionnaireTranslations){
                    $cbQuestionnaireTranslations = $cbQuestionnaireTranslations->update([
                        'content' => $translation['content'],
                        'accept' => $translation['accept'],
                        'ignore' => $translation['ignore'],
                    ]);
                } else {
                    $cbQuestionnaireTranslations = $cbQuestionnaire->cbQuestionnaireTranslation()->create(
                        [
                            'language_code' => $translation['language_code'],
                            'content' => $translation['content'],
                            'accept' => $translation['accept'],
                            'ignore' => $translation['ignore'],
                        ]
                    );
                }
            }
            return $cbQuestionnaireTranslations;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setCbQuestionnaire(Request $request)
    {
        try {
            $cb = Cb::whereCbKey($request->json('cb_key'))->firstOrFail();

            if ($request->has('elements')){

                $configurations = $request->json('elements');

                $response = [];
                $cbQuestionnairesOld = [];
                $cbQuestionnairesNew = [];

                foreach ($configurations as $key => $configuration) {

                    $action = Action::whereCode($key)->firstOrFail();

                    //Get all of the existing CB Questionnaires to a collection to intersect with the ones
                    //that are being updated, and delete the ones that were unchecked by the user.
                    $cbQuestionnaires = $cb->cbQuestionnaire()->get();
                    if ($cbQuestionnaires) {
                        $cbQuestionnairesOld = $cbQuestionnaires->pluck('id');
                    }

                    //if is a vote action
                    if ($key == 'vote_event') {
                        foreach ($configuration as $voteKey => $item){

                            $cbQuestionnaire = $cb->cbQuestionnaire()
                                ->whereHas('cbQuestionnaireVote',function ($query) use($voteKey){
                                    $query->where('vote_event_key', '=', $voteKey);
                                })
                                ->first();

                            if (is_null($cbQuestionnaire)){
                                //creates a new Cb Questionnaire and the respective Cb Questionnaire Vote
                                $options = ['cb_questionnarie_key' => null ];
                                $newOrUpdatedCbQuestionnaire = $this->createOrUpdateCbQuestionnaire($cb, $action, $item, $options);
                                $cbQuestionnaireVote = $newOrUpdatedCbQuestionnaire->cbQuestionnaireVote()->create([
                                    'vote_event_key' => $voteKey
                                ]);
                            } else {
                                //updates the existing Cb Questionnaire
                                $options = ['cb_questionnarie_key' => $cbQuestionnaire->cb_questionnarie_key ];
                                $newOrUpdatedCbQuestionnaire = $this->createOrUpdateCbQuestionnaire($cb, $action, $item, $options);
                            }
                            if (isset($item['translations'])) {
                                //Create Or Update Cb Questionnaires Translations
                                $translations = $this->createOrUpdateTranslations($item['translations'], $newOrUpdatedCbQuestionnaire);
                            }
                            $response[] = $newOrUpdatedCbQuestionnaire;
                            $cbQuestionnairesNew[] = $newOrUpdatedCbQuestionnaire->id;
                        }

                    } else {
                        //create Or Update Cb Questionnaires
                        $newOrUpdatedCbQuestionnaire = $this->createOrUpdateCbQuestionnaire($cb, $action, $configuration);

                        if (isset($configuration['translations'])) {
                            //Create Or Update Cb Questionnaires Translations
                            $translations = $this->createOrUpdateTranslations($configuration['translations'], $newOrUpdatedCbQuestionnaire);
                        }
                        $response[] = $newOrUpdatedCbQuestionnaire;
                        $cbQuestionnairesNew[] = $newOrUpdatedCbQuestionnaire->id;
                    }
                }

                //-------------Delete the unchecked Cb Questionnaires
                $cbQuestionnairesToDelete = $cbQuestionnairesOld->diff($cbQuestionnairesNew);
                //Deletes the associated translations and the vote event relation in the database
                if (!$cbQuestionnairesToDelete->isEmpty()){
                    foreach ($cbQuestionnairesToDelete as $id){
                        $cbQuestionnaire = CbQuestionnaries::find($id);
                        if(!is_null($cbQuestionnaire)){
                            $cbQuestionnaire->cbQuestionnaireTranslation()->delete();
                            $cbQuestionnaire->cbQuestionnaireVote()->delete();
                        }
                    }
                    CbQuestionnaries::destroy($cbQuestionnairesToDelete);
                }
                //----------------------------------------------------

                return response()->json(['data' => $response], 200);
            }
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CbQuestionnaire not Found'], 404);
        }catch (Exception $e) {
            return response()->json($e->getMessage());
            return response()->json(['error' => 'Failed to Update the Cb Questionnaires'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCbQuestionnaireUser(Request $request){
        try {
            $userKey = $request->json('user_key');
            $cbQuestionnaireKey = $request->json('cb_questionnaire_key');

            if (is_null($userKey) || is_null($cbQuestionnaireKey)){
                return response()->json(['data' => null], 200);
            }

            $user = OrchUser::whereUserKey($userKey)->first();
            $questionnaire = CbQuestionnaries::whereCbQuestionnarieKey($cbQuestionnaireKey)->first();

            $ignore = $questionnaire->cbQuestionnairesUser()->whereUserId($user->id)->first();

            return response()->json(['data' =>$ignore], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CbQuestionnaireUser not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setCbQuestionnaireUser(Request $request){

        try {
            $user = OrchUser::whereUserKey($request->userKey)->first();

            $questionnaire = CbQuestionnaries::whereCbQuestionnarieKey($request->cbQuestionnaireKey)->first();

            $questionnaireUser = $questionnaire->cbQuestionnairesUser()->attach(
                [
                    $user->id => [
                        'date_ignore' => Carbon::now(),
                    ]
                ]
            );

            return response()->json(['data' => $questionnaireUser], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CbQuestionnaireUser not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserIgnoredQuestionnaires(Request $request){

        try {
            $userKey = ONE::verifyToken($request);
            $user = OrchUser::whereUserKey($userKey)->first();

            $ignoredQuestionnaires = $user->cbQuestionnaires()->get();

            $keys = collect();
            $questionnaires = collect();

            if ($ignoredQuestionnaires){
                $keys = $ignoredQuestionnaires->pluck('questionnarie_key')->unique();

                if(!$keys->isEmpty()){
                    $questionnaires = Q::getQuestionnaires($request, $keys);
                }
            }

            return response()->json(['data' => $questionnaires], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

}
