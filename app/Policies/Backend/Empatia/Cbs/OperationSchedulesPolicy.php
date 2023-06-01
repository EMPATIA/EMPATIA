<?php

namespace App\Policies\Backend\Empatia\Cbs;

use App\Models\Empatia\Cbs\OperationSchedule;
use App\Models\KeycloakUser;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class OperationSchedulesPolicy
{
    use HandlesAuthorization;

    protected const PREFIX = 'backend.empatia.cbs.operation-schedule';

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
     * @param  \App\Models\OperationSchedule  $operationSchedule
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(KeycloakUser $keycloakUser, OperationSchedule $operationSchedule)
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
     * @param  \App\Models\OperationSchedule  $operationSchedule
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(KeycloakUser $keycloakUser, OperationSchedule $operationSchedule)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\KeycloakUser  $keycloakUser
     * @param  \App\Models\OperationSchedule  $operationSchedule
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(KeycloakUser $keycloakUser, OperationSchedule $operationSchedule)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\KeycloakUser  $keycloakUser
     * @param  \App\Models\OperationSchedule  $operationSchedule
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(KeycloakUser $keycloakUser, OperationSchedule $operationSchedule)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\KeycloakUser  $keycloakUser
     * @param  \App\Models\OperationSchedule  $operationSchedule
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(KeycloakUser $keycloakUser, OperationSchedule $operationSchedule)
    {
        //
    }
}
