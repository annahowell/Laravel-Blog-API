<?php

namespace Tests\Feature\Http\Controllers\CommentController;

use App\Post;
use App\User;
use App\Comment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a '401 Unauthorized' is returned when a guest user attempts to delete an existing comment
     */
    public function testGuestCannotDeleteExistingComment(): void
    {
        factory(User::class)->create();
        factory(Post::class)->create();
        $comment = factory(Comment::class)->create();

        $this->delete(self::URL_PREFIX . 'comments/' . $comment['id'], [], self::HEADER)
            ->assertUnauthorized();

        $this->assertDatabaseHas('comments', [
            'id' => $comment['id'],
        ]);
    }



    /**
     * Test a '204 No Content' is returned when an authenticated commenter user attempts to delete their own existing
     * comment
     */
    public function testAuthenticatedCommenterUserCanDeleteTheirOwnExistingComment(): void
    {
        $user = factory(User::class)->create()->assignRole('commenter');
        factory(Post::class)->create();
        $comment = factory(Comment::class)->create();

        $this->actingAs($user, 'api')->delete(self::URL_PREFIX . 'comments/' . $comment['id'], [], self::HEADER);

        $this->assertDatabaseMissing('comments', [
            'id' => $comment['id'],
        ]);
    }



    /**
     * Test a '204 No Content' is returned when an authenticated editor user attempts to delete their own existing
     * comment
     */
    public function testAuthenticatedEditorUserCanDeleteTheirOwnExistingComment(): void
    {
        $user = factory(User::class)->create()->assignRole('editor');
        factory(Post::class)->create();
        $comment = factory(Comment::class)->create();

        $this->actingAs($user, 'api')->delete(self::URL_PREFIX . 'comments/' . $comment['id'], [], self::HEADER);

        $this->assertDatabaseMissing('comments', [
            'id' => $comment['id'],
        ]);
    }



    /**
     * Test a '204 No Content' is returned when an authenticated admin user attempts to delete their own existing
     * comment
     */
    public function testAuthenticatedAdminUserCanDeleteTheirOwnExistingComment(): void
    {
        $user = factory(User::class)->create()->assignRole('admin');
        factory(Post::class)->create();
        $comment = factory(Comment::class)->create();

        $this->actingAs($user, 'api')->delete(self::URL_PREFIX . 'comments/' . $comment['id'], [], self::HEADER);

        $this->assertDatabaseMissing('comments', [
            'id' => $comment['id'],
        ]);
    }



    /**
     * Test a '403 Forbidden' is returned when an authenticated commenter user attempts to delete an existing comment
     * made by another user
     */
    public function testAuthenticatedCommenterUserCannotDeleteAnotherUsersExistingComment(): void
    {
        factory(User::class)->create();
        factory(Post::class)->create();
        $comment = factory(Comment::class)->create();
        $user = factory(User::class)->create()->assignRole('commenter');

        $this->actingAs($user, 'api')->delete(self::URL_PREFIX . 'comments/' . $comment['id'], [], self::HEADER)
            ->assertForbidden();

        $this->assertDatabaseHas('comments', [
            'id' => $comment['id'],
        ]);
    }



    /**
     * Test a '403 Forbidden' is returned when an authenticated editor user attempts to delete an existing comment
     * made by another user
     */
    public function testAuthenticatedEditorUserCannotDeleteAnotherUsersExistingComment(): void
    {
        factory(User::class)->create();
        factory(Post::class)->create();
        $comment = factory(Comment::class)->create();
        $user = factory(User::class)->create()->assignRole('editor');

        $this->actingAs($user, 'api')->delete(self::URL_PREFIX . 'comments/' . $comment['id'], [], self::HEADER)
            ->assertForbidden();

        $this->assertDatabaseHas('comments', [
            'id' => $comment['id'],
        ]);
    }



    /**
     * Test a '404 Not Found' is returned when an authenticated admin user attempts to delete a nonexistent comment
     */
    public function testAuthenticatedAdminUserCannotDeleteNonexistentComment(): void
    {
        $user = factory(User::class)->create()->assignRole('admin');

        $this->actingAs($user, 'api')->delete(self::URL_PREFIX . 'comments/1', [], self::HEADER)
            ->assertNotFound();
    }
}
