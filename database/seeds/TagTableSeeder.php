<?php

use App\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints(); // Cross compatible; lesser of two evils
        Tag::truncate();
        Schema::enableForeignKeyConstraints();

        factory(Tag::class, 20)->create();
    }
}
