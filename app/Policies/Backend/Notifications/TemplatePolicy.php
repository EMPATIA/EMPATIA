<?php

namespace App\Policies\Backend\Notifications;

use App\Models\Backend\Notifications\Template;
use App\Models\KeycloakUser;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TemplatePolicy
{
    use HandlesAuthorization;

    protected const PREFIX = 'notifications.template';

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
     * @param  KeycloakUser  $user
     * @return Response|bool
     */
    public function viewAny(KeycloakUser $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  KeycloakUser  $user
     * @param  Template  $template
     * @return Response|bool
     */
    public function view(KeycloakUser $user, Template $template)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  KeycloakUser  $user
     * @return Response|bool
     */
    public function create(KeycloakUser $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  KeycloakUser  $user
     * @param  Template  $template
     * @return Response|bool
     */
    public function update(KeycloakUser $user, Template $template)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  KeycloakUser  $user
     * @param  Template  $template
     * @return Response|bool
     */
    public function delete(KeycloakUser $user, Template $template)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  KeycloakUser  $user
     * @param  Template  $template
     * @return Response|bool
     */
    public function restore(KeycloakUser $user, Template $template)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  KeycloakUser  $user
     * @param  Template  $template
     * @return Response|bool
     */
    public function forceDelete(KeycloakUser $user, Template $template)
    {
        //
    }
}
