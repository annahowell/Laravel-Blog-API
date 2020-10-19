<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(schema="UserRequest")
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
 *      )
 * }
 */
class UserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
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
        ];
    }
}
