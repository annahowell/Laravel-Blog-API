<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Post;
use App\User;
use Faker\Generator as Faker;

$factory->define(Post::class, function (Faker $faker) {
    return [
        'title'   => $faker->sentence,
        // 1-6 paragraphs, true return as string not array
        'body'    => '<p>'.$faker->paragraphs(rand(1, 6), true).'</p>',
        'user_id' => User::all()->random()->id,
    ];
});
