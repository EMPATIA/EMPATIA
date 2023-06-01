<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Helpers\HFrontend;
use App\Helpers\HKeycloak;
use App\Traits\HasLoginLevels;
use App\Traits\HasPermissions;
use App\Traits\KeycloakAuthenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasLoginLevels;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'parameters',
        'data',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     */
    /*protected $hidden = [
        'password',
        'remember_token',
    ];*/

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'data' => 'object',
        'parameters' => 'object',
    ];


    /**
     * Email keycloak link to change user password
     * @param string $uuid
     * @return bool
     */
    public function sendPasswordChangeRequest($uuid): bool
    {
        return HKeycloak::sendUserUpdatePasswordEmail($uuid);
    }

    /**
     * Update user data only in keycloak
     * @param array $data
     * @return bool
     */
    public function updateKeycloakData(array $data): bool
    {
        return HKeycloak::updateUser($data, auth()->user()->uuid);
    }

    /**
     * Update user data only in local database
     * @param array $parameters
     * @return bool
     */
    public function updateParameters(array $parameters): bool
    {
        $params = [];
        $configUserParameters = HFrontend::getConfigurationByCode('user_parameters');

        foreach ($configUserParameters as $userParameter) {
            $params[getField($userParameter, 'code')] = getField($parameters, 'parameters_' . getField($userParameter, 'code')) ?? getField($parameters, 'parameters.' . getField($userParameter, 'code'));
        }
        if ($this->update([
            'parameters' => $params ?? null])) {
            return true;
        }
        return false;
    }

    /**
     * Update user name only in local database
     * @param string $name
     * @return bool
     */
    public function updateUserName(string $name): bool
    {
        if ($this->update([
            'name' => $name ?? null])) {
            return true;
        }
        return false;
    }

    /**
     * Create user with keycloak retrived data
     * @param array $user
     * @return void
     */
    public static function createLocalUser(array $user): void
    {
        try {
            \App\Models\User::create([
                'name' => getField($user,"username"),
                'uuid' => getField($user,"id"),
                'email' => getField($user,"email"),

            ]);
         } catch (QueryException | \Throwable $e) {
            logError( $e->getMessage() );
         }
    }


    /**
     * Get local users with role
     * @param string $role
     * @return array
     */
    public static function getByRole(string $role):array
    {
        $usersWithRole = [];
        $keycloakUsers = collect(HKeycloak::getUsersByRole($role));
        $keycloakUUIDS = $keycloakUsers->pluck('id');
        $localUsers = User::whereIn('uuid', $keycloakUUIDS)->get();

        foreach ($localUsers ?? [] as $user){
            $commonUsers=$keycloakUsers->where("id", getField($user,"uuid"))->first();
                if (!empty($commonUsers)) {
                    $user->setKeycloakData($commonUsers);
                    array_push($usersWithRole, $user);
                }

        }
        return $usersWithRole;
    }

    /**
     * set data with key keycloakData
     * @param array $user
     * @return void
     */
    public function setKeycloakData(array $user)
    {
        data_set($this,"keycloakData",$user);

    }

    /**
     * Get full name using keycloakData  if not request keycloak user using uuid
     * @return string
     */
    public function getfullName():string
    {

        $keycloakData = data_get($this,"keycloakData");
        if (!empty($keycloakData)){
            $firstName = getField($keycloakData,"firstName", "User");
            $lastName = explode(" ", getField($keycloakData, 'lastName'," "));

            return $firstName . ' ' . end($lastName);
        }else{//if keycloakData doesn't exist gets info from keycloak
            $user = HKeycloak::getUser($this->uuid);

           $firstName = getField($user,"firstName", "User");
           $lastName = explode(" ", getField($user, 'lastName'," "));

           return $firstName . ' ' . end($lastName);
        }

    }

    /**   LOGIN LEVELS   **/

    /**
     * Check wether the user has filled the phone_number parameter.
     *
     * @return bool
     */
    public function checkLLFilledPhoneNumber() : bool
    {
        return !empty( data_get($this, 'parameters.phone_number') );
    }

    /**
     * Check wether the user has filled the nif parameter.
     *
     * @return bool
     */
    public function checkLLFilledNif() : bool
    {
        return !empty( data_get($this, 'parameters.nif') );
    }
}
