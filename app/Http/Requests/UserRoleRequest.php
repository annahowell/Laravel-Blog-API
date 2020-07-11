<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

/**
 * @OA\Schema(schema="UserRoleRequest")
 * {
 *      @OA\Property(
 *          property="displayname",
 *          type="string",
 *          description="Optional new display name for the user"
 *      ),
 *      @OA\Property(
 *          property="email",
 *          type="string",
 *          description="Optional new email"
 *      ),
 *      @OA\Property(
 *          property="password",
 *          type="string",
 *          description="Optional new password, must be minumum of 10 characters with one upper and lower case character, one number and one special character"
 *      ),
 *      @OA\Property(
 *          property="password_confirmation",
 *          type="string",
 *          description="Password confirmation, must match password, required if password is to be amended"
 *      ),
 *      @OA\Property(
 *          property="roles",
 *          type="array",
 *          description="Array of role ids assigned to the user",
 *          type="array",
 *          @OA\Items(
 *              type="integer",
 *          )
 *      )
 * }
 */
class UserRoleRequest extends FormRequest
{
    private $adminRoleId;
    private $noOfAdmins;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $this->adminRoleId = Role::findByName('admin', 'web')->id;
        $this->noOfAdmins = User::role($this->adminRoleId)->get()->count();

        return [
            'displayname' => 'string|min:2|max:64|unique:users',
            'email'       => 'string|email|unique:users',
            'password'    => [
                'string',
                'confirmed',
                'min:10',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[*.!@#$%^&(){}[\]:;<>,.?\~_+\-=\|]/',
            ],
            'enabled' => [
                function ($attribute, $value, $fail) {
                    // If there's one admin user, and we're editing that user, and attempting to disable the account
                    if ($this->noOfAdmins == 1 && $this->user->id == Auth::id() && $value === false) {
                        $fail('As the only admin you may not disable your account.');
                    }
                }
            ],
            'roles' => [
                'array',
                'exists:roles,id',
                function ($attribute, $value, $fail) {
                    // If there's one admin user, and we're editing that user, and the admin role id isn't in the updated list
                    if ($this->noOfAdmins == 1 && $this->user->id == Auth::id() && !in_array($this->adminRoleId, $value)) {
                        $fail('As the only admin you may not remove your admin role.');
                    }
                }
            ],
            'roles.*' => 'integer',
        ];
    }
}
