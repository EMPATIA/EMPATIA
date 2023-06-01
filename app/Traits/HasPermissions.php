<?php

namespace App\Traits;

use App\Models\Permission;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;


trait HasPermissions
{
    // TODO: may need improvements

    /**
     * Returns a collection of the user's permissions.
     *
     * @return Collection
     */
    public function permissions() : Collection
    {
        $roles = Auth::roles() ?? [];
        return Permission::all()->filter(function ($item) use ($roles) {
            return !empty( array_intersect($roles, $item->roles ?? []) );
        });
    }

    /**
     * Returns a user permission.
     *
     * @param string $key
     * @return Permission|null
     */
    public function permission(string $key) : ?Permission
    {
        return $this->permissions()->where('key', '=', $key)->first();
    }

    /**
     * Determine if the model may perform the given permission.
     *
     * @param string $key   The permission key.
     * @return bool
     */
    public function hasPermissionTo(string $key): bool
    {
        return $this->hasPermission($key);
    }

    /**
     * An alias to hasPermissionTo(), but avoids throwing an exception.
     *
     * @param string $permission
     * @return bool
     */
    public function checkPermissionTo(string $permission): bool
    {
        try {
            return $this->hasPermissionTo($permission);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Determine if the model has any of the given permissions.
     *
     * @param string|array ...$keys
     *
     * @return bool
     */
    public function hasAnyPermission(...$keys): bool
    {
        $keys = collect($keys)->flatten();

        foreach ($keys as $key) {
            if ($this->checkPermissionTo($key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the model has all the given permissions.
     *
     * @param string|array ...$keys
     *
     * @return bool
     */
    public function hasAllPermissions(...$keys): bool
    {
        $keys = collect($keys)->flatten();

        foreach ($keys as $key) {
            if (! $this->checkPermissionTo($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the model has the given permission.
     *
     * @param string $key
     * @return bool
     */
    public function hasPermission(string $key): bool
    {
        return !empty($this->permission($key));
    }
}
