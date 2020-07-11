<?php

namespace Tests\Feature\Http\Controllers\UserController;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a '401 Unauthorized' is returned when a guest attempts to get update another users account
     */
    public function testGuestCannotDeleteExistingUser(): void
    {
        $user = factory(User::class)->create();

        $this->put(self::URL_PREFIX . 'user/' . $user['id'], [], self::HEADER)
            ->assertUnauthorized();
    }


    /**
     * Test a '200 Ok' is returned along with the users details, and their original commenter role when an authenticated
     * commenter user attempts to update their account using a valid request format, and includes a roles array with a
     * role id other than commenter
     */
    public function testAuthenticatedCommenterUserCanPutTheirOwnAccountDetailsExceptRole(): void
    {
        $existingUser = factory(User::class)->create()->assignRole('commenter');

        $request = [
            'displayname'           => 'validdisplayname',
            'email'                 => 'valid@email.com',
            'password'              => 'validPassw0rd!',
            'password_confirmation' => 'validPassw0rd!',
            'roles'                 => [Role::findByName('admin', 'web')->id]
        ];

        $user = $this->actingAs($existingUser, 'api')->put(self::URL_PREFIX . 'user/' . $existingUser['id'], $request, self::HEADER);

        $user
            ->assertStatus(200)
            ->assertJson([
                'id'          => $existingUser['id'],
                'displayname' => $request['displayname'],
                'roles'       => [
                    [
                        'id'   => Role::findByName('commenter', 'web')->id,
                        'name' => Role::findByName('commenter', 'web')->name,
                    ]
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'id'          => $existingUser['id'],
            'displayname' => $user['displayname'],
            'email'       => $request['email'],
        ]);

        $this->assertDatabaseMissing('model_has_roles', [
            'role_id'    => Role::findByName('admin', 'web')->id,
            'model_type' => 'App\User',
            'model_id'   => $existingUser['id'],
        ]);
    }


    /**
     * Test a '403 Forbidden' is returned when an authenticated commenter user attempts to update another users account
     */
    public function testAuthenticatedCommenterUserCannotPutAnotherUsersDetails(): void
    {
        $existingUser = factory(User::class)->create();
        $user = factory(User::class)->create()->assignRole('commenter');

        $this->actingAs($user, 'api')->put(self::URL_PREFIX . 'user/' . $existingUser['id'], [], self::HEADER)
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id'          => $existingUser['id'],
            'displayname' => $existingUser['displayname'],
            'email'       => $existingUser['email'],
        ]);
    }


    /**
     * Test a '200 Ok' is returned along with the users details, and their original editor role when an authenticated
     * editor user attempts to update their account using a valid request format, and includes a roles array with a
     * role id other than commenter
     */
    public function testAuthenticatedEditorUserCanPutTheirOwnAccountDetailsExceptRole(): void
    {
        $existingUser = factory(User::class)->create()->assignRole('editor');

        $request = [
            'displayname'           => 'validdisplayname',
            'email'                 => 'valid@email.com',
            'password'              => 'validPassw0rd!',
            'password_confirmation' => 'validPassw0rd!',
            'roles'                 => [Role::findByName('admin', 'web')->id]
        ];

        $user = $this->actingAs($existingUser, 'api')->put(self::URL_PREFIX . 'user/' . $existingUser['id'], $request, self::HEADER);

        $user
            ->assertStatus(200)
            ->assertJson([
                'id'          => $existingUser['id'],
                'displayname' => $request['displayname'],
                'roles'       => [
                    [
                        'id'   => Role::findByName('editor', 'web')->id,
                        'name' => Role::findByName('editor', 'web')->name,
                    ]
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'id'          => $existingUser['id'],
            'displayname' => $user['displayname'],
            'email'       => $request['email'],
        ]);

        $this->assertDatabaseMissing('model_has_roles', [
            'role_id'    => Role::findByName('admin', 'web')->id,
            'model_type' => 'App\User',
            'model_id'   => $existingUser['id'],
        ]);
    }


    /**
     * Test a '403 Forbidden' is returned when an authenticated editor user attempts to update another users account
     */
    public function testAuthenticatedEditorUserCannotPutAnotherUsersDetails(): void
    {
        $existingUser = factory(User::class)->create();
        $user = factory(User::class)->create()->assignRole('editor');

        $this->actingAs($user, 'api')->put(self::URL_PREFIX . 'user/' . $existingUser['id'], [], self::HEADER)
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id'          => $existingUser['id'],
            'displayname' => $existingUser['displayname'],
            'email'       => $existingUser['email'],
        ]);
    }


    /**
     * Test a '200 Ok' is returned along with the users details, and their new role when an authenticated admin user
     * attempts to update their account using a valid request format, and includes a roles array with a role id other
     * than admin when another admin user exists
     */
    public function testAuthenticatedAdminUserCanPutTheirOwnAccountDetailsAndChangeRoleWhenAnotherAdminExists(): void
    {
        factory(User::class)->create()->assignRole('admin');
        $existingUser = factory(User::class)->create()->assignRole('admin');

        $request = [
            'displayname'           => 'validdisplayname',
            'email'                 => 'valid@email.com',
            'password'              => 'validPassw0rd!',
            'password_confirmation' => 'validPassw0rd!',
            'roles'                 => [Role::findByName('editor', 'web')->id]
        ];

        $user = $this->actingAs($existingUser, 'api')->put(self::URL_PREFIX . 'user/' . $existingUser['id'], $request, self::HEADER);

        $user
            ->assertStatus(200)
            ->assertJson([
                'id'          => $existingUser['id'],
                'displayname' => $request['displayname'],
                'roles'       => [
                    [
                        'id'          => Role::findByName('editor', 'web')->id,
                        'name'        => 'editor',
                        'permissions' => Role::findByName('editor', 'web')->getAllPermissions()->sortBy('name')->pluck('name')->toArray()
                    ]
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'id'          => $existingUser['id'],
            'displayname' => $request['displayname'],
            'email'       => $request['email'],
        ]);

        $this->assertDatabaseMissing('model_has_roles', [
            'role_id'    => Role::findByName('admin', 'web')->id,
            'model_type' => 'App\User',
            'model_id'   => $existingUser['id'],
        ]);
    }


    /**
     * Test a '422 Unprocessable entity' is returned along with the users details, and their new role when an
     * authenticated admin user attempts to update their account using a valid request format, and includes a roles
     * array with a role id other than admin when another admin user exists
     */
    public function testAuthenticatedAdminUserCannotPutTheirOwnAccountDetailsAndChangeRoleWhenTheyAreTheOnlyAdmin(): void
    {
        $existingUser = factory(User::class)->create()->assignRole('admin');

        $request = [
            'displayname'           => 'validdisplayname',
            'email'                 => 'valid@email.com',
            'password'              => 'validPassw0rd!',
            'password_confirmation' => 'validPassw0rd!',
            'roles'                 => [Role::findByName('editor', 'web')->id]
        ];

        $this->actingAs($existingUser, 'api')->put(self::URL_PREFIX . 'user/' . $existingUser['id'], $request, self::HEADER)
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors'  => [
                    'roles' => [
                        'As the only admin you may not remove your admin role.'
                    ]
                ]
            ]);
    }


    /**
     * Test a '422 Unprocessable Entity' is returned when an admin attempts to update their own account using an invalid
     * request format
     *
     * @dataProvider  \Tests\Feature\Http\Controllers\UserController\DataProvider::userPutUserInputValidation
     */
    public function testAuthenticatedAdminUserCannotPutInvalidData(string $input, $inputValue, array $validationErrors): void
    {
        $user = factory(User::class)->create()->assignRole('admin');

        $this->actingAs($user, 'api')->put(self::URL_PREFIX . 'user/' . $user['id'], [$input => $inputValue], self::HEADER)
            ->assertStatus(422)
            ->assertJsonValidationErrors($validationErrors);
    }
}
