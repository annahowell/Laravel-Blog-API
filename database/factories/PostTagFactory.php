<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Post;
use App\Tag;
use App\PostTag;

$factory->define(PostTag::class, function () {
    return [
        'post_id' => Post::all()->random()->id,
        'tag_id'  =>  Tag::all()->random()->id,
    ];
});
