<?php

namespace Tests\Feature\Http\Controllers\CommentController;

use App\Post;
use App\User;
use App\Comment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a '200 Ok' is returned along with the comment, post id and user who made the comment, when attempting to get
     * an existing comment
     */
    public function testCanGetExistingComment(): void
    {
        $user = factory(User::class)->create();
        factory(Post::class)->create();
        $comment = factory(Comment::class)->create();

        $this->get(self::URL_PREFIX . 'comments/' . $comment['id'], self::HEADER)
            ->assertStatus(200)
            ->assertJson([
                'id'         => $comment['id'],
                'body'       => $comment['body'],
                'post_id'    => $comment['post_id'],
                'created_at' => $comment['created_at'],
                'updated_at' => $comment['updated_at'],
                'author' => [
                    'id'          => $user['id'],
                    'displayname' => $user['displayname'],
                ],
            ]);
    }


    /**
     * Test a '404 Not Found' is returned when attempting to get a nonexistent comment
     */
    public function testCannotGetNonexistentComment(): void
    {
        $this->get(self::URL_PREFIX . 'comments/1', self::HEADER)
            ->assertNotFound();
    }
}
