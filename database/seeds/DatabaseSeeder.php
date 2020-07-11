<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // This is called by the create_permission_tables migration
        // $this->call(PermissionRoleTableSeeder::class);
        $this->call(UserTableSeeder::class);

        $this->call(PostTableSeeder::class);
        $this->call(TagTableSeeder::class);
        $this->call(CommentTableSeeder::class);

        $this->call(PostTagTableSeeder::class);
    }
}
