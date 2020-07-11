<?php

namespace App\Policies;

use App\Post;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create a post
     * @param  User $user
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('post-posts');
    }


    /**
     * Determine whether the user can update a post
     * @param  User $user
     * @param  Post $post
     */
    public function update(User $user, Post $post)
    {
        if (true === $user->hasPermissionTo('put-own-posts')) {
            return $user->id == $post->user_id;
        }
    }


    /**
     * Determine whether the user can delete a post
     * @param  User $user
     * @param  Post $post
     */
    public function delete(User $user, Post $post)
    {
        if (true === $user->hasPermissionTo('delete-own-posts')) {
            return $user->id == $post->user_id;
        }
    }
}
