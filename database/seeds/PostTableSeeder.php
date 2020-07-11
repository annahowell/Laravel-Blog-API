<?php

use App\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PostTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints(); // Cross compatible; lesser of two evils
        Post::truncate();
        Schema::enableForeignKeyConstraints();

        factory(Post::class, 10)->create();
    }
}
