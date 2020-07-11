<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(User::class, function (Faker $faker) {
    return [
        'displayname'       => $faker->word,
        'email'             => $faker->safeEmail,
        'email_verified_at' => now(),
        'password'          => '$2y$10$hP5qybufo6MMO7j7xFsC/e/dsZ0fzh/bx4qh2dNKNo.tRHTRZEi7q', // Password123!
        'remember_token'    => Str::random(10),
    ];
});
