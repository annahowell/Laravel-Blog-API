<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(schema="PostRequest")
 * {
 *      @OA\Property(
 *          property="title",
 *          type="string",
 *          description="Post title"
 *      ),
 *      @OA\Property(
 *          property="body",
 *          type="string",
 *          description="Body of the post"
 *      ),
 *      @OA\Property(
 *          property="tags",
 *          type="array",
 *          description="Array of tag ids related to this post",
 *          type="array",
 *          @OA\Items(
 *              type="integer",
 *          )
 *      )
 * }
 */
class PostRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'  => 'required|min:8|max:255',
            'body'   => 'required',
            'tags'   => 'array|exists:App\Tag,id',
            'tags.*' => 'integer',
        ];
    }
}
