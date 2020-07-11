<?php

namespace Tests\Feature\Http\Controllers\UserController;

use App\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a '401 Unauthorized' is returned when a guest attempts to get user details
     */
    public function testGuestCannotGetExistingUserDetails(): void
    {
        $user = factory(User::class)->create()->assignRole('commenter');

        $this->get(self::URL_PREFIX . 'user/' . $user['id'], self::HEADER)
            ->assertUnauthorized();
    }


    /**
     * Test a '401 Unauthorized' is returned when a guest attempts to get all users
     */
    public function testGuestCannotGetAllUsers(): void
    {
        $this->get(self::URL_PREFIX . 'user', self::HEADER)
            ->assertUnauthorized();
    }


    /**
     * Test a '401 Unauthorized' is returned when a guest attempts to all roles
     */
    public function testGuestCannotGetAllRoles(): void
    {
        $this->get(self::URL_PREFIX . 'user/roles', self::HEADER)
            ->assertUnauthorized();
    }


    /**
     * Test a '200 Ok' is returned along with the user details when an authenticated commenter user attempts to get
     * their own details
     */
    public function testAuthenticatedCommenterUserCanGetExistingOwnUserDetails(): void
    {
        $user = factory(User::class)->create()->assignRole('commenter');

        $this->actingAs($user, 'api')->get(self::URL_PREFIX . 'user/' . $user['id'], self::HEADER)
            ->assertStatus(200)
            ->assertJson([
                'id'          => $user['id'],
                'displayname' => $user['displayname'],
                'roles'       => [
                    [
                        'id'   => Role::findByName('commenter', 'web')->id,
                        'name' => Role::findByName('commenter', 'web')->name,
                    ]
                ]
            ])
            ->assertJsonMissing(['permissions']);
    }


    /**
     * Test a '403 Forbidden' is returned when an authenticated commenter user attempts to get another users details
     */
    public function testAuthenticatedCommenterUserCannotGetAnotherUsersDetails(): void
    {
        $existingUser = factory(User::class)->create();
        $user = factory(User::class)->create()->assignRole('commenter');

        $this->actingAs($user, 'api')->get(self::URL_PREFIX . 'user/' . $existingUser['id'], self::HEADER)
            ->assertForbidden();
    }


    /**
     * Test a '403 Forbidden' is returned whe an authenticated commenter user attempts to get all users
     */
    public function testAuthenticatedCommenterUserCannotGetAllUsers(): void
    {
        $user = factory(User::class)->create()->assignRole('commenter');

        $this->actingAs($user, 'api')->get(self::URL_PREFIX . 'user', self::HEADER)
            ->assertForbidden();
    }


    /**
     * Test a '403 Forbidden' is returned when an authenticated commenter user attempts to get all roles
     */
    public function testAuthenticatedCommenterUserCannotGetAllRoles(): void
    {
        $user = factory(User::class)->create()->assignRole('commenter');

        $this->actingAs($user, 'api')->get(self::URL_PREFIX . 'user/roles', self::HEADER)
            ->assertForbidden();
    }


    /**
     * Test a '200 Ok' is returned along with the user details when an authenticated editor user attempts to get their
     * own details
     */
    public function testAuthenticatedEditorUserCanGetExistingOwnUserDetails(): void
    {
        $user = factory(User::class)->create()->assignRole('editor');

        $this->actingAs($user, 'api')->get(self::URL_PREFIX . 'user/' . $user['id'], self::HEADER)
            ->assertStatus(200)
            ->assertJson([
                'id'          => $user['id'],
                'displayname' => $user['displayname'],
                'roles'       => [
                    [
                        'id'   => Role::findByName('editor', 'web')->id,
                        'name' => Role::findByName('editor', 'web')->name,
                    ]
                ]
            ])
            ->assertJsonMissing(['permissions']);
    }


    /**
     * Test a '403 Forbidden' is returned when an authenticated editor user attempts to get another users details
     */
    public function testAuthenticatedEditorUserCannotGetAnotherUsersDetails(): void
    {
        $existingUser = factory(User::class)->create();
        $user = factory(User::class)->create()->assignRole('editor');

        $this->actingAs($user, 'api')->get(self::URL_PREFIX . 'user/' . $existingUser['id'], self::HEADER)
            ->assertForbidden();
    }


    /**
     * Test a '403 Forbidden' is returned whe an authenticated editor user attempts to get all users
     */
    public function testAuthenticatedEditorUserCannotGetAllUsers(): void
    {
        $user = factory(User::class)->create()->assignRole('editor');

        $this->actingAs($user, 'api')->get(self::URL_PREFIX . 'user', self::HEADER)
            ->assertForbidden();
    }


    /**
     * Test a '403 Forbidden' is returned when an authenticated editor user attempts to get all roles
     */
    public function testAuthenticatedEditorUserCannotGetAllRoles(): void
    {
        $user = factory(User::class)->create()->assignRole('editor');

        $this->actingAs($user, 'api')->get(self::URL_PREFIX . 'user/roles', self::HEADER)
            ->assertForbidden();
    }


    /**
     * Test a '200 Ok' is returned along with the user details when an authenticated admin user attempts to get their
     * own details
     */
    public function testAuthenticatedAdminUserCanGetExistingOwnUserDetails(): void
    {
        $user = factory(User::class)->create()->assignRole('admin');

        $this->actingAs($user, 'api')->get(self::URL_PREFIX . 'user/' . $user['id'], self::HEADER)
            ->assertStatus(200)
            ->assertJson([
                'id'          => $user['id'],
                'displayname' => $user['displayname'],
                'enabled'     => true,
                'roles'       => [
                    [
                        'id'          => Role::findByName('admin', 'web')->id,
                        'name'        => 'admin',
                        'permissions' => $user->getAllPermissions()->sortBy('name')->pluck('name')->toArray()
                    ]
                ]
            ]);
    }


    /**
     * Test a '200 Ok' is returned along with the user details when an authenticated admin user attempts to get another
     * users details
     */
    public function testAuthenticatedAdminUserCanGetExistingUsersDetails(): void
    {
        $existingUser = factory(User::class)->create()->assignRole('commenter');
        $user = factory(User::class)->create()->assignRole('admin');

        $this->actingAs($user, 'api')->get(self::URL_PREFIX . 'user/' . $existingUser['id'], self::HEADER)
            ->assertStatus(200)
            ->assertJson([
                'id'          => $existingUser['id'],
                'displayname' => $existingUser['displayname'],
                'roles'       => [
                    [
                        'id'          => Role::findByName('commenter', 'web')->id,
                        'name'        => 'commenter',
                        'permissions' => $existingUser->getAllPermissions()->sortBy('name')->pluck('name')->toArray()
                    ]
                ]
            ]);
    }


    /**
     * Test a '200 Ok' is returned whe an authenticated admin user attempts to get all users
     */
    public function testAuthenticatedAdminUserCanGetAllUsers(): void
    {
        $user = factory(User::class)->create()->assignRole('admin');

        $this->actingAs($user, 'api')->get(self::URL_PREFIX . 'user', self::HEADER)
            ->assertStatus(200);
    }


    /**
     * Test a '200 Ok' is returned when an authenticated admin user attempts to get all roles
     */
    public function testAuthenticatedAdminUserCanGetAllRoles(): void
    {
        $user = factory(User::class)->create()->assignRole('admin');

        $this->actingAs($user, 'api')->get(self::URL_PREFIX . 'user/roles', self::HEADER)
            ->assertStatus(200);
    }


    /**
     * Test a '200 Ok' is returned when an authenticated user attempts to logout
     */
    public function testAuthenticatedUserCanLogout(): void
    {
        $user = factory(User::class)->create()->assignRole('commenter');

        $this->actingAs($user, 'api')->get(self::URL_PREFIX . 'user/logout', self::HEADER)
            ->assertStatus(200)
            ->assertJson(["message" => 'Successfully logged out.']);
    }
    

    /**
     * Test a '401 Unauthorized' is returned when a guest attempts to logout
     */
    public function testGuestCannotGetLogout(): void
    {
        $this->get(self::URL_PREFIX . 'user/logout', self::HEADER)
            ->assertUnauthorized();
    }
}
