<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints(); // Cross compatible; lesser of two evils
        User::truncate();
        Schema::enableForeignKeyConstraints();

        $commenter = factory(User::class)->create([
            'displayname' => 'commenter',
            'email'       => 'commenter@bar.com',
        ]);

        $editor = factory(User::class)->create([
            'displayname' => 'editor',
            'email'       => 'editor@bar.com',
        ]);

        $admin = factory(User::class)->create([
            'displayname' => 'admin',
            'email'       => 'admin@bar.com',
        ]);

        $commenter->assignRole('commenter');
        $editor->assignRole('editor');
        $admin->assignRole('admin');
    }
}
