<?php

namespace App\Providers;

use App\User;
use App\Tag;
use App\Post;
use App\Comment;
use App\Policies\UserPolicy;
use App\Policies\TagPolicy;
use App\Policies\PostPolicy;
use App\Policies\CommentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',

        User::class => UserPolicy::class,
        Post::class => PostPolicy::class,
        Comment::class => CommentPolicy::class,
        Tag::class => TagPolicy::class,
    ];



    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Grant admins access to all endpoints
        Gate::before(function ($user) {
            return $user->hasRole('admin') ? true : null;
        });

        Passport::routes();
    }
}
