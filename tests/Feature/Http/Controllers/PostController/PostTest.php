<?php

namespace Tests\Feature\Http\Controllers\PostController;

use App\Tag;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a '401 Unauthorized' is returned when a guest user attempts to create a new post
     */
    public function testGuestCannotPostPost(): void
    {
        $this->post(self::URL_PREFIX . 'posts', [], self::HEADER)
            ->assertUnauthorized();
    }


    /**
     * Test a '403 Forbidden' is returned when an authenticated commenter user attempts to create a new post
     */
    public function testAuthenticatedCommenterUserCannotPostValidPost(): void
    {
        $user = factory(User::class)->create()->assignRole('commenter');
        $tag = factory(Tag::class)->create();

        $request = [
            'title' => 'Valid title',
            'body'  => 'Valid body',
            'tags'  => [$tag['id']]
        ];

        $this->actingAs($user, 'api')->post(self::URL_PREFIX . 'posts', $request, self::HEADER)
            ->assertForbidden();
    }


    /**
     * Test a '201 Created' is returned along with the post, user who made the post and related tags, when an
     * authenticated editor user attempts to create a new post using a valid request format
     */
    public function testAuthenticatedEditorUserCanPostValidPost(): void
    {
        $user = factory(User::class)->create()->assignRole('editor');
        $tag = factory(Tag::class)->create();
        $request = [
            'title' => 'Valid title',
            'body'  => 'Valid body',
            'tags'  => [$tag['id']]
        ];

        $post = $this->actingAs($user, 'api')->post(self::URL_PREFIX . 'posts', $request, self::HEADER);

        $post
            ->assertStatus(201)
            ->assertJson([
                'id'         => $post['id'],
                'title'      => $request['title'],
                'body'       => $request['body'],
                'created_at' => $post['created_at'],
                'updated_at' => $post['updated_at'],
                'author' => [
                    'id'          => $user['id'],
                    'displayname' => $user['displayname'],
                ],
                'tags' => [
                    [
                        'id'    => $tag['id'],
                        'title' => $tag['title'],
                    ],
                ],
            ]);

        $this->assertDatabaseHas('posts', [
            'id'      => $post['id'],
            'title'   => $request['title'],
            'body'    => $request['body'],
            'user_id' => $user['id'],
        ]);

        $this->assertDatabaseHas('post_tags', [
            'post_id' => $post['id'],
            'tag_id'  => $tag['id'],
        ]);
    }


    /**
     * Test a '201 Created' is returned along with the post, user who made the post and related tags, when an
     * authenticated admin user attempts to create a new post using a valid request format
     */
    public function testAuthenticatedAdminUserCanPostValidPost(): void
    {
        $user = factory(User::class)->create()->assignRole('admin');
        $tag = factory(Tag::class)->create();
        $request = [
            'title' => 'Valid title',
            'body'  => 'Valid body',
            'tags'  => [$tag['id']]
        ];

        $post = $this->actingAs($user, 'api')->post(self::URL_PREFIX . 'posts', $request, self::HEADER);

        $post
            ->assertStatus(201)
            ->assertJson([
                'id'         => $post['id'],
                'title'      => $request['title'],
                'body'       => $request['body'],
                'created_at' => $post['created_at'],
                'updated_at' => $post['updated_at'],
                'author' => [
                    'id'          => $user['id'],
                    'displayname' => $user['displayname'],
                ],
                'tags' => [
                    [
                        'id'    => $tag['id'],
                        'title' => $tag['title'],
                    ],
                ],
            ]);

        $this->assertDatabaseHas('posts', [
            'id'      => $post['id'],
            'title'   => $request['title'],
            'body'    => $request['body'],
            'user_id' => $user['id'],
        ]);

        $this->assertDatabaseHas('post_tags', [
            'post_id' => $post['id'],
            'tag_id'  => $tag['id'],
        ]);
    }


    /**
     * Test a '422 Unprocessable Entity' is returned when an authenticated admin user attempts to create a post using an
     * invalid request format
     *
     * @dataProvider  \Tests\Feature\Http\Controllers\PostController\DataProvider::postPostPutInputValidation
     * @param string $input
     * @param string $inputValue
     * @param array $validationError
     */
    public function testAuthenticatedAdminUserCannotPostInvalidPost(string $input, $inputValue, array $validationErrors): void
    {
        $user = factory(User::class)->create();

        $this->actingAs($user, 'api')->post(self::URL_PREFIX . 'posts', [$input => $inputValue], self::HEADER)
            ->assertStatus(422)
            ->assertJsonValidationErrors($validationErrors);
    }
}
