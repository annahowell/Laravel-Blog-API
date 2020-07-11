<?php

namespace Tests\Feature\Http\Controllers\PostController;

use App\Post;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a '401 Unauthorized' is returned when a guest user attempts to delete an existing post
     */
    public function testGuestCannotDeleteExistingPost(): void
    {
        factory(User::class)->create();
        $post = factory(Post::class)->create();

        $this->delete(self::URL_PREFIX . 'posts/' . $post['id'], [], self::HEADER)
            ->assertUnauthorized();

        $this->assertDatabaseHas('posts', [
            'id' => $post['id'],
        ]);
    }



    /**
     * Test a '401 Unauthorized' is returned when a commenter user attempts to delete an existing post
     */
    public function testAuthenticatedCommenterUserCannotDeletePost(): void
    {
        factory(User::class)->create();
        $post = factory(Post::class)->create();
        $user = factory(User::class)->create()->assignRole('commenter');

        $this->actingAs($user, 'api')->delete(self::URL_PREFIX . 'posts/' . $post['id'], [], self::HEADER)
            ->assertForbidden();

        $this->assertDatabaseHas('posts', [
            'id' => $post['id'],
        ]);
    }



    /**
     * Test a '204 No Content' is returned when an authenticated user attempts to delete their own existing post
     */
    public function testAuthenticatedEditorUserCanDeleteTheirOwnExistingPost(): void
    {
        $user = factory(User::class)->create()->assignRole('editor');
        $post = factory(Post::class)->create();

        $this->actingAs($user, 'api')->delete(self::URL_PREFIX . 'posts/' . $post['id'], [], self::HEADER)
            ->assertNoContent(204);

        $this->assertDatabaseMissing('posts', [
            'id' => $post['id'],
        ]);
    }



    /**
     * Test a '403 Forbidden' is returned when an authenticated user attempts to delete an existing post made by another
     * user
     */
    public function testAuthenticatedEditorUserCannotDeleteAnotherUsersExistingPost(): void
    {
        factory(User::class)->create();
        $post = factory(Post::class)->create();
        $user = factory(User::class)->create()->assignRole('editor');

        $this->actingAs($user, 'api')->delete(self::URL_PREFIX . 'posts/' . $post['id'], [], self::HEADER)
            ->assertForbidden();

        $this->assertDatabaseHas('posts', [
            'id' => $post['id'],
        ]);
    }



    /**
     * Test a '204 No Content' is returned when an authenticated admin user attempts to delete another users existing
     * post
     */
    public function testAuthenticatedAdminUserCanDeleteAnotherUsersExistingPost(): void
    {
        factory(User::class)->create();
        $post = factory(Post::class)->create();
        $user = factory(User::class)->create()->assignRole('admin');

        $this->actingAs($user, 'api')->delete(self::URL_PREFIX . 'posts/' . $post['id'], [], self::HEADER)
            ->assertNoContent(204);

        $this->assertDatabaseMissing('posts', [
            'id' => $post['id'],
        ]);
    }



    /**
     * Test a '404 Not Found' is returned when an authenticated admin user attempts to delete a nonexistent post
     */
    public function testAuthenticatedAdminUserCannotDeleteNonexistentPost(): void
    {
        $user = factory(User::class)->create()->assignRole('admin');

        $this->actingAs($user, 'api')->delete(self::URL_PREFIX . 'posts/1', [], self::HEADER)
            ->assertNotFound();
    }
}
