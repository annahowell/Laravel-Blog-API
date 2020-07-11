<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(schema="PostWithCommentsResource")
 * {
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          format="int64",
 *          description="Post id"
 *      ),
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
 *          property="created_at",
 *          type="string",
 *          description="Date and time the post was created"
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          type="string",
 *          description="Date and time the post was last updated"
 *      ),
 *      @OA\Property(
 *          property="author",
 *          type="object",
 *          description="User who created the post",
 *          ref="#/components/schemas/UserResource",
 *      ),
 *      @OA\Property(
 *          property="tags",
 *          type="array",
 *          description="Tags related to this post",
 *          @OA\Items(
 *              ref="#/components/schemas/TagResource",
 *          )
 *      ),
 *      @OA\Property(
 *          property="comments",
 *          type="array",
 *          description="Comments on this post",
 *          @OA\Items(
 *              ref="#/components/schemas/CommentResource",
 *          )
 *      ),
 * }
 */
class PostWithCommentsResource extends JsonResource
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
            'title'      => $this->title,
            'body'       => $this->body,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'author'     => new UserResource($this->user),
            'tags'       => TagResource::collection($this->tags->sortBy('title')),
            'comments'   => CommentResource::collection($this->comments->sortBy('created_at')),
        ];
    }
}
