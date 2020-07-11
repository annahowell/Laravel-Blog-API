<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Comment;
use App\Post;
use App\User;
use Faker\Generator as Faker;

$factory->define(Comment::class, function (Faker $faker) {
    return [
        // 1-2 paragraphs, true return as string not array
        'body'    => '<p>' . $faker->paragraphs(rand(1, 2), true) . '</p>',
        'user_id' => User::all()->random()->id,
        'post_id' => Post::all()->random()->id,
    ];
});
