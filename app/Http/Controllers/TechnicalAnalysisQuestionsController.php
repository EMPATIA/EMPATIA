<?php

namespace App\Http\Controllers;

use App\Cb;
use App\One\One;
use App\TechnicalAnalysisQuestion;
use App\Topic;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TechnicalAnalysisQuestionsController extends Controller
{
    protected $required = [
        'store' => [
            'cb_key',
            'translations'
        ],
        'update' => [
            'translations'
        ]
    ];

    /**
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $cbKey)
    {
        try {
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();

            $technicalAnalysisQuestions = $cb->technicalAnalysisQuestions()->get();

            foreach($technicalAnalysisQuestions as $technicalAnalysisQuestion){
                if (!($technicalAnalysisQuestion->translation($request->header('LANG-CODE')))) {
                    if (!$technicalAnalysisQuestion->translation($request->header('LANG-CODE-DEFAULT'))) {
                        if (!$technicalAnalysisQuestion->translation('en')) {
                            return response()->json(['error' => 'No translation found'], 404);
                        }
                    }
                }
            }
            return response()->json($technicalAnalysisQuestions, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Cb not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to show Technical Analysis Questions'], 500);
        }
    }

    /**
     * @param Request $request
     * @param $technicalAnalysisQuestionKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $technicalAnalysisQuestionKey)
    {
        try {
            $technicalAnalysisQuestion = TechnicalAnalysisQuestion::whereTechAnalysisQuestionKey($technicalAnalysisQuestionKey)->firstOrFail();

            if (!($technicalAnalysisQuestion->translation($request->header('LANG-CODE')))) {
                if (!$technicalAnalysisQuestion->translation($request->header('LANG-CODE-DEFAULT'))){
                    if (!$technicalAnalysisQuestion->translation('en')){
                        return response()->json(['error' => 'No translation found'], 404);
                    }
                }
            }
            return response()->json($technicalAnalysisQuestion, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Technical Analysis Question not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to show Technical Analysis Question'], 500);
        }
    }

    /**
     * @param $technicalAnalysisQuestionKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($technicalAnalysisQuestionKey)
    {
        try {
            $technicalAnalysisQuestion = TechnicalAnalysisQuestion::whereTechAnalysisQuestionKey($technicalAnalysisQuestionKey)->firstOrFail();
            $technicalAnalysisQuestion->translations();

            return response()->json($technicalAnalysisQuestion, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Technical Analysis Question not found'], 404);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //token verification
        $userKey = ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['store'], $request);

        try {
            $cbKey = $request->json('cb_key');
            $acceptable = $request->json('acceptable');
            $cb = Cb::whereCbKey($cbKey)->firstOrFail();

            $key = '';
            do {
                $rand = str_random(32);

                if (!($exists = TechnicalAnalysisQuestion::whereTechAnalysisQuestionKey($rand)->exists())) {
                    $key = $rand;
                }
            } while ($exists);

            $technicalAnalysisQuestion = $cb->technicalAnalysisQuestions()->create([
                'tech_analysis_question_key' => $key,
                'code' => $request->json('code'),
                'acceptable' => $acceptable,
                'created_by' => $userKey
            ]);

            foreach ($request->json('translations') as $translation) {
                if (isset($translation['language_code']) && isset($translation['question'])) {
                    $technicalAnalysisQuestionTranslation = $technicalAnalysisQuestion->technicalAnalysisQuestionTranslations()->create(
                        [
                            'language_code' => $translation['language_code'],
                            'question' => $translation['question']
                        ]
                    );
                }
            }
            return response()->json($technicalAnalysisQuestion,201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'TechnicalAnalysisQuestion not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to store TechnicalAnalysisQuestion'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $technicalAnalysisQuestionKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $technicalAnalysisQuestionKey)
    {
        ONE::verifyToken($request);
        ONE::verifyKeysRequest($this->required['update'], $request);

        try{
            $translationsOld = [];
            $translationsNew = [];

            $technicalAnalysisQuestion = TechnicalAnalysisQuestion::whereTechAnalysisQuestionKey($technicalAnalysisQuestionKey)->firstOrFail();

            $translationsId = $technicalAnalysisQuestion->technicalAnalysisQuestionTranslations()->get();
            foreach ($translationsId as $translationId){
                $translationsOld[] = $translationId->id;
            }

            foreach($request->json('translations') as $translation){
                if (isset($translation['language_code']) && isset($translation['question'])){
                    $technicalAnalysisQuestionTranslation = $technicalAnalysisQuestion->technicalAnalysisQuestionTranslations()->whereLanguageCode($translation['language_code'])->first();
                    if (empty($technicalAnalysisQuestionTranslation)) {
                        $technicalAnalysisQuestionTranslation = $technicalAnalysisQuestion->technicalAnalysisQuestionTranslations()->create(
                            [
                                'language_code' => $translation['language_code'],
                                'question'          => $translation['question']
                            ]
                        );
                    }
                    else {
                        $technicalAnalysisQuestionTranslation->question        = $translation['question'];
                        $technicalAnalysisQuestionTranslation->save();
                    }
                }
                $translationsNew[] = $technicalAnalysisQuestionTranslation->id;
            }

            $deleteTranslations = array_diff($translationsOld, $translationsNew);
            foreach ($deleteTranslations as $deleteTranslation) {
                $deleteId = $technicalAnalysisQuestion->technicalAnalysisQuestionTranslations()->whereId($deleteTranslation)->first();
                $deleteId->delete();
            }

            $technicalAnalysisQuestion->acceptable = $request->json('acceptable') ?? 0;
            $technicalAnalysisQuestion->save();

            return response()->json($technicalAnalysisQuestion, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Technical Analysis Question not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to update Technical Analysis Question'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $technicalAnalysisQuestionKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $technicalAnalysisQuestionKey)
    {
        ONE::verifyToken($request);

        try{
            $technicalAnalysisQuestion = TechnicalAnalysisQuestion::whereTechAnalysisQuestionKey($technicalAnalysisQuestionKey)->firstOrFail();
            $technicalAnalysisQuestion->technicalAnalysisQuestionTranslations()->delete();
            $technicalAnalysisQuestion->delete();

            return response()->json('Ok', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Technical Analysis Question not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete the Technical Analysis Question'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @param $cbKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function questionsAndExistenceOfTechnicalAnalysis(Request $request, $cbKey)
    {
        try {
            $topic = Topic::whereTopicKey($request->topicKey)->firstOrFail();            /* find correspondent key on topic table */
            $technicalAnalysis = $topic->technicalAnalysis()->first();

            if(empty($technicalAnalysis)){                  /* Technical Analsysis doesn't exists */
                $data['technicalAnalysisExists'] = false;

                $cb = Cb::whereCbKey($cbKey)->firstOrFail();

                $technicalAnalysisQuestions = $cb->technicalAnalysisQuestions()->get();

                foreach($technicalAnalysisQuestions as $technicalAnalysisQuestion){
                    if (!($technicalAnalysisQuestion->translation($request->header('LANG-CODE')))) {
                        if (!$technicalAnalysisQuestion->translation($request->header('LANG-CODE-DEFAULT'))) {
                            if (!$technicalAnalysisQuestion->translation('en')) {
                                return response()->json(['error' => 'No translation found'], 404);
                            }
                        }
                    }
                }

                $data['technicalAnalysisQuestions'] = $technicalAnalysisQuestions;
            }else {
                $data['technicalAnalysisExists'] = true;        /* Technical Analsysis already exists */
            }

            return response()->json($data, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Cb not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to show Technical Analysis Questions'], 500);
        }
    }

}
