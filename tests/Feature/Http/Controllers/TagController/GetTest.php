<?php

namespace Tests\Feature\Http\Controllers\TagController;

use App\Tag;
use App\Post;
use App\User;
use App\PostTag;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a '200 Ok' is returned along with the tags when attempting to get all existing tags
     */
    public function testCanGetAllExistingTags(): void
    {
        factory(User::class)->create();
        $tagOne = factory(Tag::class)->create([
            'title' => 'aaa',
            'color'  => '#FFFFFF',
        ]);

        $tagTwo = factory(Tag::class)->create([
            'title' => 'bbb',
            'color'  => '#FFFFFF',
        ]);

        $this->get(self::URL_PREFIX . 'tags', self::HEADER)
            ->assertStatus(200)
            ->assertJson([
                [
                    'id'    => $tagOne['id'],
                    'title' => $tagOne['title'],
                    'color' => $tagOne['color'],
                ],
                [
                    'id'    => $tagTwo['id'],
                    'title' => $tagTwo['title'],
                    'color' => $tagTwo['color'],
                ],
            ]);
    }


    /**
     * Test a '200 Ok' is returned along with the tags when attempting to get all existing tags
     */
    public function testCanGetAllExistingTagsAsEmptyArrayWhenNoneExist(): void
    {
        factory(User::class)->create();

        $this->get(self::URL_PREFIX . 'tags', self::HEADER)
            ->assertStatus(200)
            ->assertJson([]);
    }


    /**
     * Test a '200 Ok' is returned along with the tag when attempting to get an existing tag
     */
    public function testCanGetExistingTag(): void
    {
        factory(User::class)->create();
        $tag = factory(Tag::class)->create();

        $this->get(self::URL_PREFIX . 'tags/' . $tag['id'], self::HEADER)
            ->assertStatus(200)
            ->assertJson([
                'id'    => $tag['id'],
                'title' => $tag['title'],
                'color' => $tag['color'],
            ]);
    }


    /**
     * Test a '404 Not Found' is returned when attempting to get a nonexistent tag
     */
    public function testCannotGetNonexistentTag(): void
    {
        $this->get(self::URL_PREFIX . 'tags/1', self::HEADER)
            ->assertNotFound();
    }
    

    /**
     * Test a '200 Ok' is returned along with the tag and posts related to it, when an authenticated user attempts to
     * get an existing tag with related posts
     */
    public function testAuthenticatedUserCanGetExistingTagWithPosts(): void
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


        $this->get(self::URL_PREFIX . 'tags/' . $tag['id'] . '/posts', self::HEADER)
            ->assertStatus(200)
            ->assertJson([
                'id'    => $tag['id'],
                'title' => $tag['title'],
                'posts' => [
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
                    ],
                ],
            ]);
    }


    /**
     * Test a '404 Not Found' is returned when attempting to get a nonexistent tag with posts
     */
    public function testGuestCannotGetNonexistentTagWithPosts(): void
    {
        $this->get(self::URL_PREFIX . 'tags/1/posts', self::HEADER)
            ->assertNotFound();
    }
}
