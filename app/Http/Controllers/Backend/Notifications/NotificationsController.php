<?php

namespace App\Http\Controllers\Backend\Notifications;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Backend\Notifications\Email;
use App\Models\Backend\Notifications\SMS;
use App\Models\Backend\Notifications\Template;

class NotificationsController extends Controller
{
    /**
     * Replace tags in content
     * @param $tags
     * @param $content
     * @return mixed
     */
    public static function buildContents($tags, $content)
    {
        try {
            // FETCH IF THE CONTENT AS TAGS IN IT [TAG STRUCTURE => #TAG#]
            foreach ($tags as $tagCode => $tagValue) {
                $content = str_replace('#' . $tagCode . '#', $tagValue, $content);
            }
            return $content;

        } catch (\Exception $e) {
            return $content;
        }
    }

    /**
     * @param Template $template
     * @param $tags
     * @param $recipientEmail
     * @param int $recipientId
     * @param null $sender
     * @param null $recipientNumber
     * @param null $data
     * @return bool
     */
    public static function createNotification($template, $tags, $recipientEmail, $recipientId = 0, $sender = null, $recipientNumber = null, $data = null)
    {
        try {
            $currentUser = Auth::user();
            switch ($template->channel ?? null) {
                case 'sms':
                    $rawContent = getField($template, 'content.'.getLang()) ?? getField($template, 'content.en');

                    if( empty($rawContent) ){
                        logError("SMS content not found.");
                        return false;
                    }

                    $content = NotificationsController::buildContents($tags, $rawContent);

                    logDebug("[ID:{$currentUser->id}, Name:{getField($tags, 'name', '-')}] Creating SMS in the DB...");
                    DB::beginTransaction();
                    // CREATE THE SMS OBJECT
                    $dbSMS = SMS::create([
                        'phone_number' => $recipientNumber,
                        'user_id' => $recipientId ?? null,
                        'content' => $content,
                        'created_by' => $currentUser != null ? $currentUser->id : 0,
                        'template' => $template->code,
                        'data' => $data,
                    ]);
                    if (!empty($dbSMS)) {
                        DB::commit();
                        logDebug("[ID:{$currentUser->id}, Name:{$currentUser->name}] SMS created in the DB!");
                    }

                    if( !env('SMS_ENABLED', false) ){
                        logInfo("SMS sending disabled (env).");
                        return true;
                    }

                    if (!empty($dbSMS)) {
                        $sms = self::formatSMS($content, $recipientNumber);
                        if (!empty($sms)) {
                            logDebug("[ID:{$currentUser->id}, Name:{$currentUser->name}] JSON formatted with the following info: " . $sms);
                            $result = self::sendSMS($sms, env("SMS_API_LINK"), env("SMS_API_TOKEN_ID"), env("SMS_API_TOKEN_SECRET"));

                            if ($result['http_status'] != 201) { //Error
                                logError("[ID:{$currentUser->id}, Name:{$currentUser->name}] SMS wasn't sent!");

                            } else {  //Success
                                $result['server_response'] = json_decode($result['server_response']) ?? $result['server_response'];
                                logDebug("[ID:{$currentUser->id}, Name:{$currentUser->name}] SMS request sent to the SMS API. Updating SMS in the DB...");

                                $smsData = $dbSMS->data;
                                data_set($smsData, "request_result", $result);

                                DB::beginTransaction();
                                if ($dbSMS->update([
                                    'data' => $smsData ?? null,
                                    'message_id' => getField(collect($result['server_response'])->first(), 'id'),
                                    'sent' => true,
                                    'sent_at' => Carbon::now()->isoFormat('Y-MM-DD HH:mm:ss')
                                ])) {
                                    DB::commit();
                                    logDebug("[ID:{$currentUser->id}, Name:{$currentUser->name}] SMS updated in the DB!");
                                }
                            }
                            return true;
                        }
                    }
                    break;

                default: // AN EMAIL SENDS A INTERNAL MESSAGE AND VICE-VERSA
                    $rawSubject = getField($template, 'subject.'.getLang()) ?? getField($template, 'subject.en');
                    $rawContent = getField($template, 'content.'.getLang()) ?? getField($template, 'content.en');

                    if( empty($rawSubject) || empty($rawContent) ){
                        logDebug("EMAIL content/subject not found.");
                        return false;
                    }

                    $subject = NotificationsController::buildContents($tags, $rawSubject);
                    $content = NotificationsController::buildContents($tags, $rawContent);
                    // CREATE THE EMAIL OBJECT
                    $emailData = [
                        'from_email' => config('mail.from.address'),
                        'from_name' => $sender ?? config('mail.from.name'),
                        'user_email' => $recipientEmail,
                        'user_id' => $recipientId,
                        'subject' => $subject,
                        'content' => $content,
                        'data' => $data,
                        'template' => $template->id,
                        'created_by' => !empty($currentUser) ? $currentUser->id : 0,
                    ];
                    DB::beginTransaction();
                    if (Email::create($emailData)) {
                        DB::commit();
                        logDebug("Email created in DB");
                        return true;
                    }
                    break;
            }


        } catch (Exception|\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            logDebug($e->getMessage() .' at line '. $e->getLine());
            return false;
        }
        return false;
    }


    public static function formatSMS($content, $recipientNumber){
        logDebug("Formatting JSON to send to SMS API...");
        $message = array(
            array(
                'to' =>
                    array(
                        array(
                            'type' => 'INTERNATIONAL',
                            'address' => $recipientNumber,
                        ),
                    ),
                'routingGroup' => 'STANDARD',
                'encoding' => 'TEXT',
                'longMessageMaxParts' => 3,
                'body' => $content,
                'protocolId' => 'IMPLICIT',
                'messageClass' => 'SIM_SPECIFIC',
                'deliveryReports' => 'ALL',
            ),
        );

        return json_encode($message);
    }

    public static function sendSMS($sms, $url, $tokenID, $tokenSecret){
        $ch = curl_init( );
        $headers = array(
            'Content-Type:application/json',
            'Authorization:Basic '. base64_encode("$tokenID:$tokenSecret")
        );

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $sms );
        // Allow cUrl functions 20 seconds to execute
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 20 );
        // Wait 10 seconds while trying to connect
        curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );


        $output = array();
        $output['server_response'] = curl_exec( $ch );
        $curl_info = curl_getinfo( $ch );
        $output['curl_info'] = $curl_info;
        $output['http_status'] = $curl_info[ 'http_code' ];
        $output['error'] = curl_error($ch);
        curl_close( $ch );

        return $output;
    }

}
