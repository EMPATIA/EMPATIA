<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use App\Topic;
use App\Entity;
use App\CbVote;
use App\One\One;
use App\OrchUser;
use App\ParameterUserType;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\UserParameter;
use Carbon\Carbon;

class SMSVoteController extends Controller {
    public function index(Request $request) {
        function validateCCFormat($cardNumber) {
            function getCCNumberFromChar($letter) {
                switch ($letter) {
                    case '0' : return 0;
                    case '1' : return 1;
                    case '2' : return 2;
                    case '3' : return 3;
                    case '4' : return 4;
                    case '5' : return 5;
                    case '6' : return 6;
                    case '7' : return 7;
                    case '8' : return 8;
                    case '9' : return 9;
                    case 'A' : return 10;
                    case 'B' : return 11;
                    case 'C' : return 12;
                    case 'D' : return 13;
                    case 'E' : return 14;
                    case 'F' : return 15;
                    case 'G' : return 16;
                    case 'H' : return 17;
                    case 'I' : return 18;
                    case 'J' : return 19;
                    case 'K' : return 20;
                    case 'L' : return 21;
                    case 'M' : return 22;
                    case 'N' : return 23;
                    case 'O' : return 24;
                    case 'P' : return 25;
                    case 'Q' : return 26;
                    case 'R' : return 27;
                    case 'S' : return 28;
                    case 'T' : return 29;
                    case 'U' : return 30;
                    case 'V' : return 31;
                    case 'W' : return 32;
                    case 'X' : return 33;
                    case 'Y' : return 34;
                    case 'Z' : return 35;
                }
            }
            
            $cardNumber=strtoupper($cardNumber);
            $sum = 0;
            $secondDigit = false;
            if(strlen($cardNumber) != 12)
                return false;
        
            for ($i = strlen($cardNumber)-1; $i >= 0; --$i){
                $value = getCCNumberFromChar($cardNumber[$i]);
                if ($secondDigit){
                    $value = $value* 2;
                    if ($value > 9)
                        $value = $value - 9;
                }
                $sum = $sum + $value;
                $secondDigit = !$secondDigit;
            }
            return ($sum % 10) == 0;
        }

        /* Hardcoded data */
        $ccUserParameterKey = env("SMSVOTE_CCParamKey");
        if (empty($ccUserParameterKey))
            return response()->json(["code"=>-11, "error" => "unable to get CC Parameter Key"],400);

        $phoneNumberParameterKey = env("SMSVOTE_PhoneNumberParamKey");
        if (empty($phoneNumberParameterKey))
            return response()->json(["code"=>-12, "error" => "unable to get Phone Number Parameter Key"],400);

        $fakeEmailDomain = env("SMSVOTE_FakeEmailDomain");
        if (empty($fakeEmailDomain))
            return response()->json(["code"=>-13, "error" => "unable to get Fake Email Domain"],400);

        $smsFormat = "/(.*\d)\s(.*\w)/";
        $smsFormat = "/(\d*)\s(.*)/";
        
        try{
            \DB::beginTransaction();
            $sms = $request->get("sms");
            $voteEventKey = $sms["event"]??"";

            /* Split SMS content */
            $smsContent = $sms["content"];
		\Log::info("SMS FULL MESSAGE: ". $smsContent);
            preg_match($smsFormat, $smsContent, $smsContentSplitted);

            if (count($smsContentSplitted)==3) {
                $phoneNumber = $sms["sender"];
                $topicNumber = $smsContentSplitted[1];
                $ccNumber = substr(str_replace(" ","", $smsContentSplitted[2]),0,12);

		\Log::info("SMS CC NUMBER: ". $ccNumber);

                // Validar formato do CC
                if (validateCCFormat($ccNumber)) {
                    /* Verify if the Vote Event exists */
                    $event = CbVote::whereVoteKey($voteEventKey)
                        ->with(["cb.topics" => function($q) use ($topicNumber) {
                            // $q->where("topic_number","=","5166516516");
                            $q->where("topic_number","=",$topicNumber);
                        }])
                        ->first();

                    if (!empty($event)) {
                        if (!empty($event->cb)) {
                            if ($event->cb->topics->count()==1) {
                                $topicKey = $event->cb->topics->first()->topic_key;
                                
                                /* Verify if user is registered */
                                $ccResults = UserParameter::whereValue($ccNumber)->whereParameterUserKey($ccUserParameterKey)->first();
                                $phoneNumberResults = UserParameter::whereValue($phoneNumber)->whereParameterUserKey($phoneNumberParameterKey)->first();
                                if (empty($ccResults) && empty($phoneNumberResults)) {
                                    /* Manually Register user */
                                    $userEmail = $ccNumber . '@' . $fakeEmailDomain;
                                    $entity = Entity::whereEntityKey($sms["entity_key"])->firstOrFail();

                                    do {
                                        $rand = str_random(32);
                    
                                        if (!($exists = User::where('user_key', '=', $rand)->exists()))
                                            $userKey = $rand;
                                    } while ($exists);

                                    $user = new User();
                                    $user->user_key = $userKey;
                                    $user->name = "Votante SMS (" . $ccNumber . ")";
                                    $user->public_name = 1;
                                    $user->email = $userEmail;
                                    $user->password = bcrypt($userEmail);
                                    $user->save();

                                    $user->userParameters()->create([
                                        "parameter_user_key" => $ccUserParameterKey,
                                        "value" => $ccNumber
                                    ]);
                                    $user->userParameters()->create([
                                        "parameter_user_key" => $phoneNumberParameterKey,
                                        "value" => $phoneNumber
                                    ]);

                                    $orchUser = OrchUser::create([
                                        'user_key' => $userKey
                                    ]);

                                    if ($orchUser->entities()->whereEntityId($entity->id)->exists())
                                        $orchUser->entities()->updateExistingPivot($entity->id, ['role' => 'user', 'status' => 'completed']);
                                    else
                                        $orchUser->entities()->attach($entity->id, ['role' => 'user', 'status' => 'completed']);
                                } elseif(!empty($ccResults))
                                    $user = $ccResults->user()->first();
                                else
                                    $user = $phoneNumberResults->user()->first();
                                
                                \DB::commit();
                                return response()->json(["code"=> 1, "user" => $user->user_key, "topic" => $topicKey, "event" => $event->vote_key],200);
                            } else
                                return response()->json(["code"=>-5, "error" => "invalid topic number"],400);
                        } else
                            return response()->json(["code"=>-4, "error" => "invalid cb"],400);
                    } else
                        return response()->json(["code"=>-3, "error" => "invalid vote event"],400);
                } else
                    return response()->json(["code"=>-2, "error" => "invalid cc format"],400);

            } else
                return response()->json(["code"=>-1, "error" => "invalid format"],400);
        } catch (Exception $e) {
\Log::info($e);
            return response()->json([
                'code' => -10,
                'error'=> 'Failed to retrieve Process SMS Vote',
                'e-line'=> $e->getLine(),
                'e-file'=> $e->getFile(),
                'e'=> $e
            ], 500);
        }
    }

    public function getSMSConfigurations(Request $request) {
        try {
            $site = One::getSite($request);

            $siteConfigurations = $site->configurationsValues()
                ->whereHas("siteConf", function($q) {
                    $q->whereIn("code",["sms_service_code","sms_service_username","sms_service_password","sms_service_sender_name"]);
                })
                ->with("siteConf")
                ->get()
                ->pluck("value","siteConf.code");
            
            return response()->json($siteConfigurations);
        } catch(Exception $e) {
            return response()->json(["error" => $e],500);
        }
    }

}
