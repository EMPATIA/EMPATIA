<?php

namespace App\Helpers;

use App\Helpers\HCache;
use Http;
use Cache;
use App\Models\User;

class HKeycloak
{
    private static array $REQUIRED_ACTIONS_AVAILABLE = [
        'UPDATE_PASSWORD',
        'CONFIGURE_TOTP',
        'terms_and_conditions',
        'UPDATE_PROFILE',
        'VERIFY_EMAIL',
        'delete_account',
        'update_user_locale,'
    ];

    /**
     * Authenticate in Keystone master realm to get access token to be able to access realm REST API
     * All REST API require the token for authentication.
     * The token by default in Keycloak has a timeout of 60s thus the token caching should be similar.
     *
     * @return string
     */
    public static function getToken(): string
    {
        $url = env('KEYCLOAK_BASE_URL') . '/realms/master/protocol/openid-connect/token';
        $username = env('KEYCLOAK_ADMIN_USERNAME');
        $password = env('KEYCLOAK_ADMIN_PASSWORD');

        try {
            return HCache::remember('keycloak_admin_token', null, function () use ($url, $username, $password) {
                logDebug("[getToken] Token not in cache");

                $response = Http::asForm()->post($url, [
                    'grant_type' => 'password',
                    'client_id' => 'admin-cli',
                    'username' => $username,
                    'password' => $password,
                ]);

                if ($response->failed()) {
                    logError("[getToken] Request error: " . $response->status());
                    return null;
                }

                return getField($response, "access_token", "");
            }, HCache::$TIMEOUT_KEYCLOAK_TOKEN) ?? '';
        } catch (\Exception|\Throwable $e) {
            logError("[getToken] Log error: " . $e->getMessage());
        }
        return "";
    }

    /**
     * Generate base URI for the REST API
     *
     * @return string
     */
    public static function getBaseUrl(): string
    {
        return env('KEYCLOAK_BASE_URL') . "/admin/realms/" . env('KEYCLOAK_REALM');
    }

    /**
     * Get users array from Keycloak
     *
     * @return array
     */
    public static function getUsers(): array
    {
        $url = self::getBaseUrl() . "/users";

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . self::getToken(),
            ])->get($url);

            if ($response->failed()) {
                logError("[getUsers] Request error: " . $response->status());
                return [];
            }

            return $response->json();
        } catch (\Exception|\Throwable $e) {
            logError("[getUsers] Error: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Get users with role from Keycloak
     *
     * @return array
     */
    public static function getUsersByRole(string $role): array
    {
        $url = self::getBaseUrl() . "/clients/".env("KEYCLOAK_AUTH_CLIENT_ID")."/roles/".$role."/users";

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . self::getToken(),
            ])->get($url);

            if ($response->failed()) {
                logError("[getUsers] Request error: " . $response->status());
                return [];
            }

            return $response->json();
        } catch (\Exception|\Throwable $e) {
            logError("[getUsers] Error: " . $e->getMessage());
        }

        return [];
    }


    /**
     * Get user with uuid from Keycloak
     *
     * @return array
     */
    public static function getUser($uuid): array
    {
        $url = self::getBaseUrl() . "/users/".$uuid;


        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . self::getToken(),
            ])->get($url);

            if ($response->failed()) {
                logError("[getUsers] Request error: " . $response->status());
                return [];
            }

            return $response->json();
        } catch (\Exception|\Throwable $e) {
            logError("[getUsers] Error: " . $e->getMessage());
        }

        return [];
    }



    /**
     * Get all groups from Keycloak
     *
     * @return array
     */
    public static function getGroups(): array
    {
        $url = self::getBaseUrl() . "/groups";

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . self::getToken(),
            ])->get($url);

            if ($response->failed()) {
                logError("[getUsers] Request error: " . $response->status());
                return [];
            }

            return $response->json();
        } catch (\Exception|\Throwable $e) {
            logError("[getUsers] Error: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Get all users from Keycloak group
     *
     * @return array
     */
    public static function getAllUsersFromGroup($groupId): array
    {
        $url = self::getBaseUrl() . "/groups/".$groupId."/members";

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . self::getToken(),
            ])->get($url);

            if ($response->failed()) {
                logError("[getUsers] Request error: " . $response->status());
                return [];
            }

            return $response->json();
        } catch (\Exception|\Throwable $e) {
            logError("[getUsers] Error: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Create user in Keycloak
     *
     * @param mixed $email
     * @param mixed $name
     * @param mixed $groups
     * @param mixed $emailVerified
     * @return bool
     */
    public static function createUser(string $email, string $name, array $groups, bool $emailVerified = false): bool
    {
        $url = self::getBaseUrl() . "/users";

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . self::getToken(),
            ])->post($url, [
                'enabled' => true,
                'email' => $email,
                'firstName' => $name,
                'emailVerified' => $emailVerified,
                'groups' => $groups,
            ]);

            if ($response->failed()) {
                logError("[createUser] Request error: " . $response->status());
                logError("[createUser] Error msg: " . $response->body());
                return false;
            }

            return true;
        } catch (\Exception|\Throwable $e) {
            logError("[createUser] Error: " . $e->getMessage());
        }

        return false;
    }

    public static function sendUserUpdatePasswordEmail(string $uuid): bool
    {
        $url = self::getBaseUrl() . "/users/" . $uuid . "/execute-actions-email";

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . self::getToken(),
            ])->put($url, ["UPDATE_PASSWORD"]);
            if ($response->failed()) {
                logError("[sendUserUpdatePasswordEmail] Request error: " . $response->status());
                logError("[sendUserUpdatePasswordEmail] Error msg: " . $response->body());
                return false;
            }

            return true;
        } catch (\Exception|\Throwable $e) {
            logError("[sendUserUpdatePasswordEmail] Error: " . $e->getMessage());
        }

        return false;
    }

    public static function getUserGroups($uuid): array
    {
        $url = self::getBaseUrl() . "/users/" . $uuid . "/groups";

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . self::getToken(),
            ])->get($url);

            if ($response->failed()) {
                logError("[getUserGroups] Request error: " . $response->status());
                return [];
            }

            return $response->json();
        } catch (\Exception|\Throwable $e) {
            logError("[getUserGroups] Error: " . $e->getMessage());
        }

        return [];
    }

    public static function importUsersFromDB(): array
    {
        $result = [];
        try {
            $users = User::all();
            foreach ($users as $user) {
                logDebug("[importUsersFromDB] importing user: " . getField($user, 'email'));
                $res = self::createUser(getField($user, 'email'), getField($user, 'name', ''), ['laravel-bo-user'], !empty(getField($user, 'email_verified_at')));
                if (!empty($res)) {
                    data_set($user, 'uuid', getField(collect(self::getUsers())->where('email', getField($user, 'email'))->first(), 'id'));
                    $user->update();
                }
                $result[getField($user, 'email')] = $res;
            }
        } catch (\Exception|\Throwable $e) {
            logError("[importUsersFromDB] Error: " . $e->getMessage());
        }

        return $result;
    }


    /**
     * Update users name in Keycloak
     * @param array $data
     * @return bool
     */
    public static function updateUser(array $data, $uuid): bool
    {
        $url = self::getBaseUrl() . "/users/" . $uuid;
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . self::getToken(),
            ])->put($url, [
                'enabled' => true,
                'firstName' => getField($data, 'firstName'),
                'lastName' => getField($data, 'lastName'),
            ]);
            if ($response->failed()) {
                logError("[updateUser] Request error: " . $response->status());
                logError("[updateUser] Error msg: " . $response->body());
                return false;
            }
            return true;
        } catch (\Exception|\Throwable $e) {
            logError("[createUser] Error: " . $e->getMessage());
        }

        return false;
    }
}
