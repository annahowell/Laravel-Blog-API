<?php

namespace App\Http\Requests;

use App\Rules\StrippedLength;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(schema="CommentStoreRequest")
 * {
 *      @OA\Property(
 *          property="body",
 *          type="string",
 *          description="Body of the post"
 *      ),
 *      @OA\Property(
 *          property="post_id",
 *          type="integer",
 *          format="int64",
 *          description="post id the comment belongs to"
 *      )
 * }
 */
class CommentStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'body'    => ['required', 'min:2', new StrippedLength(1000)],
            'post_id' => 'required|integer',
        ];
    }
}
