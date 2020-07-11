<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(schema="PostCommentResource")
 * {
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          format="int64",
 *          description="Comment id"
 *      ),
 *      @OA\Property(
 *          property="body",
 *          type="string",
 *          description="Body of the comment"
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          type="string",
 *          description="Date and time the comment was created"
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          type="string",
 *          description="Date and time the comment was last updated"
 *      ),
 *      @OA\Property(
 *          property="author",
 *          type="object",
 *          description="User who made the comment",
 *          ref="#/components/schemas/UserResource",
 *      ),
 * }
 */
class PostCommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'body'       => $this->body,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'author'     => new UserResource($this->user),
        ];
    }
}
