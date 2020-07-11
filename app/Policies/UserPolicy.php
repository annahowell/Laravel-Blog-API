<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view a list of users
     *
     * NOTE: Because the admin role is the only role with permission to manage users, and because the admin role has
     * access to all endpoints through Gate::before in AuthServiceProvider, this will (currently) always return false
     *
     * @param  User $user
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('manage-users');
    }


    /**
     * Determines whether a non admin user is attempting to view their own account details
     * @param  User $user
     * @param  User $userToDelete
     */
    public function view(User $user, User $userToDelete)
    {
        return $user->id == $userToDelete->id;
    }


    /**
     * Determine whether the user can view a list of roles
     *
     * NOTE: Because the admin role is the only role with permission to manage roles, and because the admin role has
     * access to all endpoints through Gate::before in AuthServiceProvider, this will (currently) always return false
     *
     * @param  User $user
     */
    public function viewRoles(User $user)
    {
        return $user->hasPermissionTo('manage-roles');
    }


    /**
     * Determine whether the user can view a list of roles
     *
     * NOTE: Because the admin role is the only role with permission to manage roles, and because the admin role has
     * access to all endpoints through Gate::before in AuthServiceProvider, this will (currently) always return false
     *
     * @param  User $user
     */
    public function update(User $user, User $userToUpdate)
    {
        return $user->id == $userToUpdate->id;
    }


    /**
     * Determines whether a non admin user is attempting to delete their own account
     * @param  User $user
     * @param  User $userToDelete
     */
    public function delete(User $user, User $userToDelete)
    {
        return $user->id == $userToDelete->id;
    }
}
