<?php

namespace Tests\Feature\Http\Controllers\TagController;

use App\Tag;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a '401 Unauthorized' is returned when a guest user attempts to update an existing tag
     */
    public function testGuestCannotPutTag(): void
    {
        factory(User::class)->create();
        $existingTag = factory(Tag::class)->create();

        $this->put(self::URL_PREFIX . 'tags/' . $existingTag['id'], [], self::HEADER)
            ->assertUnauthorized();
    }



    /**
     * Test a '403 Forbidden' is returned when a guest user attempts to update an existing tag
     */
    public function testAuthenticatedCommenterUserCannotPutValidTag(): void
    {
        $user = factory(User::class)->create()->assignRole('commenter');
        $existingTag = factory(Tag::class)->create();

        $request = [
            'title' => 'Valid title',
            'color' => '#FF00FF',
        ];

        $this->actingAs($user, 'api')->put(self::URL_PREFIX . 'tags/' . $existingTag['id'], $request, self::HEADER)
            ->assertForbidden();
    }



    /**
     * Test a '403 Forbidden' is returned when a guest user attempts to update an existing tag
     */
    public function testAuthenticatedEditorUserCannotPutValidTag(): void
    {
        $user = factory(User::class)->create()->assignRole('editor');
        $existingTag = factory(Tag::class)->create();

        $request = [
            'title' => 'Valid title',
            'color' => '#FF00FF',
        ];

        $this->actingAs($user, 'api')->put(self::URL_PREFIX . 'tags/' . $existingTag['id'], $request, self::HEADER)
            ->assertForbidden();
    }



    /**
     * Test a '200 Ok' is returned along with the tag, when an authenticated admin user attempts to update tag using
     * a valid request format
     */
    public function testAuthenticatedAdminUserCanPutValidTag(): void
    {
        $user = factory(User::class)->create()->assignRole('admin');
        $existingTag = factory(Tag::class)->create();

        $request = [
            'title' => 'New Valid title content',
            'color' => '#FF00FF',
        ];

        $tag = $this->actingAs($user, 'api')->put(self::URL_PREFIX . 'tags/' . $existingTag['id'], $request, self::HEADER);

        $tag
            ->assertStatus(200)
            ->assertJson([
                'id'    => $existingTag['id'],
                'title' => $request['title'],
                'color' => $request['color'],
            ]);

        $this->assertDatabaseHas('tags', [
            'id'    => $existingTag['id'],
            'title' => $tag['title'],
            'color' => $tag['color'],
        ]);

        $this->assertDatabaseMissing('tags', [
            'id'   => $existingTag['id'],
            'title' => $existingTag['title'],
            'color' => $existingTag['color'],
        ]);
    }



    /**
     * Test a '404 Not Found' is returned when an authenticated admin user attempts to update a nonexistent tag
     */
    public function testAuthenticatedAdminUserCannotPutNonexistentPost(): void
    {
        $user = factory(User::class)->create()->assignRole('admin');

        $this->actingAs($user, 'api')->put(self::URL_PREFIX . 'tags/1', [], self::HEADER)
            ->assertNotFound();
    }



    /**
     * Test a '422 Unprocessable Entity' is returned when an authenticated admin user attempts to update a tag using an
     * invalid request format
     *
     * @dataProvider  \Tests\Feature\Http\Controllers\TagController\DataProvider::tagPutInputValidation
     * @param string $input
     * @param string $inputValue
     * @param string $validationError
     */
    public function testAuthenticatedAdminUserCannotPutTheirOwnInvalidTag(string $input, string $inputValue, array $validationErrors): void
    {
        $user = factory(User::class)->create();
        $existingTag = factory(Tag::class)->create();

        $this->actingAs($user, 'api')->put(self::URL_PREFIX . 'tags/' . $existingTag['id'], [$input => $inputValue], self::HEADER)
            ->assertStatus(422)
            ->assertJsonValidationErrors($validationErrors);
    }
}
