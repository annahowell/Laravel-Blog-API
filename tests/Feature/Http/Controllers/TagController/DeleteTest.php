<?php

namespace Tests\Feature\Http\Controllers\TagController;

use App\Tag;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a '401 Unauthorized' is returned when a guest user attempts to delete an existing tag
     */
    public function testGuestCannotDeleteExistingTag(): void
    {
        factory(User::class)->create();
        $tag = factory(Tag::class)->create();

        $this->delete(self::URL_PREFIX . 'tags/' . $tag['id'], [], self::HEADER)
            ->assertUnauthorized();

        $this->assertDatabaseHas('tags', [
            'id' => $tag['id'],
        ]);
    }



    /**
     * Test a '403 Forbidden' is returned when an authenticated commenter user attempts to delete their own existing tag
     */
    public function testAuthenticatedCommenterUserCannotDeleteExistingTag(): void
    {
        $user = factory(User::class)->create()->assignRole('commenter');
        $tag = factory(Tag::class)->create();

        $this->actingAs($user, 'api')->delete(self::URL_PREFIX . 'tags/' . $tag['id'], [], self::HEADER)
            ->assertForbidden();

        $this->assertDatabaseHas('tags', [
            'id' => $tag['id'],
        ]);
    }



    /**
     * Test a '403 Forbidden' is returned when an authenticated editor user attempts to delete their own existing tag
     */
    public function testAuthenticatedEditorUserCannotDeleteExistingTag(): void
    {
        $user = factory(User::class)->create()->assignRole('editor');
        $tag = factory(Tag::class)->create();

        $this->actingAs($user, 'api')->delete(self::URL_PREFIX . 'tags/' . $tag['id'], [], self::HEADER)
            ->assertForbidden();

        $this->assertDatabaseHas('tags', [
            'id' => $tag['id'],
        ]);
    }



    /**
     * Test a '204 No Content' is returned when an authenticated user attempts to delete their own existing tag
     */
    public function testAuthenticatedAdminUserCanDeleteExistingTag(): void
    {
        $user = factory(User::class)->create()->assignRole('admin');
        $tag = factory(Tag::class)->create();

        $this->actingAs($user, 'api')->delete(self::URL_PREFIX . 'tags/' . $tag['id'], [], self::HEADER)
            ->assertNoContent(204);

        $this->assertDatabaseMissing('tags', [
            'id' => $tag['id'],
        ]);
    }



    /**
     * Test a '404 Not Found' is returned when an authenticated user attempts to delete a nonexistent tag
     */
    public function testAuthenticatedAdminUserCannotDeleteExistingTag(): void
    {
        $user = factory(User::class)->create()->assignRole('admin');

        $this->actingAs($user, 'api')->delete(self::URL_PREFIX . 'tags/1', [], self::HEADER)
            ->assertNotFound();
    }
}
