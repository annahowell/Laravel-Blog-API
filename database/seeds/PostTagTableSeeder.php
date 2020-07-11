<?php

use App\Tag;
use App\Post;
use App\PostTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PostTagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints(); // Cross compatible; lesser of two evils
        PostTag::truncate();
        Schema::enableForeignKeyConstraints();

        $tags = Tag::all();

        Post::all()->each(function ($task) use ($tags) {
            $task->tags()->attach(
                $tags->random(rand(0, 5))->pluck('id')->toArray()
            );
        });
    }
}
