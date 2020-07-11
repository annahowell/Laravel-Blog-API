<?php

namespace Tests\Feature\Http\Controllers\PostController;

use App\Tag;
use App\Post;
use App\User;
use App\Comment;
use App\PostTag;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a '200 Ok' is returned along with the posts, the user that made the post and related tags, when attempting
     * to get all existing posts
     */
    public function testCanGetAllExistingPosts(): void
    {
        $user = factory(User::class)->create();
        $tag = factory(Tag::class)->create();
        $postOne = factory(Post::class)->create();
        $postTwo = factory(Post::class)->create();

        factory(PostTag::class)->create([
            'post_id' => $postOne['id'],
            'tag_id'  => $tag['id'],
        ]);

        factory(PostTag::class)->create([
            'post_id' => $postTwo['id'],
            'tag_id'  => $tag['id'],
        ]);

        $this->get(self::URL_PREFIX . 'posts', self::HEADER)
            ->assertStatus(200)
            ->assertJson([
            [
                'id'         => $postOne['id'],
                'title'      => $postOne['title'],
                'body'       => $postOne['body'],
                'created_at' => $postOne['created_at'],
                'updated_at' => $postOne['updated_at'],
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
            ],
            [
                'id'         => $postTwo['id'],
                'title'      => $postTwo['title'],
                'body'       => $postTwo['body'],
                'created_at' => $postTwo['created_at'],
                'updated_at' => $postTwo['updated_at'],
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
            ]
        ]);
    }



    /**
     * Test a '200 Ok' is returned along with the posts, the user that made the post and related tags, when attempting
     * to get all existing posts
     */
    public function testCanGetAllExistingPostsAsEmptyArrayWhenNoneExist(): void
    {
        $this->get(self::URL_PREFIX . 'posts', self::HEADER)
            ->assertStatus(200)
            ->assertJson([]);
    }



    /**
     * Test a '200 Ok' is returned along with the post, the user that made the post and related tags, when attempting
     * to get all existing posts
     */
    public function testCanGetExistingPost(): void
    {
        $user = factory(User::class)->create();
        $tag = factory(Tag::class)->create();
        $post = factory(Post::class)->create();
        factory(PostTag::class)->create();

        $this->get(self::URL_PREFIX . 'posts/' . $post['id'], self::HEADER)
            ->assertStatus(200)
            ->assertJson([
                'id'         => $post['id'],
                'title'      => $post['title'],
                'body'       => $post['body'],
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
    }



    /**
     * Test a '404 Not Found' is returned when attempting to get a nonexistent post
     */
    public function testCannotGetNonexistentPost(): void
    {
        $this->get(self::URL_PREFIX . 'posts/1', self::HEADER)
            ->assertNotFound();
    }



    /**
     * Test a '200 Ok' is returned along with the post, the user that made the post, related tags, related comments and
     * the user that made the comment, when attempting to get an existing post with comments
     */
    public function testCanGetExistingPostWithComments(): void
    {
        $user = factory(User::class)->create();
        $tag = factory(Tag::class)->create();
        $post = factory(Post::class)->create();
        $comment = factory(Comment::class)->create();
        factory(PostTag::class)->create();

        $this->get(self::URL_PREFIX . 'posts/' . $post['id'] . '/comments', self::HEADER)
            ->assertStatus(200)
            ->assertJson([
                'id'         => $post['id'],
                'title'      => $post['title'],
                'body'       => $post['body'],
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
                'comments' => [
                    [
                        'id'         => $comment['id'],
                        'body'       => $comment['body'],
                        'created_at' => $comment['created_at'],
                        'updated_at' => $comment['updated_at'],
                        'author' => [
                            'id'          => $user['id'],
                            'displayname' => $user['displayname'],
                        ]
                    ],
                ],
            ]);
    }



    /**
     * Test a '404 Not Found' is returned when attempting to get a nonexistent post with comments
     */
    public function testCannotGetNonexistentPostWithComments(): void
    {
        $this->get(self::URL_PREFIX . 'posts/1/comments', self::HEADER)
            ->assertNotFound();
    }
}
