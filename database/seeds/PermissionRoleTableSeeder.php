<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage-users',
            'manage-roles',
            'manage-permissions',

            'post-comments',
            'put-own-comments',
            'put-all-comments',
            'delete-own-comments',
            'delete-all-comments',

            'post-posts',
            'put-own-posts',
            'put-all-posts',
            'delete-own-posts',
            'delete-all-posts',

            'post-tags',
            'put-tags',
            'delete-tags',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        Role::create(['name' => 'foo']);

        Role::create(['name' => 'admin'])
            // ->givePermissionTo(Permission::all());
            ->givePermissionTo([
                'manage-users',
                'manage-roles',
                'manage-permissions',

                'post-comments',
                'put-all-comments',
                'delete-all-comments',

                'post-posts',
                'put-all-posts',
                'delete-all-posts',

                'post-tags',
                'put-tags',
                'delete-tags',
            ]);

        Role::create(['name' => 'editor'])
            ->givePermissionTo([
                'post-comments',
                'put-own-comments',
                'delete-own-comments',

                'post-posts',
                'put-own-posts',
                'delete-own-posts',
            ]);

        Role::create(['name' => 'commenter'])
            ->givePermissionTo([
                'post-comments',
                'put-own-comments',
                'delete-own-comments',
            ]);
    }
}
