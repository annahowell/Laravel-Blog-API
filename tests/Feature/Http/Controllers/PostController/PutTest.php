<?php

namespace Tests\Feature\Http\Controllers\PostController;

use App\PostTag;
use App\Tag;
use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a '401 Unauthorized' is returned when a guest user attempts to update an existing post
     */
    public function testGuestCannotPutExistingPost(): void
    {
        factory(User::class)->create();
        $existingPost = factory(Post::class)->create();

        $this->put(self::URL_PREFIX . 'posts/' . $existingPost['id'], [], self::HEADER)
            ->assertUnauthorized();
    }



    /**
     * Test a '403 Forbidden' is returned when an authenticated commenter user attempts to update an existing post
     */
    public function testAuthenticatedCommenterUserCannotPutPost(): void
    {
        factory(User::class)->create();
        $existingPost = factory(Post::class)->create();
        $tag = factory(Tag::class)->create();
        $user = factory(User::class)->create()->assignRole('commenter');

        $request = [
            'title' => 'Valid title',
            'body'  => 'Valid body',
            'tags'  => [$tag['id']]
        ];

        $this->actingAs($user, 'api')->put(self::URL_PREFIX . 'posts/' . $existingPost['id'], $request, self::HEADER)
            ->assertForbidden();
    }



    /**
     * Test a '200 Ok' is returned along with the post, user who made the post and related tags, when an
     * authenticated editor user attempts to update their own existing post using a valid request format
     */
    public function testAuthenticatedEditorUserCanPutTheirOwnValidPost(): void
    {
        $user = factory(User::class)->create()->assignRole('editor');
        $existingTag = factory(Tag::class)->create();
        $existingPost = factory(Post::class)->create();
        $tag = factory(Tag::class)->create();

        $request = [
            'title' => 'Valid title',
            'body'  => 'Valid body',
            'tags'  => [$tag['id']]
        ];

        $post = $this->actingAs($user, 'api')->put(self::URL_PREFIX . 'posts/' . $existingPost['id'], $request, self::HEADER);

        $post
            ->assertStatus(200)
            ->assertJson([
                'id'         => $existingPost['id'],
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
                        'color' => $tag['color'],
                    ],
                ],
                'comment_count' => 0,
            ]);

        $this->assertDatabaseHas('posts', [
            'id' => $existingPost['id'],
            'title' => $request['title'],
            'body' => $request['body'],
        ]);

        $this->assertDatabaseMissing('posts', [
            'id'    => $existingPost['id'],
            'title' => $existingPost['title'],
            'body'  => $existingPost['body'],
        ]);

        $this->assertDatabaseHas('post_tags', [
            'post_id' => $existingPost['id'],
            'tag_id'  => $tag['id'],
        ]);

        $this->assertDatabaseMissing('post_tags', [
            'post_id' => $existingPost['id'],
            'tag_id'  => $existingTag['id'],
        ]);
    }



    /**
     * Test a '403 Forbidden' is returned when an authenticated editor user attempts to update an existing post made by
     * another user
     */
    public function testAuthenticatedEditorUserCannotPutAnotherUsersExistingPost(): void
    {
        factory(User::class)->create();
        $existingTag = factory(Tag::class)->create();
        $existingPost = factory(Post::class)->create();

        factory(PostTag::class)->create([
            'post_id' => $existingPost['id'],
            'tag_id'  => $existingTag['id'],
        ]);

        $tag = factory(Tag::class)->create();
        $user = factory(User::class)->create()->assignRole('editor');

        $request = [
            'title' => 'Valid title',
            'body'  => 'Valid body',
            'tags'  => [$tag['id']]
        ];

        $this->actingAs($user, 'api')->put(self::URL_PREFIX . 'posts/' . $existingPost['id'], $request, self::HEADER)
            ->assertForbidden();

        $this->assertDatabaseHas('posts', [
            'id' => $existingPost['id'],
            'title' => $existingPost['title'],
            'body' => $existingPost['body'],
        ]);

        $this->assertDatabaseHas('post_tags', [
            'post_id' => $existingPost['id'],
            'tag_id'  => $existingTag['id'],
        ]);

        $this->assertDatabaseMissing('post_tags', [
            'post_id' => $existingPost['id'],
            'tag_id'  => $tag['id'],
        ]);
    }



    /**
     * Test a '200 Ok' is returned along with the post, user who made the post and related tags, when an
     * authenticated admin user attempts to update another users existing post using a valid request format
     */
    public function testAuthenticatedAdminUserCanPutAnotherUsersExistingPost(): void
    {
        $exitingUser = factory(User::class)->create();
        $existingTag = factory(Tag::class)->create();
        $existingPost = factory(Post::class)->create();

        factory(PostTag::class)->create([
            'post_id' => $existingPost['id'],
            'tag_id'  => $existingTag['id'],
        ]);

        $tag = factory(Tag::class)->create();
        $user = factory(User::class)->create()->assignRole('admin');

        $request = [
            'title' => 'Valid title',
            'body'  => 'Valid body',
            'tags'  => [$tag['id']]
        ];

        $post = $this->actingAs($user, 'api')->put(self::URL_PREFIX . 'posts/' . $existingPost['id'], $request, self::HEADER);

        $post
            ->assertStatus(200)
            ->assertJson([
                'id'         => $existingPost['id'],
                'title'      => $request['title'],
                'body'       => $request['body'],
                'created_at' => $post['created_at'],
                'updated_at' => $post['updated_at'],
                'author' => [
                    'id'          => $exitingUser['id'],
                    'displayname' => $exitingUser['displayname'],
                ],
                'tags' => [
                    [
                        'id'    => $tag['id'],
                        'title' => $tag['title'],
                        'color' => $tag['color'],
                    ],
                ],
                'comment_count' => 0,
            ]);

        $this->assertDatabaseHas('posts', [
            'id' => $existingPost['id'],
            'title' => $request['title'],
            'body' => $request['body'],
        ]);

        $this->assertDatabaseMissing('posts', [
            'id'    => $existingPost['id'],
            'title' => $existingPost['title'],
            'body'  => $existingPost['body'],
        ]);

        $this->assertDatabaseHas('post_tags', [
            'post_id' => $existingPost['id'],
            'tag_id'  => $tag['id'],
        ]);

        $this->assertDatabaseMissing('post_tags', [
            'post_id' => $existingPost['id'],
            'tag_id'  => $existingTag['id'],
        ]);
    }



    /**
     * Test a '404 Not Found' is returned when an authenticated admin user attempts to update a nonexistent post
     */
    public function testAuthenticatedAdminUserCannotPutNonexistentPost(): void
    {
        $user = factory(User::class)->create()->assignRole('admin');

        $this->actingAs($user, 'api')->put(self::URL_PREFIX . 'posts/1', [], self::HEADER)
            ->assertNotFound();
    }



    /**
     * Test a '422 Unprocessable Entity' is returned when an authenticated admin user attempts to update an existing
     * post using an invalid request format
     *
     * @dataProvider  \Tests\Feature\Http\Controllers\PostController\DataProvider::postPostPutInputValidation
     * @param string $input
     * @param string $inputValue
     * @param array $validationError
     */
    public function testAuthenticatedAdminUserCannotPutInvalidPost(string $input, $inputValue, array $validationErrors): void
    {
        $user = factory(User::class)->create()->assignRole('admin');
        $existingPost = factory(Post::class)->create();

        $this->actingAs($user, 'api')->put(self::URL_PREFIX . 'posts/' . $existingPost['id'], [$input => $inputValue], self::HEADER)
            ->assertStatus(422)
            ->assertJsonValidationErrors($validationErrors);
    }
}
