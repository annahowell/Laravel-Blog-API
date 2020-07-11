<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(schema="UserSignupRequest")
 * {
 *      @OA\Property(
 *          property="displayname",
 *          type="string",
 *          description="Desired display name for the user"
 *      ),
 *      @OA\Property(
 *          property="email",
 *          type="string",
 *          description="User's email"
 *      ),
 *      @OA\Property(
 *          property="password",
 *          type="string",
 *          description="User's password, must be minumum of 10 characters with one upper and lower case character, one
            number and one special character"
 *      ),
 *      @OA\Property(
 *          property="password_confirmation",
 *          type="string",
 *          description="User's password confirmation, must match password"
 *      )
 * }
 */
class UserSignupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'displayname' => 'required|string|min:2|max:64|unique:users',
            'email'       => 'required|string|email|unique:users',
            'password'    => [
                'required',
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
