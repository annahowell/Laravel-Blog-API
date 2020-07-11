<?php

namespace App\Http\Requests;

use App\Rules\StrippedLength;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(schema="CommentUpdateRequest")
 * {
 *      @OA\Property(
 *          property="body",
 *          type="string",
 *          description="Body of the post"
 *      )
 * }
 */
class CommentUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'min:2', new StrippedLength(1000)]
        ];
    }
}
