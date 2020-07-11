<?php

namespace Tests\Feature\Http\Controllers\UserController;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a '401 Unauthorized' is returned when a guest attempts to get delete another users account
     */
    public function testGuestCannotDeleteExistingUser(): void
    {
        $user = factory(User::class)->create()->assignRole('commenter');

        $this->delete(self::URL_PREFIX . 'user/' . $user['id'], [],self::HEADER)
            ->assertUnauthorized();

        $this->assertDatabaseHas('users', [
            'id'      => $user['id'],
            'enabled' => true
        ]);
    }



    /**
     * Test a '204 No Content' is returned when an authenticated commenter user attempts to delete their own account
     */
    public function testAuthenticatedCommenterUserCanDeleteTheirOwnAccount(): void
    {
        $user = factory(User::class)->create()->assignRole('commenter');

        $this->actingAs($user, 'api')->delete(self::URL_PREFIX . 'user/' . $user['id'], [],self::HEADER)
            ->assertNoContent(204);

        $this->assertDatabaseHas('users', [
            'id'      => $user['id'],
            'enabled' => false
        ]);
    }



    /**
     * Test a '403 Forbidden' is returned when an authenticated commenter user attempts to delete another users account
     */
    public function testAuthenticatedCommenterUserCannotDeleteAnotherUsersAccount(): void
    {
        $existingUser = factory(User::class)->create();
        $user = factory(User::class)->create()->assignRole('commenter');

        $this->actingAs($user, 'api')->delete(self::URL_PREFIX . 'user/' . $existingUser['id'], [],self::HEADER)
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id'      => $user['id'],
            'enabled' => true
        ]);
    }



    /**
     * Test a '204 No Content' is returned when an authenticated editor user attempts to delete their own account
     */
    public function testAuthenticatedEditorUserCanDeleteTheirOwnAccount(): void
    {
        $user = factory(User::class)->create()->assignRole('editor');

        $this->actingAs($user, 'api')->delete(self::URL_PREFIX . 'user/' . $user['id'], [], self::HEADER)
            ->assertNoContent(204);

        $this->assertDatabaseHas('users', [
            'id'      => $user['id'],
            'enabled' => false
        ]);
    }



    /**
     * Test a '403 Forbidden' is returned when an authenticated editor user attempts to delete another users account
     */
    public function testAuthenticatedEditorUserCannotDeleteAnotherUsersAccount(): void
    {
        $existingUser = factory(User::class)->create();
        $user = factory(User::class)->create()->assignRole('editor');

        $this->actingAs($user, 'api')->delete(self::URL_PREFIX . 'user/' . $existingUser['id'], [], self::HEADER)
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id'      => $user['id'],
            'enabled' => true
        ]);
    }



    /**
     * Test a '204 No Content' is returned when an authenticated admin user attempts to delete their own account and
     * another admin user exists
     */
    public function testAuthenticatedAdminUserCanDeleteTheirOwnAccountWhenAnotherAdminExists(): void
    {
        factory(User::class)->create()->assignRole('admin');
        $user = factory(User::class)->create()->assignRole('admin');

        $this->actingAs($user, 'api')->delete(self::URL_PREFIX . 'user/' . $user['id'], [], self::HEADER)
            ->assertNoContent(204);

        $this->assertDatabaseHas('users', [
            'id'      => $user['id'],
            'enabled' => false
        ]);
    }



    /**
     * Test a '409 Conflict' is returned when an authenticated admin user attempts to delete their own account and they
     * are the only admin user
     */
    public function testAuthenticatedAdminUserCannotDeleteTheirOwnAccountWhenAnotherAdminDoesntExists(): void
    {
        $user = factory(User::class)->create()->assignRole('admin');

        $this->actingAs($user, 'api')->delete(self::URL_PREFIX . 'user/' . $user['id'], [], self::HEADER)
            ->assertStatus(409);

        $this->assertDatabaseHas('users', [
            'id'      => $user['id'],
            'enabled' => true
        ]);
    }



    /**
     * Test a '204 No Content' is returned when an authenticated admin user attempts to delete another users account
     */
    public function testAuthenticatedAdminUserCanDeleteAnotherUsersAccount(): void
    {
        $existingUser = factory(User::class)->create();
        $user = factory(User::class)->create()->assignRole('admin');

        $this->actingAs($user, 'api')->delete(self::URL_PREFIX . 'user/' . $existingUser['id'], [], self::HEADER)
            ->assertNoContent(204);

        $this->assertDatabaseHas('users', [
            'id'      => $existingUser['id'],
            'enabled' => false
        ]);
    }
}
