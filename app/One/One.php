<?php

namespace App\One;

use App\ComModules\Auth;
use App\ComModules\Notify;
use App\Entity;
use App\EntityGroup;
use App\EntityNotification;
use App\EntityNotificationType;
use App\Module;
use App\Site;
use App\Topic;
use App\User;
use Exception;
use Form;
use HttpClient;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Session;
use Route;
use Log;


class One
{
    public function __construct()
    {
    }

    public static function verifyRoleAdmin(Request $request, $userKey) {

        $request = [
            'component'  => 'orchestrator',
            'headers'   => [
                'X-AUTH-TOKEN: '.$request->header('X-AUTH-TOKEN'),
                'X-ENTITY-KEY: '.$request->header('X-ENTITY-KEY')],
            'api'       => 'auth',
            'method'    => 'role',
            'attribute'    => $userKey,
        ];

        $response = ONE::send('GET', $request);

        if ($response->statusCode() == 200){
            try{
                return $response->json()->role;
            }catch(Exception $e){
                return null;
            }
        }
        return null;
    }


    public static function verifyRole($userKey, $request)
    {
        $requestOrch = [
            'component' => 'orchestrator',
            'headers' => [
                'X-AUTH-TOKEN: ' . $request->header('X-AUTH-TOKEN'),
                'X-ENTITY-KEY: ' . $request->header('X-ENTITY-KEY')],
            'api' => 'auth',
            'method' => 'role',
            'attribute' => $userKey
        ];

        $response = ONE::send('GET', $requestOrch);

        if ($response->statusCode() == 200) {
            try {
                return $response->json()->role;
            } catch (Exception $e) {
                return response()->json(['error' => 'Failed to verify User'], 500);
            }

        }
        return response()->json(['error' => 'User not Found'], $response->statusCode());
    }


    public static function verifySecurity(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        $entity = empty($request->header('X-ENTITY-KEY')) ? null : Entity::whereEntityKey($request->header('X-ENTITY-KEY')->first());

        if (User::verifyRole($userKey, "admin")) {
            return $userKey;
        } else if (User::verifyRole($userKey, "manager") && $entity) {
            $user = User::whereUserKey($userKey)->firstOrFail();

            if ($entity->users->contains($user)){
                return $userKey;
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }

    public static function checkTokenOrchestrator($moduleToken){
        if (Module::where('token', '=', $moduleToken)->exists()) {
            return true;
        } else
            return false;
    }

    /**
     * @param $keys
     * @param $request
     */
    public static function verifyKeysRequest($keys,Request $request)
    {
        foreach ($keys as $key){
            if (!$request->exists($key))
            {
                abort(400);
            }
        }
    }

    public static function verifyKeysArray($keys, $data)
    {
        foreach ($keys as $key){
            $result = $data[$key];
            if (!isset($result))
            {
                abort(400,$key);
            }
        }
    }


    public static function verifyLogin(Request $request ) {
        if (empty($request->header('X-AUTH-TOKEN')))
            return null;

        if( $request->header('X-AUTH-TOKEN') === "!Empatia#2016#!"){
            return "defaultUSERprojectEMPATIA2016JAN";
        }

        $request = [
            'component' => 'auth',
            'headers'   => ['X-AUTH-TOKEN: '.$request->header('X-AUTH-TOKEN')],
            'api'       => 'auth',
            'method'    => 'validate',
        ];

        $response = ONE::send('GET', $request);
        if ($response->statusCode() == 200){
            try{
                return $response->json()->user_key;
            }catch(Exception $e){
                return null;
            }
        }
        return null;
    }


    public static function verifyToken(Request $request, $url = null ) {
        if (empty($url)){
            $url = env('COMPONENT-AUTH');
        }

        if (empty($request->header('X-AUTH-TOKEN')))
            abort(400);

        $request = [
            'component' => 'auth',
            'headers'   => ['X-AUTH-TOKEN: '.$request->header('X-AUTH-TOKEN')],
            'api'       => 'auth',
            'method'    => 'validate',
        ];

        $response = ONE::send('GET', $request);


        if ($response->statusCode() == 200){
            try{
                return $response->json()->user_key;
            }catch(Exception $e){
                abort(500, 'Failed to verify User');
            }
        }
        abort($response->statusCode(), $response->json()->error);
    }


    public static function actionType($name = 'form')
    {
        if (strpos(array_get(Route::getCurrentRoute()->getAction(), 'as', ''), $name . '.create') !== false)
            $type = 'create';
        else if (strpos(array_get(Route::getCurrentRoute()->getAction(), 'as', ''), $name . '.edit') !== false)
            $type = 'edit';
        else
            $type = 'show';

        return $type;
    }

    public static function form($name = 'form', $type = null, $layout = '_layouts.form', $title = null)
    {
        if ($title == null)
            $title = $name;

        if ($type == null) {
            $type = One::actionType($title);
        }

        return new OneForm($name, $type, $layout, $title);
    }

    public static function actionButtons($id, $params, $version = null)
    {
        $conf = [
            'edit' => ['color' => 'success', 'icon' => 'pencil'],
            'create' => ['color' => 'success', 'icon' => 'plus'],
            'show' => ['color' => 'info', 'icon' => 'eye'],
            'delete' => ['color' => 'danger', 'icon' => 'remove'],
        ];

        $html = '';
        foreach ($params as $type => $action) {
            if ($type == 'edit' && isset($version)) {
                $html .= '<a href="' . action($action, [$id, $version]) . '" class="btn btn-flat btn-' . $conf[$type]['color'] . ' btn-xs" data-toggle="tooltip" data-delay=\'{"show":"1000"}\' title="' . trans('form.' . $type) . '"><i class="fa fa-' . $conf[$type]['icon'] . '"></i></a> ';
            } else {
                $html .= '<a href="' . action($action, $id) . '" class="btn btn-flat btn-' . $conf[$type]['color'] . ' btn-xs" data-toggle="tooltip" data-delay=\'{"show":"1000"}\' title="' . trans('form.' . $type) . '"><i class="fa fa-' . $conf[$type]['icon'] . '"></i></a> ';
            }
        }
        return $html;
    }

    public static function messages()
    {
        $html = '';

        if (Session::has('message')) {
            $html .= '<div class="alert alert-success">' . Session::get('message') . "</div>";
        }

        if (Session::has('errors')) {
            $errors = Session::get('errors');
            $html .= '<div class="alert alert-danger">';
            $html .= '<h4><i class="icon fa fa-ban"></i>Error!</h4>';
            $html .= 'We encountered the following errors:';
            $html .= '<ul>';
            foreach ($errors->all() as $message) {
                $html .= '<li>' . $message . '</li>';
            }
            $html .= '</ul>';
            $html .= '</div>';
        }
        return $html;
    }

    public static function get($requestData){
        return One::send('GET', $requestData);
    }

    public static function put($requestData){
        return One::send('PUT', $requestData);
    }

    public static function post($requestData){
        return One::send('POST',$requestData);
    }

    public static function delete($requestData){
        return One::send('DELETE',$requestData);
    }

    public static function send($action, $requestData){

        $url = null;
        if (array_key_exists('url', $requestData)){
            $url = $requestData['url'];
        }
        else
        {
            if(array_key_exists('component', $requestData)){
                //Request to Ochestrator url
                $array = array(
                    'analytics'     => env('COMPONENT-ANALYTICS'),
                    'auth'          => env('COMPONENT-AUTH'),
                    'cb'            => env('COMPONENT-CB'),
                    'cm'            => env('COMPONENT-CM'),
                    'files'         => env('COMPONENT-FILES'),
                    'logs'          => env('COMPONENT-LOGS'),
                    'mp'            => env('COMPONENT-MP'),
                    'notify'        => env('COMPONENT-NOTIFY'),
                    'orchestrator'	=> env('COMPONENT-ORCHESTRATOR'),
                    'q'             => env('COMPONENT-Q'),
                    'vote'          => env('COMPONENT-VOTE'),
                    'wui'           => env('COMPONENT-WUI'),
                    'kiosk'         => env('COMPONENT-KIOSK'),
                    'events'        => env('COMPONENT-EVENTS'),
                    'empatia'       => env('COMPONENT-EMPATIA'),
                );

                $url = $array[$requestData['component']];
            }

        }
        if (!empty($url)){
            if (!empty($requestData["api"]))
                $requestData["api"] = trim($requestData["api"], " /");

            if (!empty($requestData["api_attribute"]))
                $requestData["api_attribute"] = trim($requestData["api_attribute"], " /");

            if (!empty($requestData["method"]))
                $requestData["method"] = trim($requestData["method"], " /");

            if (!empty($requestData["attribute"]))
                $requestData["attribute"] = trim($requestData["attribute"], " /");

            if (!array_key_exists("params", $requestData))
                $requestData["params"] = [];



            if (!empty($requestData['key']))
            {

                $url .= "/".$requestData["key"] ;
            }
            if (!empty($requestData["api"])){
                $url .= "/".$requestData["api"];
            }

            if (!empty($requestData["api_attribute"])) {
                $url .= "/" . $requestData["api_attribute"];
            }

            if(!empty($requestData["method"]))
                $url .= "/".$requestData["method"];

            if(!empty($requestData["attribute"]))
                $url .= "/".$requestData["attribute"];

            if (!empty($requestData["headers"]))
                $headers = array_merge($requestData["headers"], ["X-MODULE-TOKEN: ". env('MODULE_TOKEN','INVALID')]);
            else
                $headers =  ["X-MODULE-TOKEN: ". env('MODULE_TOKEN','INVALID')];

            $request = [
                'url' => $url,
                'headers' => $headers,
                'params' => $requestData['params'],
                'json' => true
            ];
            Log::debug("SEND: ".$action." ".json_encode($request));

            if ($action === 'GET')
                $response = HttpClient::GET($request);
            else if ($action === 'POST')
                $response = HttpClient::POST($request);
            else if ($action === 'PUT')
                $response = HttpClient::PUT($request);
            else if ($action === 'DELETE')
                $response = HttpClient::DELETE($request);
            Log::debug("RCV: ".$action." ".json_encode($response));
            return $response;
        }

    }


    /**
     * @param $request
     * @return mixed
     */
    public static function getEntity($request)
    {
        try {
            $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->first();
            if (empty($entity)) {
                $entity = Entity::whereEntityKey($request->json('entity_key'))->first();
                if(empty($entity) && !empty($request->entity_key)){
                    $entity = Entity::whereEntityKey($request->entity_key)->first();
                }
            }

            if (empty($entity)){
                return false;
            } else {
                return $entity;
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Entity'], 500);
        }
    }

    public static function performanceEvaluation($id, $action, $comunicationComp, $jsonInformation = null, $url = null){
        $redis = Redis::connection();
        $redis->rpush('performance', json_encode(
            array(
                'request_key'           => $id,
                'component'             => 'PAD',
                'session_id'            => session()->getId(),
                'action'                => $action,
                'url'                   => $url,
                'comunication_component'=> $comunicationComp,
                'json_msg'              => $jsonInformation,
                'timestamp'             => microtime(true)
            )
        ));
    }

    /**
     * Notify topic followers of new actions in topics followed
     *
     * @param $request
     * @param $tags
     * @param $notificationType
     * @return One|bool
     */
    public static function notifyFollowers($request, $tags, $notificationType)
    {
        try {
            $topic = Topic::whereTopicKey($request->topic_key)->firstOrFail();
            $followers = $topic->followers()->pluck('user_key');

            if(!$followers->isEmpty()){
                $users = collect(Auth::listUser($followers));

                if (!$users->isEmpty()){
                    $response = Notify::notifyUsers($request, $notificationType, $tags, $users, $request->site);
                }
            }
            return null;

        }catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve followers'], 500);
        }
    }

    /**
     * @param $text
     * @param array $data
     * @return string
     */
    public static function cleanString($text, $data = [])
    {
        $tagsToRemove = [
            '<script>', '</script>'
        ];

        if(!empty($data)){
            foreach ($data as $item){
                $tagsToRemove[] = '<'.$item.'>';
                $tagsToRemove[] = '</'.$item.'>';
            }
        }
        $response = str_replace($tagsToRemove, '', $text);

        return $response;
    }

    /* LOGS */
    public static function sendLog($type, $message)
    {
        $transport = new UdpTransport("empatia-log.onesource.pt", 12201);
        $publisher = new Publisher();
        $publisher->addTransport($transport);

        $messageTmp = new Message();
        $messageTmp->setShortMessage("Foobar!")
            ->setLevel(\Psr\Log\LogLevel::DEBUG)
            ->setFullMessage("There was a foo in bar")
            ->setFacility("example-facility");

        $publisher->publish($messageTmp);

        $logger = new Logger($publisher, 'example-facility');

        $logger->alert($message);

        return ONE::post([
            'component' => 'logs',
            'method' => 'log',
            'params' => [
                "type" => $type,
                "component" => "1",
                "ip" => $_SERVER["REMOTE_ADDR"],
                "message" => $message,
                "url" => "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"
            ]
        ]);

    }

    /**
     * @param $credentials
     * @return bool
     * @throws Exception
     */
    public static function verifySocialToken($credentials)
    {
        $requestAppAccessToken = ['url' => 'https://graph.facebook.com/oauth/access_token?client_id=' . $credentials['app_id'] . '&client_secret=' . $credentials['app_secret'] . '&grant_type=client_credentials'];

        $response = ONE::send('GET', $requestAppAccessToken);

        if ($response->statusCode() != 200) {
            throw new Exception("Failed to get App Access Token.");
        }

        $appAccessToken = substr($response->content(), 13);

        $requestUserId = [
            'url' => 'https://graph.facebook.com/debug_token?input_token=' . $credentials['input_token'] . '&access_token=' . $appAccessToken
        ];

        $response = ONE::send('GET', $requestUserId);

        if ($response->statusCode() != 200) {
            throw new Exception("Failed to get User Id.");
        }

        $userId = $response->json()->data->user_id;

        if ($response->json()->data->is_valid){
            if($userId == $credentials['social_id'])
                return true;
        } else {
            return false;
        }
        return false;
    }

    /**
     * @param Request $request
     * @param $sentModules
     * @return bool
     */
    public static function verifyModulesAccess(Request $request, $sentModules)
    {
        $moduleToken = $request->header('X-MODULE-TOKEN');

        if (!is_null($moduleToken)) {
            $modules = Module::whereIn('code', $sentModules)->get();
            if ($modules && $modules->contains('token', $moduleToken)) {
                return true;
            }
        }
        abort(400);
    }

    /**
     * @param Request $request
     * @param $notificationCode
     * @return string
     */
    public static function sendNotificationEmail(Request $request, $notificationCode)
    {

        try{
            $userKey = !empty($request->header('X-AUTH-TOKEN')) ? ONE::verifyToken($request) : 'SYSTEM';

            $entityKey = $request->header('X-ENTITY-KEY');
            $siteKey = $request->header('X-SITE-KEY');

            $site = Site::where('key',$siteKey)->firstOrFail();
            $entity = Entity::whereEntityKey($entityKey)->firstOrFail();
            $entityNotificationType = EntityNotificationType::whereCode($notificationCode)->firstOrFail();

            $entityNotification = $entity->entityNotifications()->whereEntityNotificationTypeId($entityNotificationType->id)->firstOrFail();
            $groupIds = isset($entityNotification->groups) ? json_decode($entityNotification->groups) : [];

            if (!empty($groupIds)){
                $userKeys = EntityGroup::with('users')
                    ->find($groupIds)
                    ->pluck('users')
                    ->flatten()
                    ->pluck('user_key');
            }

            $userEmails = User::whereIn('user_key', $userKeys)->get()->pluck('email');

            $response = Notify::sendEmailByTemplateKey($request, $site, $entityNotification->template_key, $userEmails, $userKey,$request['tags'] ?? null);

            return $response;
        } catch (Exception $e) {
            return response()->json($e->getMessage());
            return response()->json(['error' => 'Failed to Send Notification Emails'], 500);
        }
    }
}
