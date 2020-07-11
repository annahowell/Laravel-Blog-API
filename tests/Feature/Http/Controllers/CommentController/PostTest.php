<?php

namespace Tests\Feature\Http\Controllers\CommentController;

use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a '401 Unauthorized' is returned when a guest user attempts to create a new comment
     */
    public function testGuestCannotPostComment(): void
    {
        $this->post(self::URL_PREFIX . 'comments', [], self::HEADER)
            ->assertUnauthorized();
    }


    /**
     * Test a '201 Created' is returned along with the comment, post id and user who made the comment, when an
     * authenticated commenter user attempts to create a new comment using a valid request format
     */
    public function testAuthenticatedCommenterUserCanPostValidComment(): void
    {
        $user = factory(User::class)->create()->assignRole('commenter');
        $post = factory(Post::class)->create();
        $request = [
            'body'    => 'Valid body',
            'post_id' => $post['id'],
        ];

        $comment = $this->actingAs($user, 'api')->post(self::URL_PREFIX . 'comments', $request, self::HEADER);

        $comment
            ->assertStatus(201)
            ->assertJson([
                'id'         => $comment['id'],
                'body'       => $request['body'],
                'post_id'    => $request['post_id'],
                'created_at' => $comment['created_at'],
                'updated_at' => $comment['updated_at'],
                'author' => [
                    'id'          => $user['id'],
                    'displayname' => $user['displayname'],
                ],
            ]);

        $this->assertDatabaseHas('comments', [
            'id'   => $comment['id'],
            'body' => $request['body'],
            'post_id' => $request['post_id'],
            'user_id' => $user['id'],
        ]);
    }


    /**
     * Test a '201 Created' is returned along with the comment, post id and user who made the comment, when an
     * authenticated editor user attempts to create a new comment using a valid request format
     */
    public function testAuthenticatedEditorUserCanPostValidComment(): void
    {
        $user = factory(User::class)->create()->assignRole('editor');
        $post = factory(Post::class)->create();
        $request = [
            'body'    => 'Valid body',
            'post_id' => $post['id'],
        ];

        $comment = $this->actingAs($user, 'api')->post(self::URL_PREFIX . 'comments', $request, self::HEADER);

        $comment
            ->assertStatus(201)
            ->assertJson([
                'id'         => $comment['id'],
                'body'       => $request['body'],
                'post_id'    => $request['post_id'],
                'created_at' => $comment['created_at'],
                'updated_at' => $comment['updated_at'],
                'author' => [
                    'id'          => $user['id'],
                    'displayname' => $user['displayname'],
                ],
            ]);

        $this->assertDatabaseHas('comments', [
            'id'   => $comment['id'],
            'body' => $request['body'],
            'post_id' => $request['post_id'],
            'user_id' => $user['id'],
        ]);
    }


    /**
     * Test a '201 Created' is returned along with the comment, post id and user who made the comment, when an
     * authenticated admin user attempts to create a new comment using a valid request format
     */
    public function testAuthenticatedAdminUserCanPostValidComment(): void
    {
        $user = factory(User::class)->create()->assignRole('admin');
        $post = factory(Post::class)->create();
        $request = [
            'body'    => 'Valid body',
            'post_id' => $post['id'],
        ];

        $comment = $this->actingAs($user, 'api')->post(self::URL_PREFIX . 'comments', $request, self::HEADER);

        $comment
            ->assertStatus(201)
            ->assertJson([
                'id'         => $comment['id'],
                'body'       => $request['body'],
                'post_id'    => $request['post_id'],
                'created_at' => $comment['created_at'],
                'updated_at' => $comment['updated_at'],
                'author' => [
                    'id'          => $user['id'],
                    'displayname' => $user['displayname'],
                ],
            ]);

        $this->assertDatabaseHas('comments', [
            'id'   => $comment['id'],
            'body' => $request['body'],
            'post_id' => $request['post_id'],
            'user_id' => $user['id'],
        ]);
    }


    /**
     * Test a '422 Unprocessable Entity' is returned when an authenticated commenter user attempts to create a comment
     * using an invalid request format
     *
     * @dataProvider  \Tests\Feature\Http\Controllers\CommentController\DataProvider::commentPostInputValidation
     * @param string $input
     * @param string $inputValue
     * @param array $validationError
     */
    public function testAuthenticatedUserCannotPostInvalidComment(string $input, string $inputValue): void
    {
        $user = factory(User::class)->create()->assignRole('commenter');

        $this->actingAs($user, 'api')->post(self::URL_PREFIX . 'comments', [$input => $inputValue], self::HEADER)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['body', 'post_id']);
    }
}
