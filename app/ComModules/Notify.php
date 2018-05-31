<?php

namespace App\ComModules;
use App\One\One;
use Exception;
use Illuminate\Http\Request;

class Notify
{
    /**
     * @param $request
     * @param $emailType
     * @param $tags
     * @param $users
     * @param $site
     * @return bool
     */

    public static function notifyUsers($request, $emailType, $tags, $users, $site)
    {
        if(isset($site['no_reply_email'])) {
            try{
                $response = ONE::post([
                    'component' => 'notify',
                    'api'       => 'email',
                    'method'    => 'emailSend',
                    'attribute' => $emailType,
                    'params'    => [
                        'no_reply'      => $site['no_reply_email'],
                        'sender_name'   => $site['name'],
                        'recipient'     => $users,
                        'content'       => $tags,
                        'user_key'      => isset($user["user_key"]) ? $user["user_key"] : null
                    ],
                    'headers' =>  [
                        "X-AUTH-TOKEN: ". $request->header('X-AUTH-TOKEN'),
                        "X-SITE-KEY: ". $request->header('X-SITE-KEY'),
                        "X-ENTITY-KEY: ". $request->header('X-ENTITY-KEY')
                    ]
                ]);

                return $response->json();
            } catch (Exception $e){
                return $e->getMessage();
            }
        }
        return false;
    }


    /**
     * @param $templateKey
     * @param $usersEmail
     * @param $user_key
     * @param $tags
     * @param null $entityKey
     * @param null $siteName
     * @param null $siteNoReplyEmail
     * @return mixed
     */
    public static function sendEmailForDeadlineNotification($templateKey, $usersEmail, $user_key, $tags, $entityKey = null, $siteName = null, $siteNoReplyEmail = null){

        $response = ONE::post([
            'component' => 'notify',
            'api' => 'email',
            'method' => 'send',
            'attribute' => $templateKey,
            'params' => [
                'no_reply' => $siteNoReplyEmail,
                'sender_name' => $siteName,
                'recipient' => $usersEmail,
                'content' => $tags,
                'user_key' => $user_key
            ],
            'headers' =>  [
                "X-ENTITY-KEY: ". $entityKey
            ]
        ]);
        return $response;
    }

    /**
     * @param Request $request
     * @param $typeKey
     * @param $siteKey
     * @param $translations
     * @return mixed
     * @throws Exception
     */
    public static function entityNotificationTemplates(Request $request, $typeKey, $siteKey, $translations)
    {
        $response = One::post([
            'component' => 'notify',
            'api'       => 'emailTemplate',
            'params'    => [
                'type_key'      => $typeKey,
                'site_key'      => $siteKey,
                'translations'  => $translations
            ],
            'headers' =>  [
                "X-AUTH-TOKEN: ". $request->header('X-AUTH-TOKEN')
            ]
        ]);

        if($response->statusCode()!= 201){
            throw new Exception(trans("comModulesNotify.failedSaveEmailTemplate"));
        }
        return $response->json();
    }

    /**
     * @param $typeCode
     * @return mixed
     * @throws Exception
     */
    public static  function getTypeKey($typeCode){
        $response = ONE::get([
            'component' => 'notify',
            'api' => 'type',
            'method' => 'getTypeKey',
            'params' => [
                'typeCode' => $typeCode
            ]
        ]);
        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesNotify.errorGettingTypeKey"));
        }
        return $response->json();
    }

    /**
     * @param $templateKey
     * @return mixed
     * @throws Exception
     */
    public static function getEmailTemplateTranslations($templateKey)
    {
        $response = One::get([
            'component' => 'notify',
            'api' => 'emailTemplate',
            'method' => 'edit',
            'api_attribute' => $templateKey
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesNotify.errorGettingTemplateEmailTranslations"));
        }
        return $response->json();
    }

    /**
     * @param Request $request
     * @param $typeKey
     * @param $templateKey
     * @param $translations
     * @return mixed
     * @throws Exception
     */
    public static function editEmailTemplate(Request $request, $typeKey, $templateKey, $translations){
        $response = ONE::put([
            'component' => 'notify',
            'api' => 'emailTemplate',
            'attribute' => $templateKey,
            'params' => [
                'type_key' => $typeKey,
                'translations' => $translations
            ],
            'headers' =>  [
                "X-AUTH-TOKEN: ". $request->header('X-AUTH-TOKEN')
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesNotify.errorUpdatingTemplate"));
        }
        return $response->json();
    }

    /**
     * @param Request $request
     * @param $site
     * @param $templateKey
     * @param $usersEmail
     * @param $userKey
     * @param null $tags
     * @return mixed
     * @throws Exception
     */
    public static function sendEmailByTemplateKey(Request $request, $site, $templateKey, $usersEmail, $userKey, $tags = null){
        $response = ONE::post([
            'component' => 'notify',
            'api'       => 'email',
            'method'    => 'createEmails',
            'params'    => [
                'to'            => $usersEmail,
                'tags'          => $tags,
                'no_reply'      => $site->no_reply_email,
                'user_key'      => $userKey,
                'sender_name'   => $site->name,
                'template_key'  => $templateKey
            ],
            'headers' =>  [
                "X-ENTITY-KEY: ". $request->header('X-ENTITY-KEY'),
                "LANG-CODE: ". $request->header('LANG-CODE'),
                "LANG-CODE-DEFAULT: ". $request->header('LANG-CODE-DEFAULT')
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesNotify.error_sending_email_by_template_key"));
        }
        return $response->json();
    }

    public static function getEmailTemplate($siteKey, $code){
        $response = One::get([
            'component' => 'notify',
            'api' => 'emailTemplate',
            'method' => 'getEmailTemplate',
            'params' =>[
                'siteKey' =>$siteKey,
                'code' => $code
            ]
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesNotify.errorGettingTemplateEmail"));
        }
        return $response->json();
    }

    /**
     * @param Request $request
     * @param $site
     * @param $templateKey
     * @param $usersEmail
     * @param $user_key
     * @param $tags
     * @return bool
     */
    public static function sendEmailByTemplate(Request $request, $site, $templateKey, $usersEmail, $user_key, $tags){

        if(isset($site->no_reply_email)) {
            $response = ONE::post([
                'component' => 'notify',
                'api' => 'email',
                'method' => 'send',
                'attribute' => $templateKey,
                'params' => [
                    'no_reply' => $site->no_reply_email,
                    'sender_name' => $site->name,
                    'recipient' => $usersEmail,
                    'content' => $tags,
                    'user_key' => $user_key
                ],
                'headers' =>  [
                    "X-ENTITY-KEY: ". $request->header('X-ENTITY-KEY'),
                    "LANG-CODE: ". $request->header('LANG-CODE'),
                    "LANG-CODE-DEFAULT: ". $request->header('LANG-CODE-DEFAULT')
                ]
            ]);
            return $response->json();
        }
        return false;
    }
}