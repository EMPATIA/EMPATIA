<?php

namespace App\Models;

use App\Events\Empatia\Frontend\UserCreated;
use App\Traits\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Redirect;
use Auth;
use Exception;

class KeycloakUser implements Authenticatable, AuthorizableContract
{
    use Authorizable, HasPermissions;

    const DEFAULT_NAME = 'TMP';

    /**
     * Attributes we retrieve from Profile
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'name',
        'email_verified'
    ];

    /**
     * User attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Constructor
     *
     * @param array $profile Keycloak user info
     */
    public function __construct(array $profile)
    {

        // Save profile information in attribute
        foreach ($profile as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->attributes[$key] = $value;
            }
        }

        // Get user from database
        try {
            // by uuid | by email
            $user =
                User::whereUuid(getField($profile, 'sub'))->first() ??
                User::whereEmail(getField($profile, 'email'))->first();
        } catch (QueryException | Exception  | \Throwable $e) {
            logError($e->getMessage(), true, null, null, null, null, 0);
            // TODO: set flash message OR redirect to error page so user has links to navigate somewhere
            abort(401);
        }

        // If no user in database then create one
        if (empty($user)) {
            try {
                // Create new user
                $user = new User();
                $user->uuid     = getField($profile, 'sub');
                $user->email    = getField($profile, 'email');
                $user->name     = getField($profile, 'name', self::DEFAULT_NAME); # Remove when User model is updated

                $user->save();

                UserCreated::dispatch($user);

            } catch (QueryException | Exception  | \Throwable $e) {
                logError('Creating User in local DB: '.$e->getMessage(), true, null, null, null, null, 0);
                // TODO: set flash message OR redirect to error page so user has links to navigate somewhere
                abort(401);
            }
        }

        // Set ID attribute
        $this->attributes["id"] = $user->id;

        // Set user uuid from attribute
        $this->uuid = getField($profile, 'sub');

        // Set user id from attribute
        $this->id = $this->getKey();
    }

    /**
     * Magic method to get attributes
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->id;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->id;
    }

    /**
     * Check user has roles
     *
     * @param string|array $roles
     * @param string $resource
     * @return boolean
     * @see KeycloakWebGuard::hasRole()
     *
     */
    public function hasRole($roles, $resource = '')
    {
        return Auth::hasRole($roles, $resource);
    }

    /**
     * Check user has any of the roles
     *
     * @param  string|array  $roles
     * @param  string  $resource
     * @return boolean
     */
    public function hasAnyRole($roles, $resource = '')
    {
        $authRoles = Auth::roles($resource);

        foreach ((array)$roles as $role) {
            if (in_array($role, $authRoles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getAuthPassword()
    {
        throw new \BadMethodCallException('Unexpected method [getAuthPassword] call');
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getRememberToken()
    {
        throw new \BadMethodCallException('Unexpected method [getRememberToken] call');
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     * @codeCoverageIgnore
     */
    public function setRememberToken($value)
    {
        throw new \BadMethodCallException('Unexpected method [setRememberToken] call');
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getRememberTokenName()
    {
        throw new \BadMethodCallException('Unexpected method [getRememberTokenName] call');
    }

    /**
     * Whether the user is admin.
     *
     * @return bool
     */
    public function isAdmin() : bool
    {
        return $this->hasAnyRole(['admin', 'laravel-admin']);
    }
}
