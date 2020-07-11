<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(schema="UserLoginRequest")
 * {
 *      @OA\Property(
 *          property="email",
 *          type="string",
 *          description="User's email"
 *      ),
 *      @OA\Property(
 *          property="password",
 *          type="string",
 *          description="User's password"
 *      ),
 *      @OA\Property(
 *          property="remember_me",
 *          type="boolean",
 *          description="Whether or not to remember the user for 30 days instead of a single day"
 *      ),
 * }
 */
class UserLoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email'       => 'required|string|email',
            'password'    => 'required|string',
            'remember_me' => 'boolean'
        ];
    }
}
