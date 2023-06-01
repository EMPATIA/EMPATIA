<?php

namespace App\Policies\Backend\Empatia;

use App\Models\KeycloakUser;
use App\Models\Empatia\LoginLevel;
use Illuminate\Auth\Access\HandlesAuthorization;

class LoginLevelPolicy
{
    use HandlesAuthorization;

    protected const PREFIX = 'empatia.loginLevel';

    /**
     * Perform pre-authorization checks.
     *
     * @param  KeycloakUser $user
     * @param  string $ability
     * @return void|bool
     */
    public function before(KeycloakUser $user, string $ability)
    {
        if ( $user->isAdmin() || $user->hasPermission(self::PREFIX.".$ability")) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\KeycloakUser  $keycloakUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(KeycloakUser $keycloakUser)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\KeycloakUser  $keycloakUser
     * @param  \App\Models\LoginLevel  $loginLevel
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(KeycloakUser $keycloakUser, LoginLevel $loginLevel)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\KeycloakUser  $keycloakUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(KeycloakUser $keycloakUser)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\KeycloakUser  $keycloakUser
     * @param  \App\Models\LoginLevel  $loginLevel
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(KeycloakUser $keycloakUser, LoginLevel $loginLevel)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\KeycloakUser  $keycloakUser
     * @param  \App\Models\LoginLevel  $loginLevel
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(KeycloakUser $keycloakUser, LoginLevel $loginLevel)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\KeycloakUser  $keycloakUser
     * @param  \App\Models\LoginLevel  $loginLevel
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(KeycloakUser $keycloakUser, LoginLevel $loginLevel)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\KeycloakUser  $keycloakUser
     * @param  \App\Models\LoginLevel  $loginLevel
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(KeycloakUser $keycloakUser, LoginLevel $loginLevel)
    {
        //
    }
}
