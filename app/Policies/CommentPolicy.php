<?php

namespace App\Policies;

use App\User;
use App\Comment;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can delete a comment
     * @param  User    $user
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('post-comments');
    }


    /**
     * Determine whether the user can delete a comment
     * @param  User    $user
     * @param  Comment $comment
     */
    public function update(User $user, Comment $comment)
    {
        if (true === $user->hasPermissionTo('put-own-comments')) {
            return $user->id == $comment->user_id;
        }
    }


    /**
     * Determine whether the user can delete a comment
     * @param  User    $user
     * @param  Comment $comment
     */
    public function delete(User $user, Comment $comment)
    {
        if (true === $user->hasPermissionTo('delete-own-comments')) {
            return $user->id == $comment->user_id;
        }
    }
}
