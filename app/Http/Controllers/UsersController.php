<?php
/**
 * Copyright (C) 2016 OneSource - Consultoria Informatica Lda <geral@onesource.pt>
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License as published by the Free
 * Software Foundation; either version 3 of the License, or (at your option) any
 * later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along
 * with this program; if not, see <http://www.gnu.org/licenses>.
 */

namespace App\Http\Controllers;
use App\ComModules\Orchestrator;
use App\Entity;
use App\One\One;
use App\One\OneLog;
use App\OrchUser;
use App\ParameterUserType;
use App\Sms;
use App\SocialNetwork;
use App\User;
use App\UserLoginLevel;
use App\UserParameter;
use App\UserQuestionnaireUniqueKey;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use DB;


use HttpClient;

/**
 * Class UsersController
 * @package App\Http\Controllers
 */


/**
 * Class UsersController
 * @package App\Http\Controllers
 */


/**
 * @SWG\Tag(
 *   name="User",
 *   description="Everything about User",
 * )
 *
 *  @SWG\Definition(
 *      definition="userErrorDefault",
 *      required={"error"},
 *      @SWG\Property( property="error", type="string", format="string")
 *  )
 *
 *  @SWG\Definition(
 *   definition="User",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(
 *           required={"name", "email", "password", "identity_card", "vat_number", "birthday"},
 *           @SWG\Property(property="name", format="string", type="string"),
 *           @SWG\Property(property="email", format="string", type="string"),
 *           @SWG\Property(property="password", format="password", type="string"),
 *           @SWG\Property(property="identity_card", format="string", type="string"),
 *           @SWG\Property(property="vat_number", format="string", type="string"),
 *           @SWG\Property(property="birthday", format="date", type="string")
 * )
 *   }
 * )
 *
 */

class UsersController extends Controller
{
    /**
     * Parameters required when storing a new User
     * @var array
     */
    protected $requiredParameters = ['name', 'email'];

    /**
     * Options paramaters of the User
     * @var array
     */
    protected $optionalParameters = ['surname','public_name','public_surname','public_email','role_id', 'street', 'city', 'zip_code', 'country', 'nationality', 'identity_card', 'identity_type', 'vat_number', 'phone_number', 'mobile_number', 'homepage', 'birthday', 'gender', 'marital_status', 'job', 'job_status', 'photo_id', 'photo_code', 'rfid', 'alphanumeric_code'];

    /**
     * Parameters required when storing a new User by ID
     * @var array
     */
    protected $idParameters = ['name', 'identity_card', 'parameters'];

    /**
     * List of Roles
     * @var array
     */
    protected $roles = [
        "ADMIN" => "admin",
        "MANAGER" => "manager",
        "USER" => "user"
    ];

    /**
     *
     * @SWG\Definition(
     *     definition="userAuthentication",
     *     required={"email", "password"},
     *     @SWG\Property( property="email", type="string", format="string"),
     *     @SWG\Property( property="password", type="string", format="string")
     * )
     *
     *  @SWG\Definition(
     *     definition="replyUserAuthentication",
     *     required={"token"},
     *     @SWG\Property( property="token", type="string", format="string")
     * )
     *
     * @SWG\Post(
     *  path="/auth/authenticate",
     *  summary="Authentication of a user",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"User"},
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="User login information",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/userAuthentication")
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *  @SWG\Parameter(
     *      name="X-AUTH-TOKEN",
     *      in="header",
     *      description="Authentication Token",
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=200,
     *      description="Authentication token for user",
     *      @SWG\Schema(ref="#/definitions/replyUserAuthentication")
     *  ),
     *  @SWG\Response(
     *      response="500",
     *      description="Could not create token",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="401",
     *      description="Invalid Credentials",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *   )
     * )
     *
     */

    /**
     * Authenticate a user from the received login and password.
     * Returns the JWTAuth Token
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticateUser(Request $request)
    {
        //Libertrium authentication check
        $siteKey  = !empty($request->header('X-SITE-KEY')) ? $request->header('X-SITE-KEY') : null;
        $libertriumServerLink = $this->checkLibertriumSiteConfiguration($siteKey);

        try {
            if (env('LDAP_AUTH', false)){
                $ldapConn = ldap_connect(env('LDAP_ADDRESS', '10.0.0.24'));
                if ($ldapConn) {
                    ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
                    $ldapBind = ldap_bind($ldapConn, env('LDAP_ADMIN', "cn=admin,dc=empatia-project,dc=eu"), env('LDAP_PASSWORD', "az10POI"));

                    if ($ldapBind) {
                        $baseDN = env('LDAP_BASEDN', 'ou=users,dc=empatia-project,dc=eu');
                        $filter = 'mail=' . $request->email;

                        $ldapSearch = ldap_search($ldapConn, $baseDN, $filter, ['cn']);
                        $ldapEntry = ldap_first_entry($ldapConn, $ldapSearch);

                        if ($ldapEntry) {
                            $userCN = ldap_get_values($ldapConn, $ldapEntry, 'cn')[0];
                            try { //LDAP AUTHENTICATION
                                $userBind = ldap_bind($ldapConn, "cn=" . $userCN . "," . $baseDN, $request->password);
                                try {
                                    $user = User::whereEmail($request->email)->firstOrFail();

                                    if ($user->confirmed) {
                                        $token = JWTAuth::fromUser($user);
                                        Redis::set($token, time());
                                        return response()->json(['token' => $token], 200);
                                    }

                                } catch (Exception $e) { //USER NOT FOUND IN DB
                                    $user = new User;
                                    $user->name = $userCN;
                                    $user->email = $request->email;
                                    $user->password = bcrypt(str_random(32));
                                    $user->confirmed = 1;

                                    do {
                                        $rand = str_random(32);

                                        if (!($exists = User::where('user_key', '=', $rand)->exists())) {
                                            $user->user_key = $rand;
                                        }
                                    } while ($exists);

                                    $user->save();

                                    $token = JWTAuth::fromUser($user);
                                    Redis::set($token, time());
                                    return response()->json(['token' => $token, 'user_key' => $user->user_key], 200);
                                }

                            } catch (Exception $e) { //LDAP AUTHENTICATION FAILED -> DB AUTHENTICATION
                            }
                        }
                    }
                }
            }

        } catch (Exception $e) {}

        try {

            //  Libertrium Verification

            if ($libertriumServerLink) {

                //Libertrium Remote Login Attempt
                $result = $this->libertriumLogin($request->email, $request->password, $libertriumServerLink);
                if ($result['success']) {

                    $libertriumAuthData = $result['libertriumAuthData'];
                    $libertriumUser = $libertriumAuthData['user'];

                    //Check user in our DB
                    $user = User::whereEmail($request->email)->first();

                    if (!empty($user)) {

                        //Libertrium OK - User in DB --> Update Pass and proceed Login
                        $user->password = bcrypt($request->password);
                        $user->save();
                    } else {

                        //Libertrium OK - but Not in DB --> Register New User
                        try {
                            $result = $this->storeNewLibertriumUser($libertriumUser, $request->password);

                            $credentials = array_merge($request->only('email', 'password'));
                            if ($token = JWTAuth::attempt($credentials)) {

                                Redis::set($token, time());
                                $timestamp = Redis::get($token);
                                $user = User::whereEmail($request->email)->firstOrFail();
                                $user->timeout = $timestamp + env('JWT_TIMEOUT', 1200);
                                $user->save();

                                //IF Registration succeed - register in Orchestrator on WUI
                                return response()->json(['token' => $token, 'libertrium' => true, 'user_key' => $user->user_key], 200);
                            }

                        } catch (Exception $e) {
                            return response()->json(['error' => 'Error Saving Libertrium User'], 500);
                        }
                    }
                }
            }
        }catch (Exception $e){
            return response()->json(['error' => 'Error on Libertrium Login'], 500);
        }


        try{

//            $credentials = array_merge($request->only('email', 'password'), ["confirmed" => true]);
            $credentials = array_merge($request->only('email', 'password'));
//            OneLog::info("Login done - Auth Module ");
            if ($token = JWTAuth::attempt($credentials)) {
                Redis::set($token, time());
                $timestamp = Redis::get($token);
                $user = User::whereEmail($request->email)->firstOrFail();
                $user->timeout = $timestamp + env('JWT_TIMEOUT', 1200);
                $user->save();

                if ($user->confirmed) {
                    $token = JWTAuth::fromUser($user);
                    Redis::set($token, time());
                    return response()->json(['token' => $token], 200);
                }

                return response()->json(['token' => $token, 'confirmation_code' => $user->confirmation_code], 200);
            }

            $user = User::whereEmail($request->email)->firstOrFail();

            if (Schema::hasTable("old_monza_users")) {
                $importedUser = DB::table("old_monza_users")->whereEmail($request->email)->first();

                if ($importedUser->old_crypt) {
                    $pass = md5($request->password);

                    if ($pass == $importedUser->password) {
                        $user->password = bcrypt($request->password);

                        $timestamp = Redis::get($token);
                        $user->timeout = $timestamp + env('JWT_TIMEOUT', 1200);
                        $user->save();

                        if ($user->confirmed) {
                            $token = JWTAuth::fromUser($user);
                            Redis::set($token, time());
                            return response()->json(['token' => $token], 200);
                        }

                        return response()->json(['token' => $token, 'confirmation_code' => $user->confirmation_code], 200);
                    }
                } else {
                    $pass = explode("$", $importedUser->password);
                    $hash = array_pop($pass);
                    $salt = implode("$", $pass);
                    $salt .= "$";
                    if (!(crypt($request->password, $salt) == $importedUser->password))
                        return response()->json(['error' => 'Invalid Credentials'], 401);

                    $user->password = bcrypt($request->password);

                    $timestamp = Redis::get($token);
                    $user->timeout = $timestamp + env('JWT_TIMEOUT', 1200);
                    $user->save();

                    if ($user->confirmed) {
                        $token = JWTAuth::fromUser($user);
                        Redis::set($token, time());
                        return response()->json(['token' => $token], 200);
                    }
                }

                return response()->json(['token' => $token, 'confirmation_code' => $user->confirmation_code], 200);
            }

        } catch (Exception $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }
        return response()->json(['error' => 'Invalid Credentials'], 401);
    }

    /**
     *
     * @SWG\Definition(
     *     definition="rfidAuthentication",
     *     required={"rfid"},
     *     @SWG\Property( property="rfid", type="string", format="string")
     * )
     *
     *  @SWG\Definition(
     *     definition="replyRfidAuthentication",
     *     required={"token"},
     *     @SWG\Property( property="token", type="string", format="string")
     * )
     *
     * @SWG\Post(
     *  path="/auth/authenticateRFID",
     *  summary="Authentication of a user by RFID",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"User"},
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="User RFID code",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/rfidAuthentication")
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=200,
     *      description="Authentication token for user",
     *      @SWG\Schema(ref="#/definitions/replyUserAuthentication")
     *  ),
     *  @SWG\Response(
     *      response="500",
     *      description="Could not create token",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="401",
     *      description="Invalid Credentials",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *   )
     * )
     *
     */

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticateRFID(Request $request) {
        try {
            $user = User::whereRfid($request->json("rfid"))
                ->whereConfirmed(true)->first();

            if (!is_null($user)) {
                if ($token = JWTAuth::fromUser($user)) {
                    Redis::set($token, time());
                    return response()->json(['token' => $token], 200);
                }
            }

        } catch (Exception $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json(['error' => 'Invalid Credentials'], 401);
    }

    /**
     *
     * @SWG\Definition(
     *     definition="alphanumericdAuthentication",
     *     required={"alphanumeric_code"},
     *     @SWG\Property( property="alphanumeric_code", type="string", format="string")
     * )
     *
     *  @SWG\Definition(
     *     definition="replyAlphanumericAuthentication",
     *     required={"token"},
     *     @SWG\Property( property="token", type="string", format="string")
     * )
     *
     * @SWG\Post(
     *  path="/auth/authenticateAlphanumeric",
     *  summary="Authentication of a user by an Alphanumeric code",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"User"},
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="User Alphanumeric code",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/alphanumericAuthentication")
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=200,
     *      description="Authentication token for user",
     *      @SWG\Schema(ref="#/definitions/replyUserAuthentication")
     *  ),
     *  @SWG\Response(
     *      response="500",
     *      description="Could not create token",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="401",
     *      description="Invalid Credentials",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *   )
     * )
     *
     */

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticateAlphanumeric(Request $request) {
        try {
            $user = User::whereAlphanumericCode($request->json("alphanumeric_code"))
                ->whereConfirmed(true)->first();

            if (!is_null($user)) {
                if ($token = JWTAuth::fromUser($user)) {
                    Redis::set($token, time());
                    return response()->json(['token' => $token], 200);
                }
            }

        } catch (Exception $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json(['error' => 'Invalid Credentials'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticateUserkey(Request $request) {
        try {
            $user = User::whereUserKey($request->json("user_key"))
                ->whereConfirmed(true)->first();

            if (!is_null($user)) {
                if ($token = JWTAuth::fromUser($user)) {
                    Redis::set($token, time());
                    return response()->json(['token' => $token], 200);
                }
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json(['error' => 'Invalid userKey'], 401);
    }

    //TODO: review function and compile swagger documentation
    /**
     * Logout a user with the received token
     * @param Request $request
     */
    public function logoutUser(Request $request)
    {
        try {
            Redis::del($request->header('X-AUTH-TOKEN'));
        } catch (Exception $e) {
            //
        }
    }

    /**
     *
     * @SWG\Definition(
     *     definition="validateUser"
     * )
     *
     *  @SWG\Definition(
     *     definition="replyValidateUser",
     *     required={"user_key"},
     *     @SWG\Property( property="user_key", type="string", format="string")
     * )
     *
     * @SWG\Get(
     *  path="/auth/validate",
     *  summary="Validation of a user",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"User"},
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *  @SWG\Parameter(
     *      name="X-AUTH-TOKEN",
     *      in="header",
     *      description="Authentication Token",
     *     required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=200,
     *      description="User Key of the user",
     *      @SWG\Schema(ref="#/definitions/replyValidateUser")
     *  ),
     *  @SWG\Response(
     *      response="400",
     *      description="Token Absent",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="401",
     *      description="Token Expired",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *   )
     * )
     *
     */
    /**
     * Validate a user from the received token.
     * Returns the User user_key
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateUser(Request $request)
    {
        try {
            if (empty($request->header('X-AUTH-TOKEN')))
                throw new Exception();
            if ($user = JWTAuth::setToken($request->header('X-AUTH-TOKEN'))->authenticate()) {
                $timestamp = Redis::get($request->header('X-AUTH-TOKEN'));

                /*if (is_null($timestamp)) {
                    //OneLog::error("Auth Token not found in Redis: " . $request->header('X-AUTH-TOKEN'));
                }elseif ($timestamp + env('JWT_TIMEOUT', 1200) <= time())
                    //OneLog::error("Auth Token Timeout: " . $request->header('X-AUTH-TOKEN'));*/

                if (!is_null($timestamp) && $timestamp + env('JWT_TIMEOUT', 1200) > time()) {
                    Redis::set($request->header('X-AUTH-TOKEN'), time());
                    return response()->json(['user_key' => $user->user_key], 200);
                } else {
                    return response()->json(['error' => 'Token Expired'], 401);
                }
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token Expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token Invalid'], $e->getStatusCode());
        } catch (Exception $e) {
            return response()->json(['error' => 'Token Absent'], 400);
        }
        return response()->json(['error' => 'User not Found'], 404);
    }

    /**
     * @param $confirmationCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmEmail($confirmationCode)
    {
        if (!is_null($confirmationCode)) {
            try {
                $user = User::whereConfirmationCode($confirmationCode)->first();

                if (!empty($user)) {
                    $user->confirmed = true;
                    $user->confirmation_code = null;
                    $user->save();
                } else {
                    $userParameter = UserParameter::with("user")->whereConfirmationCode($confirmationCode)->firstOrFail();
                    $userParameter->confirmation_code = null;
                    $userParameter->save();

                    $user = $userParameter->user;
                }

                $token = null;
                if (!is_null($user)) {
                    if ($token = JWTAuth::fromUser($user)) {
                        Redis::set($token, time());
                    }
                }

                return response()->json(['confirm' => 'Email verified', 'user' => $user, 'token' => $token], 200);
            } catch (Exception $e) {
                return response()->json(['error' => 'Could not verify the email address'], 404);
            }
        }
    }


    /**
     * Receive a array of user_keys and returns all information about that users
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListUsers(Request $request)
    {
        try {
            $users = User::with('userParameters')->whereIn('user_key', $request->json('userList'))->get();
            return response()->json($users, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error getting User List'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Receive a array of user_keys and returns all information about that users with status completed
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListUsersConfirmed(Request $request)
    {
        try {

            $userKey = $this->validateUser($request)->getData()->user_key;
            $users = User::whereIn('user_key', $request->json('userList'))
                ->whereConfirmed(1)->get();
            return response()->json($users, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error getting User List'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Return the user object from the received token.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser(Request $request)
    {
        try {
            if (empty($request->header('X-AUTH-TOKEN')))
                throw new Exception();

            if ($user = JWTAuth::setToken($request->header('X-AUTH-TOKEN'))->authenticate()) {
                $timestamp = Redis::get($request->header('X-AUTH-TOKEN'));
                if (!is_null($timestamp) && $timestamp + env('JWT_TIMEOUT', 60) > time()) {
                    Redis::set($request->header('X-AUTH-TOKEN'), time());
                    $user['user_parameters'] = $user->userParameters()->get()->groupBy('parameter_user_key');
                    $user['social_networks'] = $user->socialNetworks()->get();

                    if($request->withSms)
                        $user['sms_sent'] = $user->sms()->count();

                    return response()->json(['user' => $user], 200);
                } else {
                    return response()->json(['error' => 'Token Expired'], 401);
                }
            }else{
                return response()->json(1, 200);

            }
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token Expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token Invalid'], $e->getStatusCode());
        } catch (Exception $e) {
            return response()->json(['error' => 'Token Absent'], 400);
        }
        return response()->json(['error' => 'User not Found'], 404);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Http\JsonResponse|static[]
     */
    public function index(Request $request)
    {
        $validation = $this->validateUser($request);
        if (!empty($validation->getData()->user_key)) {
            $userKey = $validation->getData()->user_key;

            if (ONE::verifyRole($userKey, $request) === 'admin') {
                try {
                    $users = User::all();
                    return response()->json($users, 200);
                } catch (Exception $e) {
                    return response()->json(['error' => 'Failed to retrieve the users list'], 500);
                }
            }
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $validation;
    }

    public function getListNames(Request $request)
    {
        $validation = $this->validateUser($request);

        if (!empty($validation->getData()->user_key)) {
            try {
                $users = User::whereIn('user_key', $request->json('userList'))->select('user_key', 'name', 'photo_id', 'photo_code')->get()->keyBy('user_key');
                return response()->json(['data' => $users], 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'User not Found'], 404);
            } catch (Exception $e) {
                return response()->json(['error' => 'Failed to retrieve the user'], 500);
            }
        }
        return $validation;
    }

    /**
     * Only for Analytics use
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAnalyticsListNames(Request $request)
    {
        $validation = $this->validateUser($request);

        if (!empty($validation->getData()->user_key)) {
            try {
                $users = User::whereIn('user_key', $request->json('userList'))->select('user_key', 'street', 'gender', 'job', 'birthday')->get()->keyBy('user_key');
                return response()->json(['data' => $users], 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'User not Found'], 404);
            } catch (Exception $e) {
                return response()->json(['error' => 'Failed to retrieve the user'], 500);
            }
        }
        return $validation;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPublicListNames(Request $request)
    {
        try {
            $entityKey = $request->header('X-ENTITY-KEY','');

            $users = User::whereHas('orchUser.entities', function ($q) use ($entityKey) {
                $q->where("entity_key","=",$entityKey);
            })->whereIn('user_key', $request->json('userList'))
                ->select('user_key', 'name', 'public_name','surname','public_surname','photo_id', 'photo_code')
                ->get()
                ->keyBy('user_key');

            foreach ($users as $user) {
                if ($user->public_name!=1)
                    $user->name = "";

                if ($user->public_surname!=1)
                    $user->surname = "";
            }
            return response()->json(['data' => $users], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve the user'], 500);
        }
    }

    /**
     *
     *
     * @SWG\Definition(
     *    definition="replyUser",
     *    required={"data"},
     *    @SWG\Property(
     *      property="data",
     *      type="object",
     *      allOf={
     *         @SWG\Items(ref="#/definitions/User")
     *      })
     *  )
     *
     * @SWG\Get(
     *  path="/user/{user_key}",
     *  summary="Show a User",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"User"},
     *
     * @SWG\Parameter(
     *      name="user_key",
     *      in="path",
     *      description="User Key",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-AUTH-TOKEN",
     *      in="header",
     *      description="User Auth Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *
     *  @SWG\Response(
     *      response="200",
     *      description="Show the User data",
     *      @SWG\Schema(ref="#/definitions/replyUser")
     *  ),
     *
     *  @SWG\Response(
     *      response="404",
     *      description="User not Found",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *  ),
     *
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to retrieve User",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $userKeyReq
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $userKeyReq)
    {
        $validation = $this->validateUser($request);
        if (!(empty($validation->getData()->user_key))) {
            $userKey = $validation->getData()->user_key;
            try {
                $roleUserRequest = ONE::verifyRole($userKey, $request);
                if ($roleUserRequest === 'admin' || $roleUserRequest === 'manager' || $userKeyReq === $userKey) {
                    $user = User::where('user_key', '=', $userKeyReq)->firstOrFail();
                    $user['user_parameters'] = $user->userParameters()->get()->groupBy('parameter_user_key');

                    if($request->withSms)
                        $user['sms_sent'] = $user->sms()->count();

                    return response()->json($user, 200);
                }
                return response()->json(['error' => 'Unauthorized'], 401);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'User not Found'], 404);
            } catch (Exception $e) {
                return response()->json(['error' => 'Failed to retrieve the user'], 500);
            }
        }
        return $validation;
    }

    /**
     *
     * @SWG\Definition(
     *     definition="createUser",
     *
     *     required={
     *     "name",
     *     "email",
     *     "password",
     *     "identity_card",
     *     "vat_number",
     *     "birthday",
     *     },
     *
     *     @SWG\Property(
     *     property="name",
     *     type="string",
     *     format="string"
     * ),
     *     @SWG\Property(
     *     property="email",
     *     type="string",
     *     format="string"
     * ),
     *     @SWG\Property(
     *     property="password",
     *     type="string",
     *     format="password"
     * ),
     *     @SWG\Property(
     *     property="identity_card",
     *     type="string",
     *     format="string"
     * ),
     *     @SWG\Property(
     *     property="vat_number",
     *     type="string",
     *     format="string"
     * ),
     *     @SWG\Property(
     *     property="birthday",
     *     type="string",
     *     format="date"
     * )
     * )
     *
     *  @SWG\Definition(
     *     definition="replyCreateUser",
     *     required={"confirmation_code", "user"},
     *     @SWG\Property( property="confirmation_code", type="string", format="string"),
     *     @SWG\Property( property="user", type="object" )
     * )
     *
     * @SWG\Post(
     *  path="/auth",
     *  summary="Creation of a user",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"User"},
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="User registry information",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/createUser")
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=201,
     *      description="the newly created user",
     *      @SWG\Schema(ref="#/definitions/replyCreateUser")
     *  ),
     *  @SWG\Response(
     *      response="401",
     *      description="Invalid Credentials",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="409",
     *      description="ID number already exists | Email already exists",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to store new User",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function store(Request $request)
    {
        ONE::verifyKeysRequest(array_merge($this->requiredParameters, ['password']), $request);
        $siteKey  = !empty($request->header('X-SITE-KEY')) ? $request->header('X-SITE-KEY') : null;
        $libertriumServerLink = $this->checkLibertriumSiteConfiguration($siteKey);
    
        try {
            if(!is_null($request->json('identity_card')) && !empty($request->json('identity_card'))){
                if (User::whereIdentityCard($request->json('identity_card'))->exists()){
                    return response()->json(['error' => 'ID number already exists'], 409);
                }
            }

            if(!is_null($request->json('email')) && !empty($request->json('email'))){
                if (User::whereEmail($request->json('email'))->exists()){
                    $user = User::whereEmail($request->json('email'))->firstOrFail();
                }
                if ($libertriumServerLink){
                    if ($this->libertriumLoginExists( $request->json('email'),$libertriumServerLink)){
                        return response()->json(['error' => 'Email already exists in libertrium'], 499);
                    }
                }
            }

            /** Verify unique user parameters*/
            if(!empty($request->json('parameters'))){
                $unique = $this->verifyUserParameters($request->json('parameters'));
                if(empty($unique)){
                    return response()->json(['error' => 'Parameter need to be unique'], 408);
                };
            }

            if (!isset($user)) {
                $user = new User();
                $user->name = $request->json('name');
                $user->public_name = 1;
                $user->email = $request->json('email');
                $user->password = bcrypt($request->json('password'));

                foreach ($this->optionalParameters as $field) {
                    if (!empty($request->json($field))) {
                        $user->$field = $request->json($field);
                    }
                }

                if (!empty($request->json('age'))) {
                    $user->birthday = Carbon::now()->subYears($request->json('age'));
                }

                do {
                    $rand = str_random(32);

                    if (!($exists = User::where('user_key', '=', $rand)->exists())) {
                        $user->user_key = $rand;
                    }
                } while ($exists);


                $user->confirmation_code = str_random(64);

                $user->save();
            }

            if(!empty($request->json('parameters'))){
                foreach ($request->json('parameters') as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $parameterValue) {
                            $userParameter = $user->userParameters()->create([
                                'parameter_user_key' => $key,
                                'value' => $parameterValue,
                            ]);
                        }
                    } else {
                        $userParameter = $user->userParameters()->create([
                            'parameter_user_key' => $key,
                            'value' => $value,
                        ]);
                    }
                }
            }

            $user['user_parameters'] = $user->userParameters()->get()->groupBy('parameter_user_key');

            return response()->json(['confirmation_code' => $user->confirmation_code, 'user' => $user], 201);

        } catch (QueryException $e) {
            if ($e->getCode() == 23000)
                return response()->json(['error' => 'Email already exists'], 409);
            return response()->json(['error' => 'Failed to store new User'], 500);
        }
    }

    /**
     *
     * @SWG\Definition(
     *     definition="updateUser",
     *
     *     required={
     *     "name",
     *     "email",
     *     "password",
     *     "identity_card",
     *     "vat_number",
     *     "birthday",
     *     },
     *
     *     @SWG\Property(
     *     property="name",
     *     type="string",
     *     format="string"
     * ),
     *     @SWG\Property(
     *     property="email",
     *     type="string",
     *     format="string"
     * ),
     *     @SWG\Property(
     *     property="password",
     *     type="string",
     *     format="password"
     * ),
     *     @SWG\Property(
     *     property="identity_card",
     *     type="string",
     *     format="string"
     * ),
     *     @SWG\Property(
     *     property="vat_number",
     *     type="string",
     *     format="string"
     * ),
     *     @SWG\Property(
     *     property="birthday",
     *     type="string",
     *     format="date"
     * )
     * )
     *
     *  @SWG\Definition(
     *     definition="replyUpdateUser",
     *     required={"user"},
     *     @SWG\Property( property="user", type="object" )
     * )
     *
     * @SWG\Put(
     *  path="/auth/{user_key}",
     *  summary="Update a user",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"User"},
     *  @SWG\Parameter(
     *      name="Body",
     *      in="body",
     *      description="User update information",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/createUser")
     *  ),
     *     @SWG\Parameter(
     *      name="user_key",
     *      in="path",
     *      description="User Key",
     *     required=true,
     *      type="string"
     *  ),
     *  @SWG\Parameter(
     *      name="X-AUTH-TOKEN",
     *      in="header",
     *      description="Authentication Token",
     *     required=true,
     *      type="string"
     *  ),
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=200,
     *      description="the newly created user",
     *      @SWG\Schema(ref="#/definitions/replyCreateUser")
     *  ),
     *  @SWG\Response(
     *      response="403",
     *      description="Trying to update profile of different user",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *   ),
     *     @SWG\Response(
     *      response="404",
     *      description="User not Found",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *  ),
     *     @SWG\Response(
     *      response="409",
     *      description="ID number already exists | Email already exists",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="500",
     *      description="error",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *  )
     * )
     *
     */

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $userKeyReq
     * @return \Illuminate\Http\JsonResponse
     * @internal param $userKey
     */
    public function update(Request $request, $userKeyReq)
    {
        $validation = $this->validateUser($request);

        if (!(empty($validation->getData()->user_key))) {
            $userKey = $validation->getData()->user_key;

            try {
                if ($userKey == $userKeyReq || ONE::verifyRole($userKey, $request) === 'admin' || ONE::verifyRole($userKey, $request) === 'manager') {

                    $user = User::where('user_key', '=', $userKeyReq)->firstOrFail();

                    if(!is_null($request->json('identity_card')) && !empty($request->json('identity_card'))){
                        if (User::where('user_key', '!=', $user->user_key)->whereIdentityCard($request->json('identity_card'))->exists()){
                            return response()->json(['error' => 'ID number already exists'], 409);
                        }
                    }

                    if(!is_null($request->json('email')) && !empty($request->json('email'))){
                        if (User::where('user_key', '!=', $user->user_key)->whereEmail($request->json('email'))->exists()){
                            return response()->json(['error' => 'Email already exists'], 409);
                        }
                    }

                    /** Verify unique user parameters*/
                    if(!empty($request->json('parameters'))){
                        $unique = $this->verifyUserParameters($request->json('parameters'),$user);
                        if(empty($unique)){
                            return response()->json(['error' => 'Parameter need to be unique'], 500);
                        };
                    }

                    $newEmail = 0;
                    foreach (array_merge($this->requiredParameters, $this->optionalParameters) as $field) {
                        if(!is_null($request->json($field))){
                            if (($field == 'email') && ($user->email != $request->json($field))){
                                $user->confirmed = 0;
                                $user->confirmation_code = str_random(64);
                                $newEmail = 1;
                            }
                            $user->$field = $request->json($field);
                        }
                    }

                    if (!empty($request->json('age'))) {
                        $user->birthday = Carbon::now()->subYears($request->json('age'));
                    }

                    if (!empty($request->json('confirmed'))) {
                        if (ONE::verifyRole($userKey, $request) === 'admin') {
                            $user->confirmed = $request->json('confirmed');
                        }
                    }

                    if (!empty($request->json('password'))) {
                        $user->password = bcrypt($request->json('password'));
                    }
                    $user->save();

                    $parametersOld = $user->userParameters()->pluck('id');
                    $parametersNew = [];

                    if(!empty($request->json('parameters'))){
                        foreach ($request->json('parameters') as $key => $value) {
                            if (is_array($value)) {
                                $valuesOld = UserParameter::whereParameterUserKey($key)->whereUserId($user->id)->pluck('id');
                                $valuesNew = [];

                                foreach ($value as $parameterValue) {
                                    if (!UserParameter::whereParameterUserKey($key)->whereUserId($user->id)->whereValue($parameterValue)->exists()){
                                        $userParameter = $user->userParameters()->create([
                                            'parameter_user_key' => $key,
                                            'value' => $parameterValue,
                                        ]);
                                    } else {
                                        $userParameter = UserParameter::whereParameterUserKey($key)->whereUserId($user->id)->whereValue($parameterValue)->firstOrFail();
                                    }
                                    $valuesNew[] = $userParameter->id;
                                    $parametersNew[] = $userParameter->id;
                                }
                            } else {
                                if (UserParameter::whereParameterUserKey($key)->whereUserId($user->id)->exists()){
                                    $userParameter = UserParameter::whereParameterUserKey($key)->whereUserId($user->id)->firstOrFail();
                                    if($value == ""){
                                        $userParameter->deleted_at = Carbon::now();
                                    }else{
                                        $userParameter->value = $value;
                                    }

                                    $userParameter->save();
                                } else {
                                    $parameterUserType = ParameterUserType::with("parameterType")->whereParameterUserTypeKey($key)->first();
                                    if (!empty($parameterUserType) && $parameterUserType->parameterType->code=="email")
                                        $confirmationCode = str_random(64);
                                    else
                                        $confirmationCode = null;

                                    $userParameter = $user->userParameters()->create([
                                        'parameter_user_key' => $key,
                                        'value' => $value,
                                        'confirmation_code' => $confirmationCode
                                    ]);
                                }
                                $parametersNew[] = $userParameter->id;
                            }
                        }
                    }
                    $user['user_parameters'] = $user->userParameters()->get()->groupBy('parameter_user_key');

                    if($request->withSms)
                        $user['sms_sent'] = $user->sms()->count();

                    //Sets the new email flag
                    $user->new_email = $newEmail;

                    return response()->json($user, 200);
                } else {
                    return response()->json(['error' => 'Trying to update profile of different user'], 403);
                }
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'User not Found'], 404);
            } catch (Exception $e) {
                return response()->json(['error' => $e], 500);
            }
        }
        return $validation;
    }

    /**
     *
     * @SWG\Definition(
     *     definition="deleteUser"
    )
     *
     *  @SWG\Definition(
     *     definition="replyDeleteAuthentication",
     *     required={"string"},
     *     @SWG\Property( property="string", type="string", format="string")
     * )
     *
     * @SWG\Delete(
     *  path="/auth/{user_key}",
     *  summary="Delete a user",
     *  produces={"application/json"},
     *  consumes={"application/json"},
     *  tags={"User"},
     *
     *     @SWG\Parameter(
     *      name="user_key",
     *      in="path",
     *      description="User Key",
     *     required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-MODULE-TOKEN",
     *      in="header",
     *      description="Module Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Parameter(
     *      name="X-AUTH-TOKEN",
     *      in="header",
     *      description="Authentication Token",
     *      required=true,
     *      type="string"
     *  ),
     *
     *  @SWG\Response(
     *      response=200,
     *      description="OK",
     *      @SWG\Schema(ref="#/definitions/replyDeleteAuthentication")
     *  ),
     *  @SWG\Response(
     *      response="500",
     *      description="Failed to delete the user",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *  ),
     *  @SWG\Response(
     *      response="401",
     *      description="Unauthorized",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *   ),
     *  @SWG\Response(
     *      response="404",
     *      description="User not Found",
     *      @SWG\Schema(ref="#/definitions/userErrorDefault")
     *  ),
     * )
     *
     */

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $userKeyReq
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $userKeyReq)
    {
        $validation = $this->validateUser($request);

        if (!(empty($validation->getData()->user_key))) {
            $userKey = $validation->getData()->user_key;

            try {
                if ($userKey == $userKeyReq || ONE::verifyRole($userKey, $request) === 'admin' || ONE::verifyRole($userKey, $request) === 'manager') {
                    $user = User::where('user_key', '=', $userKeyReq)->firstOrFail();
                    $user->delete();
                    return response()->json('OK', 200);
                }
                return response()->json(['error' => 'Unauthorized'], 401);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'User not Found'], 404);
            } catch (Exception $e) {
                return response()->json(['error' => 'Failed to delete the user'], 500);
            }
        }

        return $validation;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeSocial(Request $request)
    {
        ONE::verifyKeysRequest(array_merge($this->requiredParameters, ['code', 'social_id']), $request);

        try {
            $user = new User();
            $user->name = $request->json('name');
            $user->email = $request->json('email');
            $user->password = bcrypt(str_random(10));

            foreach ($this->optionalParameters as $field) {
                if (!empty($request->json($field))) {
                    $user->$field = $request->json($field);
                }
            }

            if (!empty($request->json('age'))) {
                $user->birthday = Carbon::now()->subYears($request->json('age'));
            }

            do {
                $rand = str_random(32);

                if (!($exists = User::where('user_key', '=', $rand)->exists())) {
                    $user->user_key = $rand;
                }
            } while ($exists);

            $user->confirmed = 1;

            $user->save();

            $user->socialNetworks()->create([
                'code' => $request->json('code'),
                'social_id' => $request->json('social_id')
            ]);

            return response()->json(['user' => $user], 201);

        } catch (QueryException $e) {
            if ($e->getCode() == 23000)
                return response()->json(['error' => 'Email already exists'], 409);

            return response()->json(['error' => 'Failed to store new User'], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticateSocial(Request $request)
    {
        try {
            //ONE::verifyKeysRequest(array_merge($this->requiredParameters, ['code', 'social_id', 'input_token', 'name', 'email']), $request);

            $credentials = array_merge($request->only('social_id', 'input_token', 'name', 'email', 'app_secret', 'app_id'), ["confirmed" => true]);

            try {
                $user = User::whereEmail($credentials['email'])->firstOrFail();
                $socialId = SocialNetwork::where('social_id', '=', $request->social_id)->first();

                if(!is_null($socialId) && !is_null($user)){
                    $token = JWTAuth::fromUser($user);

                    Redis::set($token, time());

                    return response()->json(['token' => $token,'user_key' => $user->user_key, 'login' => 1], 200);

                }else{
                    return response()->json(['error' => 'Email exists but is missing facebook authentification'], 401);
                }
            } catch (Exception $e) { //USER NOT FOUND IN DB - SocialNetwork table

                $user = User::whereEmail($credentials['email'])->get();

                if(!$user->isEmpty()){
                    $user = $user->first();
                    $user->confirmed = 1;

                    $user->save();

                    $user->socialNetworks()->create([
                        'code' => $request->json('code'),
                        'social_id' => $request->json('social_id')
                    ]);

                    $token = JWTAuth::fromUser($user);

                    Redis::set($token, time());
                    return response()->json(['token' => $token, 'user_key' => $user->user_key, 'login' => 1], 200);
                }

                $user = new User();
                $user->name = $credentials['name'];

                if(!empty($user->email)){
                    return response()->json(['error' => 'Email not found'], 404);
                }
                $user->email = $credentials['email'];
                $user->password = bcrypt(str_random(10));

                foreach ($this->optionalParameters as $field) {
                    if (!empty($request->json($field))) {
                        $user->$field = $request->json($field);
                    }
                }

                if (!empty($request->json('age'))) {
                    $user->birthday = Carbon::now()->subYears($request->json('age'));
                }

                do {
                    $rand = str_random(32);

                    if (!($exists = User::where('user_key', '=', $rand)->exists())) {
                        $user->user_key = $rand;
                    }
                } while ($exists);

                $user->confirmed = 1;

                $user->save();

                $user->socialNetworks()->create([
                    'code' => $request->json('code'),
                    'social_id' => $request->json('social_id')
                ]);

                $token = JWTAuth::fromUser($user);

                Redis::set($token, time());
                return response()->json(['token' => $token, 'user_key' => $user->user_key, 'login' => 0], 200);
            }

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Could not crate Token'], 500);
        }
        return response()->json(['error' => 'Invalid Credentials'], 401);
    }

    public function registerFacebookAccount(Request $request){
        try {
            $user = User::whereUserKey($request->user_key)->firstOrFail();

            $userVerifyEmail = User::whereEmail($request->email)->first();

            if (empty($userVerifyEmail) || $user->email == $userVerifyEmail->email){

                $socialId = SocialNetwork::whereSocialId($request->social_id)->first();

                if (empty($socialId)) {
                    $socialId = $user->socialNetworks()->create([
                        'code'      => $request->json('code'),
                        'social_id' => $request->json('social_id')
                    ]);

                } else
                    return response()->json(['error' => 'Facebook account already exists'], 500);
            }else
                return response()->json(['error' => 'Email already exists'], 500);


            return response()->json($socialId, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }catch (Exception $e) {
            return response()->json(['error' => 'Facebook account already exists'], 500);
        }

        return response()->json(['error' => 'Invalid Credentials'], 401);
    }

    public function removeFacebookAccount(Request $request){

        try {
            $user = User::whereUserKey($request->user_key)->firstOrFail();

            $social = $user->socialNetworks()->where('user_id', '=', $user->id)->forceDelete();
            $social = SocialNetwork::where('user_id', '=', $user->id)->forceDelete();

            return response()->json('OK', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['error' => 'Invalid Credentials'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeByID(Request $request)
    {
        ONE::verifyKeysRequest($this->idParameters, $request);

        try {
            if(!User::whereIdentityCard($request->json('identity_card'))->exists()) {

                $user = new User();
                $user->name = $request->json('name');
                $user->email = is_null($request->json('email')) ? $request->json('identity_card') . '@empatia.pt' : $request->json('email');
                $user->password = bcrypt(str_random(10));
                $user->identity_card = $request->json('identity_card');
                $user->confirmed = true;

                do {
                    $rand = str_random(32);
                    if (!($exists = User::whereUserKey($rand)->exists())) {
                        $user->user_key = $rand;
                    }
                } while ($exists);

                $user->save();

                foreach ($request->json('parameters') as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $parameterValue) {
                            $userParameter = $user->userParameters()->create([
                                'parameter_user_key' => $key,
                                'value' => $parameterValue,
                            ]);
                        }
                    } else {
                        $userParameter = $user->userParameters()->create([
                            'parameter_user_key' => $key,
                            'value' => $value,
                        ]);
                    }
                }

                return response()->json($user, 201);
            }
            return response()->json(['error' => 'ID number already exists'], 409);

        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to store new User'], 500);
        }
    }

    /**
     * Return the user object from the received token.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserParameters(Request $request, $userKey)
    {
        try {
            //TODO verify Him or Manager or Admin

            $user = User::whereUserKey($userKey)->first();
            $user['user_parameters'] = $user->userParameters()->get()->groupBy('parameter_user_key');

            return response()->json(['user' => $user], 200);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token Expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token Invalid'], $e->getStatusCode());
        } catch (Exception $e) {
            return response()->json(['error' => 'Token Absent'], 400);
        }
        return response()->json(['error' => 'User not Found'], 404);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recoverPassword(Request $request)
    {
        try {
            if(!is_null($request->json('email'))){
                $user = User::whereEmail($request->json('email'))->firstOrFail();
            } else {
                $user = User::whereUserKey($request->json('user_key'))->firstOrFail();
            }

            if(is_null($request->json('recover_password_token'))){
                $user->recover_password_token = str_random(64);
                $user->save();
                return response()->json($user, 200);
            }

            if($user->recover_password_token == $request->json('recover_password_token')){
                if(!is_null($request->json('password'))){
                    $user->password = bcrypt($request->json('password'));
                    $user->recover_password_token = null;
                    $user->save();
                } else {
                    return response()->json(['error' => 'Password not defined'], 409);
                }
            } else {
                return response()->json(['error' => 'Recover Token Invalid'], 409);
            }

            return response()->json($user, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        }catch (QueryException $e) {
            return response()->json(['error' => 'Failed to Update Password'], 500);
        }
        return response()->json(['error' => 'User not Found'], 404);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request)
    {
        try {
            $userKey = $this->validateUser($request)->getData()->user_key;

            if (!empty($request->json('user_key')) && ((ONE::verifyRole($userKey, $request) === 'admin') || (ONE::verifyRole($userKey, $request) === 'manager'))){
                $userKey = $request->json('user_key');
                $user = User::whereUserKey($userKey)->firstOrFail();

                $user->password = bcrypt($request->json('password'));
                $user->save();
            } else {
                $user = User::whereUserKey($userKey)->firstOrFail();

                if (password_verify($request->json('old_password'), $user->password)) {
                    $user->password = bcrypt($request->json('password'));
                    $user->save();
                } else {
                    return response()->json(['error' => 'Wrong Password'], 409);
                }
            }

            return response()->json($user, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        }catch (QueryException $e) {
            return response()->json(['error' => 'Failed to Update Password'], 500);
        }
        return response()->json(['error' => 'User not Found'], 404);
    }

    public function searchEmail(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            return response()->json([$user], 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Entity not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve Total of Users'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function emailExists(Request $request)
    {
        try {
            User::whereEmail($request->email)->firstOrFail();
            return response()->json(["exists"=>true], 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(["exists"=>false], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve data'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSmsToken(Request $request)
    {
        try {
            $validation = $this->validateUser($request);

            if (!(empty($validation->getData()->user_key))) {
                $userKey = $validation->getData()->user_key;

                $user = User::whereUserKey($userKey)->firstOrFail();
                $user->sms_token = random_int (000000, 999999);
                $user->save();

                return response()->json($user->sms_token, 200);
            }
        }catch (ModelNotFoundException $e) {
            return response()->json(["exists"=>false], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve data'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function setSmsAttempt(Request $request)
    {
        $userKey = ONE::verifyToken($request);
        try{

            $user = User::whereUserKey($userKey)->firstOrFail();
            if(!empty($user)) {
                $sms = new Sms();
                do {
                    $rand = str_random(32);

                    if (!($exists = Sms::where('sms_key', '=', $rand)->exists())) {
                        $sms->sms_key = $rand;
                    }
                } while ($exists);

                $sms->user_id = $user->id;

                $sms->save();

                return response()->json($sms, 200);
            }


        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 500);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve data'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function resetNumberSms(Request $request)
    {
        ONE::verifyToken($request);
        try{
            $userKey = $request->user_key;

            $user = User::whereUserKey($userKey)->firstOrFail();
            if(!empty($user)) {
                Sms::whereUserId($user->id)->delete();
            }
            return response()->json('Ok', 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 500);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve data'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateSmsToken(Request $request)
    {
        try {
            $validation = $this->validateUser($request);

            if (!(empty($validation->getData()->user_key))) {

                if(!is_null($request->json('sms_token'))){

                    $userKey = $validation->getData()->user_key;
                    $user = User::whereUserKey($userKey)->firstOrFail();

                    if ($user->sms_token != $request->json('sms_token')){
                        return response()->json(['error' => 'Invalid SMS Token'], 500);
                    }

                    $user->sms_token = null;
                    $user->save();
                    return response()->json('OK', 200);
                }
            }
        }catch (ModelNotFoundException $e) {
            return response()->json(["exists"=>false], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve data'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /** Set user public parameter
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setPublicParameter(Request $request)
    {
        try {
            $validation = $this->validateUser($request);
            if (!(empty($validation->getData()->user_key))) {

                ONE::verifyKeysRequest(['parameter_key','value'], $request);
                $userKey = $validation->getData()->user_key;
                $user = User::whereUserKey($userKey)->firstOrFail();
                switch ($request->json('parameter_key')){
                    case 'name':
                        $user->public_name = $request->json('value');
                        $user->save();
                        break;
                    case 'surname':
                        $user->public_surname = $request->json('value');
                        $user->save();
                        break;
                    case 'email':
                        $user->public_email = $request->json('value');
                        $user->save();
                        break;
                    default:
                        $userParameter = $user->userParameters()->whereParameterUserKey($request->json('parameter_key'))->first();
                        if(!empty($userParameter)){
                            $userParameter->public_parameter = $request->json('value');
                            $userParameter->save();
                            $userParameter->save();
                        }
                        break;
                }
                return response()->json('OK', 200);
            }
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 500);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve data'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);


    }


    /** Check if parameter is Unique
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyUniqueParameter(Request $request)
    {
        try {
            ONE::verifyKeysRequest(['parameter_user_key','value'], $request);
            $parametersKey = $request->json('parameter_user_key');

            /** check if parameter is defined as unique*/
            $parameterUnique = Orchestrator::verifyUniqueParameterUserType($parametersKey);
            if(empty($parameterUnique)){
                /** Parameter is not defined as unique, the validation is ok*/
                return response()->json(1, 200);
            }
            $validation = $this->validateUser($request);
            $isUnique = 1;
            /** if user is authenticated verify parameter in other users else verify all*/
            if (!(empty($validation->getData()->user_key)))
            {
                $userKey = $validation->getData()->user_key;
                $user = User::whereUserKey($userKey)->firstOrFail();
                $userParameterTypeUnique = UserParameter::whereParameterUserKey($parametersKey)->whereValue($request->json('value'))->whereNotIn('user_id', [$user->id])->first();
            }
            else{
                $userParameterTypeUnique = UserParameter::whereParameterUserKey($parametersKey)->whereValue($request->json('value'))->first();
            }

            if (isset($userParameterTypeUnique)) {
                $isUnique = 0;
            }
            return response()->json($isUnique, 200);

        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve data'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }




    /** Verify unique user parameters
     * @param $parameters
     * @param null $user
     * @return bool
     */
    private function verifyUserParameters($parameters, $user = null)
    {
        $parametersKeys = array_keys($parameters);
        $parametersUnique = Orchestrator::verifyUniqueParameterUserType($parametersKeys);

        foreach ($parameters as  $key => $value) {
            if($value == "")
                continue;
            if (!is_array($value) && !empty($parametersUnique) && array_key_exists($key, $parametersUnique) && $parametersUnique->{$key}) {
                if(empty($user)){
                    $userParameterTypeUnique = UserParameter::whereParameterUserKey($key)->whereValue($value)->first();
                }else{
                    $userParameterTypeUnique = UserParameter::whereParameterUserKey($key)->whereValue($value)->whereNotIn('user_id', [$user->id])->first();
                }
                /** if one user as same value in parameter, validation is not ok*/
                if (isset($userParameterTypeUnique)) {
                    return false;
                }
            }
        }
        /** validation is ok for all parameters*/
        return true;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserList(Request $request)
    {
        $validation = $this->validateUser($request);
        try{
            if ($validation){
                $userKey = $validation->getData()->user_key;
                if ((ONE::verifyRole($userKey, $request) === 'admin') || (ONE::verifyRole($userKey, $request) === 'manager')) {

                    $tableData = $request->json('tableData') ?? null;

                    $entity = Entity::whereEntityKey($request->header('X-ENTITY-KEY'))->firstOrFail();
                    $role = $request->json('role');
                    if (is_null($role))
                        $usersList = $entity->users()->where('role', '!=', 'admin')->pluck('role', 'user_key');
                    else
                        $usersList = $entity->users()->whereRole($role)->pluck('role', 'user_key');

                    $recordsTotal = $usersList->count();
                    $usersListKeys = $usersList->keys();

                    $query = User::whereIn('user_key', $usersListKeys)
                        ->orderBy($tableData['order']['value'], $tableData['order']['dir'])
                        ->skip($tableData['start'])
                        ->take($tableData['length'])
                        ->select('user_key', 'name', 'email', 'created_at', 'confirmed');

                    if(!empty($tableData['search']['value'])) {
                        $query = $query
                            ->where('name', 'like', '%'.$tableData['search']['value'].'%')
                            ->orWhere('email', 'like', '%'.$tableData['search']['value'].'%');
                    }

                    $users = $query->whereIn('user_key', $usersListKeys)->get();

                    foreach ($users as $user){
                        if ($usersListKeys->contains($user->user_key)){
                            $user->role = $usersList[$user->user_key];
                        }
                    }

                    return response()->json(['data' => $users, 'recordsTotal' => $recordsTotal]);
                }
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve data'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersToModerate(Request $request)
    {
        $validation = $this->validateUser($request);
        try{
            if ($validation){
                $userKey = $validation->getData()->user_key;
                if ((ONE::verifyRole($userKey, $request) === 'admin') || (ONE::verifyRole($userKey, $request) === 'manager')) {

                    $tableData = $request->json('table_data') ?? null;
                    $siteKey = $request->json('site_key') ?? null;
                    $usersList = Orchestrator::siteUsersToModerate($siteKey);

                    $data['recordsTotal'] = User::whereIn('user_key', $usersList)->whereConfirmed(1)->count();

                    $query = User::whereIn('user_key', $usersList)
                        ->orderBy($tableData['order']['value'], $tableData['order']['dir'])
                        ->whereConfirmed(1)
                        ->select('user_key', 'name', 'email', 'created_at', 'confirmed');

                    if(empty($tableData['search']['value'])){
                        $data['recordsFiltered'] = $query->count();

                        $data['users'] = $query
                            ->skip($tableData['start'])
                            ->take($tableData['length'])
                            ->get();
                    } else {
                        $query = $query->where(function ($subQuery) use($tableData){
                            $subQuery->where('name', 'like', '%'.$tableData['search']['value'].'%')
                                ->orWhere('email', 'like', '%'.$tableData['search']['value'].'%');
                        });

                        $data['recordsFiltered'] = $query->count();

                        $data['users'] = $query
                            ->skip($tableData['start'])
                            ->take($tableData['length'])
                            ->get();
                    }
                    return response()->json(['data' => $data]);
                }
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve data'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }



    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersToModerate2(Request $request)
    {
        $validation = $this->validateUser($request);
        try{
            if ($validation){
                $arguments = $request->input('arguments') ?? null;


                $userKey = $validation->getData()->user_key;
                if ((ONE::verifyRole($userKey, $request) === 'admin') || (ONE::verifyRole($userKey, $request) === 'manager')) {
                    $siteKey = $request->json('site_key') ?? null;
                    $usersList = Orchestrator::siteUsersToModerate($siteKey);

                    // Fake a list
                    // $usersList = ["JGVLUWSH1GkYGgtjjuUOwYgfhIAY2Ll5", "yPGApHfbOeJ3eW66CP29ByqT3VAUeCe1"];
                    $queryObj = User::whereIn('user_key', $usersList)/* ->orderBy($tableData['order']['value'], $tableData['order']['dir']) */
                    ->whereConfirmed(1)
                        ->select('user_key', 'name', 'email', 'created_at', 'confirmed');
                    if ($arguments) {
                        $queryObj->orderBy('name',$arguments['sortOrder']);
                        $queryObj->take($arguments['numberOfRecords']);
                    }
                    $data = $queryObj->get();

                    return response()->json(['data' => $data]);
                }
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve data'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUserParameters(Request $request)
    {
        try{

            $userKey = $request->json('user_key');
            $user = User::whereUserKey($userKey)->firstOrFail();
            $parameterIds = $request->json('parameters');
            foreach ($parameterIds as $parameterId){
                $user->userParameters($parameterId)->delete();
            }
            return response()->json('OK', 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve data'], 500);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }


    public function getUserAccordingToFields(Request $request)
    {
        try{
            if(strtoupper($request['search']) == 'EMAIL'){
                $user = User::where('email', $request['email'])->first();
            }
            if(strtoupper($request['search']) == 'PARAMETERS'){
                foreach($request['parameters'] as $key => $parameter){
                    $userParameterTypeUnique = UserParameter::whereParameterUserKey($key)->whereValue($parameter)->first();
                    if(!empty($userParameterTypeUnique)){
                        $user = $userParameterTypeUnique->user()->get();
                    }
                }
            }
            if(empty($user)){
                return response()->json(['error' => 'Failed to find the user'], 409);
            }
            return response()->json($user, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve data'], 500);
        }
    }

    /**
     *
     * Private function - Receives email and password and tries to login in Libertrium
     *
     *                  - Returns an array with boolean and user Libertrium Data
     *
     * @param $email
     * @param $password
     * @return array
     */
    private function libertriumLogin($email, $password, $libertriumServerLink){

        //Libertrium Authentication
        $dataToSend=[];

        try{

            //Libertrium Login Request
            $request = [
                'url' => $libertriumServerLink . '/auth/login',
                'params' => [
                    'email' => $email,
                    'password' => $password
                ],
            ];

            $response = HttpClient::POST($request);

            //Libertrium Logon OK - Return success and User Libertrium Data

            if ($response->statusCode() == 200) {

                $libertriumAuthData = json_decode($response->content(), true);

                if(!empty($libertriumAuthData)) {
                    $libertriumUserId = $libertriumAuthData['user']['_id'];

                    $dataToSend['libertriumAuthData'] = $libertriumAuthData;
                    $dataToSend['success'] = true;
                    return $dataToSend;

                }
            }

            //Libertrium Logon KO - Continue Regular Login

            $dataToSend['success'] = false;
            return $dataToSend;

        }catch (Exception $e){
            //Exception
        }
    }


    /**
     *
     * Private function - Receives email and password and tries to login in Libertrium
     *
     *                  - Returns an array with boolean and user Libertrium Data
     *
     * @param $email
     * @param $password
     * @return array
     */
    private function libertriumLoginExists($email, $libertriumServerLink){

        $userExists = false;
        try{

            //Libertrium Login Request
            $request = [
                'url' => $libertriumServerLink . '/api/users/existe',
                'params' => [
                    'email' => $email,
                ],
            ];

            $response = HttpClient::POST($request);

            //Libertrium Logon OK - Return success and User Libertrium Data

            if ($response->statusCode() == 200) {
                $userExists = true;
            }

            return $userExists;

        }catch (Exception $e){
            return false;
        }
    }

    /**
     *
     * private function - saves new user with confirmation
     *
     * @param $user
     * @param $password
     * @return array|bool
     */
    private function storeNewLibertriumUser($user, $password){

        $name = $user['nome'];
        $email = $user['email'];

        try {

            if(!is_null($email) && !empty($email)){
                if (User::whereEmail($email)->exists()){
                    return false;
                }
            }

            $user = new User();
            $user->name = $name;
            $user->public_name = 1;
            $user->email = $email;
            $user->password = bcrypt($password);
            $user->confirmed = 1;


            do {
                $rand = str_random(32);

                if (!($exists = User::where('user_key', '=', $rand)->exists())) {
                    $user->user_key = $rand;
                }
            } while ($exists);

            $user->save();
            return ['user' => $user];

        } catch (Exception $e) {
            //Exception
        }
    }


    /**
     *
     * Check site configurations for Libertrium Authentication
     * If exists returns Libertrium Server address, if doesn't exist returns false
     *
     * @param $siteKey
     * @return bool
     */
    private function checkLibertriumSiteConfiguration($siteKey){

        if(empty($siteKey))
            return false;

        try {
            $siteConfGroups = Orchestrator::getSiteConfGroups($siteKey);

            if(empty($siteConfGroups))
                return false;

            $element = null;

            foreach($siteConfGroups as $siteConfGroup){
                if ($siteConfGroup->code == 'libertriumAuth' && (isset($siteConfGroup->subgroup) && !empty($siteConfGroup->subgroup))){
                    foreach ($siteConfGroup->subgroup as $subgroup) {
                        if ($subgroup->code == 'boolean_libertrium_authentication'){
                            if(isset($subgroup->siteConfValues[0])) {
                                //If Libertrium Auth is ON - Sets $element to 1
                                if (!empty($element = json_decode($subgroup->siteConfValues[0]->value)));
                            }
                        }
                        //If Libertrium Auth is ON + Server Link is set returns Libertrium Server Link
                        if (($subgroup->code == 'libertrium_server_link') && !empty($element)){
                            if(isset($subgroup->siteConfValues[0])) {
                                if (!empty($subgroup->siteConfValues[0]->value)) {
                                    return $subgroup->siteConfValues[0]->value;
                                }
                            }
                        }
                    }
                }
            }
            return false;

        } catch (Exception $e) {
            //Exception
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function manuallyConfirmUserEmail(Request $request){
        try{
            $user = User::whereUserKey($request->input('user_key'))->firstOrFail();

            $user->confirmed = 1;
            $user->confirmation_code = null;
            $user->save();

            $userKeyAuth = ONE::verifyToken($request);
            $userKey = $request->input('user_key');
            $entityKey = $request->header('X-ENTITY-KEY');

            try{
                $user = OrchUser::whereUserKey($userKey)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'User not Found'], 404);
            }

            try{
                $entity = Entity::whereEntityKey($entityKey)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Entity not Found'], 404);
            }
            OrchUsersController::autoUpdateUserLoginLevels($user,$entity,$userKeyAuth);
            $userLoginLevels = UserLoginLevel::whereUserId($user->id)->get();

            return response()->json($user, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        } catch (Exception $e) {
            return response()->json($e->getMessage());
            return response()->json(['error' => 'Failed to confirm the user email'], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function manuallyConfirmUserSms(Request $request){
        try{
            $user = User::whereUserKey($request->input('user_key'))->firstOrFail();

            $user->	sms_token = null;
            $user->save();

            $userKeyAuth = ONE::verifyToken($request);
            $userKey = $request->input('user_key');
            $entityKey = $request->header('X-ENTITY-KEY');

            try{
                $user = OrchUser::whereUserKey($userKey)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'User not Found'], 404);
            }

            try{
                $entity = Entity::whereEntityKey($entityKey)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Entity not Found'], 404);
            }
            OrchUsersController::autoUpdateUserLoginLevels($user,$entity,$userKeyAuth);
            $userLoginLevels = UserLoginLevel::whereUserId($user->id)->get();

            return response()->json($user, 200);
        }catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        } catch (Exception $e) {
            return response()->json($e->getMessage());
            return response()->json(['error' => 'Failed to confirm the user sms'], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateUniqueKey(Request $request){
        try{
            $userKeys = $request->json('user_keys');
            $questionnaireKey = $request->json('questionnaire_key');

            $users = User::whereIn('user_key', $userKeys)->get();

            $data = [];
            $uniqueKeys = [];
            $response = [];

            foreach ($users as $user){
                $uniqueKey = '';
                do {
                    $rand = str_random(32);
                    if (!($exists = in_array($rand, $uniqueKeys)) && !($exists = UserQuestionnaireUniqueKey::where('unique_key', '=', $rand)->exists())) {
                        $uniqueKey = $rand;
                        $uniqueKeys[] = $rand;
                    }
                } while ($exists);

                $data[] = array(
                    'user_key' => $user->user_key,
                    'questionnaire_key' => $questionnaireKey,
                    'unique_key' => $uniqueKey,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                );

                $response[$user->user_key] = array(
                    'questionnaire_key' => $questionnaireKey,
                    'unique_key' => $uniqueKey,
                    'email' => $user->email
                );
            }

            UserQuestionnaireUniqueKey::insert($data);

            return response()->json($response, 201);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to generate user questionnaire unique key'], 500);
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyUniqueKey(Request $request){
        try{

            $userKey = $request->json('user_key');
            $questionnaireKey = $request->json('questionnaire_key');
            $uniqueKey = $request->json('unique_key');

            if($userKey && $questionnaireKey && $uniqueKey){
                $data = UserQuestionnaireUniqueKey::withTrashed()
                    ->with('user')
                    ->whereUserKey($userKey)
                    ->whereQuestionnaireKey($questionnaireKey)
                    ->whereUniqueKey($uniqueKey)
                    ->first();

                if ($data->user){

                    if (is_null($data->deleted_at)){

                        $user = $data->user;
                        if (!is_null($user)) {
                            if ($token = JWTAuth::fromUser($user)) {
                                Redis::set($token, time());
                                $data->delete();
                                return response()->json(['token' => $token, 'user' =>$user], 200);
                            }
                        }
                    } else {
                        return response()->json(['error' => 'URL already used'], 409);
                    }
                }
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to verify user questionnaire unique key'], 500);
        }
    }
}
