<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(schema="TagRequest")
 * {
 *      @OA\Property(
 *          property="title",
 *          type="string",
 *          description="Tag title, max 32 characters"
 *      ),
 *      @OA\Property(
 *          property="color",
 *          type="string",
 *          description="Color of the tag in full rgb format including the leading # e.g: #FF00FF"
 *      )
 * }
 */
class TagRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'title' => [
                'string',
                'min:2',
                'max:32',
                'unique:tags',
                $this->method() === 'POST' ? 'required' : null,
            ],
            'color' => [
                $this->method() === 'POST' ? 'required' : null,
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'
            ]
        ];
    }
}
