<?php

namespace Tests\Feature\Http\Controllers\TagController;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a '401 Unauthorized' is returned when a guest user attempts to create a new tag
     */
    public function testGuestCannotPostTag(): void
    {
        $this->post(self::URL_PREFIX . 'tags', [], self::HEADER)
            ->assertUnauthorized();
    }


    /**
     * Test a '403 Forbidden' is returned when an authenticated commenter user attempts to create a new tag
     */
    public function testAuthenticatedCommenterUserCannotPostValidTag(): void
    {
        $user = factory(User::class)->create()->assignRole('commenter');

        $request = [
            'title' => 'Valid title',
            'color' => '#FF00FF',
        ];

        $this->actingAs($user, 'api')->post(self::URL_PREFIX . 'tags', $request, self::HEADER)
            ->assertForbidden();
    }


    /**
     * Test a '403 Forbidden' is returned when an authenticated editor user attempts to create a new tag
     */
    public function testAuthenticatedEditorUserCannotPostValidTag(): void
    {
        $user = factory(User::class)->create()->assignRole('editor');

        $request = [
            'title' => 'Valid title',
            'color' => '#FF00FF',
        ];

        $this->actingAs($user, 'api')->post(self::URL_PREFIX . 'tags', $request, self::HEADER)
            ->assertForbidden();
    }


    /**
     * Test a '201 Created' is returned along with the tag, when an authenticated admin user attempts to create a new
     * tag using a valid request format
     */
    public function testAuthenticatedAdminUserCanPostValidTag(): void
    {
        $user = factory(User::class)->create()->assignRole('admin');
        $request = [
            'title' => 'Valid title',
            'color' => '#FF00FF',
        ];

        $tag = $this->actingAs($user, 'api')->post(self::URL_PREFIX . 'tags', $request, self::HEADER);

        $tag
            ->assertStatus(201)
            ->assertJson([
                'id'    => $tag['id'],
                'title' => $request['title'],
                'color' => $request['color'],
            ]);

        $this->assertDatabaseHas('tags', [
            'id'    => $tag['id'],
            'title' => $request['title'],
            'color' => $request['color'],
        ]);
    }


    /**
     * Test a '422 Unprocessable Entity' is returned when an authenticated admin user attempts to create a tag using an
     * invalid request format
     *
     * @dataProvider  \Tests\Feature\Http\Controllers\TagController\DataProvider::tagPostInputValidation
     * @param string $input
     * @param string $inputValue
     * @param string $validationError
     */
    public function testAuthenticatedAdminUserCannotPostInvalidTag(string $input, string $inputValue): void
    {
        $user = factory(User::class)->create()->assignRole('admin');

        $this->actingAs($user, 'api')->post(self::URL_PREFIX . 'tags', [$input => $inputValue], self::HEADER)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'color']);
    }
}
