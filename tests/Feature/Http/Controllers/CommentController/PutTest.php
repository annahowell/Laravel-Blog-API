<?php

namespace Tests\Feature\Http\Controllers\CommentController;

use App\Post;
use App\User;
use App\Comment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a '401 Unauthorized' is returned when a guest user attempts to update an existing comment
     */
    public function testGuestCannotPutComment(): void
    {
        factory(User::class)->create();
        factory(Post::class)->create();
        $existingComment = factory(Comment::class)->create();

        $this->put(self::URL_PREFIX . 'comments/' . $existingComment['id'], [], self::HEADER)
            ->assertUnauthorized();
    }



    /**
     * Test a '200 Ok' is returned along with the comment, post id and user who made the comment, when an authenticated
     * commenter user attempts to update their own existing comment using a valid request format
     */
    public function testAuthenticatedCommenterUserCanPutTheirOwnValidComment(): void
    {
        $user = factory(User::class)->create()->assignRole('commenter');
        factory(Post::class)->create();
        $existingComment = factory(Comment::class)->create();

        $request = [
            'body' => $body = 'New valid body',
        ];

        $comment = $this->actingAs($user, 'api')->put(self::URL_PREFIX . 'comments/' . $existingComment['id'], $request, self::HEADER);

        $comment
            ->assertStatus(200)
            ->assertJson([
                'id'         => $existingComment['id'],
                'body'       => $request['body'],
                'post_id'    => $comment['post_id'],
                'created_at' => $comment['created_at'],
                'updated_at' => $comment['updated_at'],
                'author' => [
                    'id'          => $user['id'],
                    'displayname' => $user['displayname'],
                ],
            ]);

        $this->assertDatabaseHas('comments', [
            'id'   => $existingComment['id'],
            'body' => $request['body'],
        ]);

        $this->assertDatabaseMissing('comments', [
            'id'   => $existingComment['id'],
            'body' => $existingComment['body'],
        ]);
    }



    /**
     * Test a '200 Ok' is returned along with the comment, post id and user who made the comment, when an authenticated
     * editor user attempts to update their own existing comment using a valid request format
     */
    public function testAuthenticatedEditorUserCanPutTheirOwnValidComment(): void
    {
        $user = factory(User::class)->create()->assignRole('editor');
        factory(Post::class)->create();
        $existingComment = factory(Comment::class)->create();

        $request = [
            'body' => $body = 'New valid body',
        ];

        $comment = $this->actingAs($user, 'api')->put(self::URL_PREFIX . 'comments/' . $existingComment['id'], $request, self::HEADER);

        $comment
            ->assertStatus(200)
            ->assertJson([
                'id'         => $existingComment['id'],
                'body'       => $request['body'],
                'post_id'    => $comment['post_id'],
                'created_at' => $comment['created_at'],
                'updated_at' => $comment['updated_at'],
                'author' => [
                    'id'          => $user['id'],
                    'displayname' => $user['displayname'],
                ],
            ]);

        $this->assertDatabaseHas('comments', [
            'id'   => $existingComment['id'],
            'body' => $request['body'],
        ]);

        $this->assertDatabaseMissing('comments', [
            'id'   => $existingComment['id'],
            'body' => $existingComment['body'],
        ]);
    }



    /**
     * Test a '200 Ok' is returned along with the comment, post id and user who made the comment, when an authenticated
     * admin user attempts to update their own existing comment using a valid request format
     */
    public function testAuthenticatedAdminUserCanPutTheirOwnValidComment(): void
    {
        $user = factory(User::class)->create()->assignRole('admin');
        factory(Post::class)->create();
        $existingComment = factory(Comment::class)->create();

        $request = [
            'body' => $body = 'New valid body',
        ];

        $comment = $this->actingAs($user, 'api')->put(self::URL_PREFIX . 'comments/' . $existingComment['id'], $request, self::HEADER);

        $comment
            ->assertStatus(200)
            ->assertJson([
                'id'         => $existingComment['id'],
                'body'       => $request['body'],
                'post_id'    => $comment['post_id'],
                'created_at' => $comment['created_at'],
                'updated_at' => $comment['updated_at'],
                'author' => [
                    'id'          => $user['id'],
                    'displayname' => $user['displayname'],
                ],
            ]);

        $this->assertDatabaseHas('comments', [
            'id'   => $existingComment['id'],
            'body' => $request['body'],
        ]);

        $this->assertDatabaseMissing('comments', [
            'id'   => $existingComment['id'],
            'body' => $existingComment['body'],
        ]);
    }



    /**
     * Test a '403 Forbidden' is returned when an authenticated commenter user attempts to delete an existing comment
     * made by another user
     */
    public function testAuthenticatedCommenterUserCannotPutAnotherUsersExistingComment(): void
    {
        factory(User::class)->create();
        factory(Post::class)->create();
        $existingComment = factory(Comment::class)->create();
        $user = factory(User::class)->create()->assignRole('commenter');

        $request = [
            'body' => $body = 'New valid body',
        ];

        $this->actingAs($user, 'api')->put(self::URL_PREFIX . 'comments/' . $existingComment['id'], $request, self::HEADER)
            ->assertForbidden();

        $this->assertDatabaseHas('comments', [
            'id' => $existingComment['id'],
        ]);
    }



    /**
     * Test a '403 Forbidden' is returned when an authenticated editor user attempts to delete an existing comment made
     * by another user
     */
    public function testAuthenticatedEditorUserCannotPutAnotherUsersExistingComment(): void
    {
        factory(User::class)->create();
        factory(Post::class)->create();
        $existingComment = factory(Comment::class)->create();
        $user = factory(User::class)->create()->assignRole('editor');

        $request = [
            'body' => $body = 'New valid body',
        ];

        $this->actingAs($user, 'api')->put(self::URL_PREFIX . 'comments/' . $existingComment['id'], $request, self::HEADER)
            ->assertForbidden();

        $this->assertDatabaseHas('comments', [
            'id' => $existingComment['id'],
        ]);
    }



    /**
     * Test a '200 Ok' is returned along with the comment, post id and user who made the comment, when an authenticated
     * admin user attempts to update their own existing comment using a valid request format
     */
    public function testAuthenticatedAdminUserCanPutAnotherUsersExistingComment(): void
    {
        $existingUser = factory(User::class)->create();
        factory(Post::class)->create();
        $existingComment = factory(Comment::class)->create();
        $user = factory(User::class)->create()->assignRole('admin');

        $request = [
            'body' => $body = 'New valid body',
        ];

        $comment = $this->actingAs($user, 'api')->put(self::URL_PREFIX . 'comments/' . $existingComment['id'], $request, self::HEADER);

        $comment
            ->assertStatus(200)
            ->assertJson([
                'id'         => $existingComment['id'],
                'body'       => $request['body'],
                'post_id'    => $comment['post_id'],
                'created_at' => $comment['created_at'],
                'updated_at' => $comment['updated_at'],
                'author' => [
                    'id'          => $existingUser['id'],
                    'displayname' => $existingUser['displayname'],
                ],
            ]);

        $this->assertDatabaseHas('comments', [
            'id'   => $existingComment['id'],
            'body' => $request['body'],
        ]);

        $this->assertDatabaseMissing('comments', [
            'id'   => $existingComment['id'],
            'body' => $existingComment['body'],
        ]);
    }



    /**
     * Test a '404 Not Found' is returned when an authenticated admin user attempts to update a nonexistent comment
     */
    public function testAuthenticatedAdminUserCannotPutNonexistentComment(): void
    {
        $user = factory(User::class)->create()->assignRole('admin');

        $this->actingAs($user, 'api')->put(self::URL_PREFIX . 'comments/1', [], self::HEADER)
            ->assertNotFound();
    }



    /**
     * Test a '422 Unprocessable Entity' is returned when an authenticated admin user attempts to update a comment using
     * an invalid request format
     *
     * @dataProvider  \Tests\Feature\Http\Controllers\CommentController\DataProvider::commentPutInputValidation
     * @param string $input
     * @param string $inputValue
     * @param string $validationError
     */
    public function testAuthenticatedUserCannotPutTheirOwnInvalidComment(string $input, string $inputValue): void
    {
        $user = factory(User::class)->create()->assignRole('admin');
        factory(Post::class)->create();
        $existingComment = factory(Comment::class)->create();

        $this->actingAs($user, 'api')->put(self::URL_PREFIX . 'comments/' . $existingComment['id'], [$input => $inputValue], self::HEADER)
            ->assertStatus(422)
            ->assertJsonValidationErrors('body');
    }
}
