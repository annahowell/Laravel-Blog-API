<?php

use App\Comment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CommentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints(); // Cross compatible; lesser of two evils
        Comment::truncate();
        Schema::enableForeignKeyConstraints();

        factory(Comment::class, 50)->create();
    }
}
