<?php

namespace App\Policies;

use App\Tag;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TagPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create a tag
     *
     * NOTE: Because the admin role is the only role with permission to create tags, and because the admin role has
     * access to all endpoints through Gate::before in AuthServiceProvider, this will (currently) always return false
     *
     * @param  User $user
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('post-tags');
    }



    /**
     * Determine whether the user can update a tag
     *
     * NOTE: Because the admin role is the only role with permission to update tags, and because the admin role has
     * access to all endpoints through Gate::before in AuthServiceProvider, this will (currently) always return false
     * @param  User $user
     * @param  Tag  $tag
     */
    public function update(User $user, Tag $tag): bool
    {
        return $user->hasPermissionTo('put-tags');

    }



    /**
     * Determine whether the user can delete a tag
     *
     * NOTE: Because the admin role is the only role with permission to delete tags, and because the admin role has
     * access to all endpoints through Gate::before in AuthServiceProvider, this will (currently) always return false
     *
     * @param  User $user
     * @param  Tag  $tag
     */
    public function delete(User $user, Tag $tag): bool
    {
        return $user->hasPermissionTo('post-tags');
    }
}
